<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialMediaContentExtractor
{
    /**
     * User-Agent yang ramah untuk mengekstrak OpenGraph preview media sosial & web.
     */
    protected array $userAgents = [
        'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
        'Twitterbot/1.0',
        'WhatsApp/2.21.12.21 A',
        'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    ];

    /**
     * Ekstrak judul, caption/konten, dan gambar preview dari URL.
     */
    public function extract(string $url): array
    {
        $url = trim($url);

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return [
                'success' => false,
                'message' => 'Format URL tidak valid. Pastikan menyertakan https://',
            ];
        }

        $platform = $this->detectPlatform($url);
        $html = $this->fetchHtml($url);

        if ($html === null || trim($html) === '') {
            return [
                'success' => false,
                'platform' => $platform,
                'source_url' => $url,
                'message' => 'Tidak dapat mengakses konten dari tautan tersebut (server tujuan menolak atau waktu habis).',
            ];
        }

        $metadata = $this->parseMetadata($html, $platform);

        if (empty($metadata['caption']) && empty($metadata['title'])) {
            return [
                'success' => false,
                'platform' => $platform,
                'source_url' => $url,
                'message' => 'Tidak ada teks atau caption yang dapat diekstrak secara otomatis dari URL ini. Anda dapat menempelkan caption secara manual.',
            ];
        }

        return [
            'success' => true,
            'platform' => $platform,
            'source_url' => $url,
            'title' => $metadata['title'],
            'caption' => $metadata['caption'],
            'image_url' => $metadata['image_url'],
            'author' => $metadata['author'],
        ];
    }

    /**
     * Identifikasi platform dari URL.
     */
    public function detectPlatform(string $url): string
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');

        if (str_contains($host, 'instagram.com')) {
            return 'Instagram';
        }
        if (str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be')) {
            return 'YouTube';
        }
        if (str_contains($host, 'tiktok.com')) {
            return 'TikTok';
        }
        if (str_contains($host, 'facebook.com') || str_contains($host, 'fb.watch')) {
            return 'Facebook';
        }
        if (str_contains($host, 'twitter.com') || str_contains($host, 'x.com')) {
            return 'X (Twitter)';
        }

        return 'Website';
    }

    /**
     * Mengambil HTML dengan mencoba beberapa User-Agent bot preview.
     */
    protected function fetchHtml(string $url): ?string
    {
        foreach ($this->userAgents as $ua) {
            try {
                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'User-Agent' => $ua,
                        'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    ])
                    ->timeout(12)
                    ->get($url);

                if ($response->successful() && strlen($response->body()) > 200) {
                    return $response->body();
                }
            } catch (\Throwable $e) {
                Log::debug('SocialMediaContentExtractor fetch error with UA '.$ua.': '.$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Parsing tag meta OpenGraph dan tag HTML lainnya.
     */
    protected function parseMetadata(string $html, string $platform): array
    {
        $ogTitle = $this->getMetaContent($html, 'og:title') ?: $this->getHtmlTitle($html);
        $ogDesc = $this->getMetaContent($html, 'og:description') ?: $this->getMetaContent($html, 'description');
        $ogImage = $this->getMetaContent($html, 'og:image');
        $author = $this->getMetaContent($html, 'og:site_name') ?: $this->getMetaContent($html, 'author');

        $caption = '';
        $title = '';

        if ($platform === 'Instagram') {
            $extracted = $this->extractInstagramContent($ogTitle, $ogDesc);
            $caption = $extracted['caption'];
            $title = $extracted['title'];
            if (! empty($extracted['account'])) {
                $author = $extracted['account'];
            }
        } elseif ($platform === 'YouTube') {
            $title = $ogTitle;
            $caption = $ogDesc;
        } elseif ($platform === 'TikTok') {
            $title = $ogTitle;
            $caption = $ogDesc ?: $ogTitle;
        } else {
            // General Web / Article
            $title = $ogTitle;
            $caption = $ogDesc;

            // Jika deskripsi web pendek, coba ambil teks paragraf dari artikel
            if (strlen($caption) < 150) {
                $bodyText = $this->extractArticleText($html);
                if (strlen($bodyText) > strlen($caption)) {
                    $caption = $bodyText;
                }
            }
        }

        return [
            'title' => trim(strip_tags((string) $title)),
            'caption' => trim((string) $caption),
            'image_url' => $ogImage ? trim($ogImage) : null,
            'author' => $author ? trim(strip_tags((string) $author)) : null,
        ];
    }

    /**
     * Ekstrak konten bersih dari format og:title / og:description Instagram.
     */
    protected function extractInstagramContent(?string $ogTitle, ?string $ogDesc): array
    {
        $ogTitle = $ogTitle ? html_entity_decode($ogTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';
        $ogDesc = $ogDesc ? html_entity_decode($ogDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';

        $caption = '';
        $account = '';

        // Deteksi nama akun dari "Username di Instagram: ..."
        if (preg_match('/^(.*?)\s+(?:di|on)\s+Instagram/i', $ogTitle, $accMatch)) {
            $account = trim($accMatch[1]);
        }

        // Coba ekstrak teks dalam tanda petik pada og:title
        if (preg_match('/:\s*["“](.*?)["”]\s*$/s', $ogTitle, $match)) {
            $caption = trim($match[1]);
        } elseif (preg_match('/:\s*["“](.*?)["”]\s*$/s', $ogDesc, $match)) {
            // Coba dari og:description
            $caption = trim($match[1]);
        }

        // Jika tidak ada tanda petik, gunakan string terpanjang
        if (empty($caption)) {
            $cleanTitle = preg_replace('/^(.*?)\s+(?:di|on)\s+Instagram:\s*/i', '', $ogTitle);
            $cleanDesc = preg_replace('/^.*?:\s*/s', '', $ogDesc);
            $caption = strlen($cleanTitle) > strlen($cleanDesc) ? $cleanTitle : $cleanDesc;
        }

        // Ambil baris pertama caption sebagai kandidat judul berita
        $title = '';
        $lines = preg_split('/\R+/', $caption, -1, PREG_SPLIT_NO_EMPTY);
        if (! empty($lines)) {
            $firstLine = trim($lines[0]);
            // Hilangkan hashtag di judul jika ada
            $firstLine = preg_replace('/#\S+/', '', $firstLine);
            $title = Str::limit(trim($firstLine), 120, '');
        }

        if (empty($title)) {
            $title = 'Kabar Terbaru SMK Telkom Lampung';
        }

        return [
            'account' => $account,
            'title' => $title,
            'caption' => $caption,
        ];
    }

    /**
     * Ambil konten tag meta berdasarkan atribut name atau property.
     */
    protected function getMetaContent(string $html, string $nameOrProp): ?string
    {
        $quoted = preg_quote($nameOrProp, '/');

        // Pattern 1: property="..." content="..."
        if (preg_match('/<meta\s+[^>]*(?:property|name)=(["\'])' . $quoted . '\1[^>]*content=(["\'])(.*?)\2/is', $html, $matches)) {
            return html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        // Pattern 2: content="..." property="..."
        if (preg_match('/<meta\s+[^>]*content=(["\'])(.*?)\1[^>]*(?:property|name)=(["\'])' . $quoted . '\3/is', $html, $matches)) {
            return html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return null;
    }

    /**
     * Ambil isi tag <title>.
     */
    protected function getHtmlTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return null;
    }

    /**
     * Ekstrak teks artikel dari halaman web biasa.
     */
    protected function extractArticleText(string $html): string
    {
        // Cari container artikel umum
        $containers = ['article', 'main', '[class*="detail__body"]', '[class*="read__content"]', '[class*="entry-content"]'];

        foreach ($containers as $tag) {
            if (preg_match('/<' . $tag . '[^>]*>(.*?)<\/' . $tag . '>/is', $html, $match)) {
                $text = strip_tags($match[1]);
                $text = preg_replace('/\s+/', ' ', $text);
                if (strlen(trim($text)) > 150) {
                    return Str::limit(trim($text), 3000, '');
                }
            }
        }

        // Fallback: ambil semua tag <p>
        if (preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $matches)) {
            $paragraphs = [];
            foreach ($matches[1] as $p) {
                $cleaned = trim(strip_tags($p));
                if (strlen($cleaned) > 40) {
                    $paragraphs[] = $cleaned;
                }
            }
            if (! empty($paragraphs)) {
                return Str::limit(implode("\n\n", array_slice($paragraphs, 0, 10)), 3000, '');
            }
        }

        return '';
    }
}
