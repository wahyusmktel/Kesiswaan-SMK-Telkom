<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\TeacherProfile;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        // Static Pages
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => now()->startOfDay()->toIso8601String(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];
        $urls[] = [
            'loc' => route('pegawai.index'),
            'lastmod' => now()->startOfDay()->toIso8601String(),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ];
        $urls[] = [
            'loc' => route('public.showcase.index'),
            'lastmod' => now()->startOfDay()->toIso8601String(),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];

        // Published Teacher Profiles
        $teachers = TeacherProfile::where('is_published', true)->get(['slug', 'updated_at']);
        foreach ($teachers as $teacher) {
            $urls[] = [
                'loc' => route('pegawai.show', $teacher->slug),
                'lastmod' => ($teacher->updated_at ?? now())->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.85',
            ];
        }

        // Published Berita (if any)
        if (class_exists(Berita::class)) {
            $articles = Berita::where('is_published', true)->get(['slug', 'updated_at', 'published_at']);
            foreach ($articles as $article) {
                $urls[] = [
                    'loc' => route('berita.show', $article->slug),
                    'lastmod' => ($article->updated_at ?? $article->published_at ?? now())->toIso8601String(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $xml .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $xml .= '<priority>' . $url['priority'] . '</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
