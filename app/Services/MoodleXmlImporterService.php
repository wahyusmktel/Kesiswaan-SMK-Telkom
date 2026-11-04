<?php

namespace App\Services;

use App\Models\SpmbTpaKategori;
use App\Models\SpmbTpaSoal;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MoodleXmlImporterService
{
    /**
     * Import a single Moodle XML file.
     *
     * @param string $filePath Absolute path to the XML file
     * @param int|null $forcedKategoriId If specified, force import to this category
     * @param string $mode 'replace' to clear existing questions in category, or 'append'
     * @return array Results summary
     */
    public function importFile(string $filePath, ?int $forcedKategoriId = null, string $mode = 'replace'): array
    {
        if (! File::exists($filePath)) {
            throw new \Exception("File XML tidak ditemukan pada lokasi: {$filePath}");
        }

        $xmlContent = File::get($filePath);
        return $this->importXmlString($xmlContent, basename($filePath), $forcedKategoriId, $mode);
    }

    /**
     * Import from an XML string.
     */
    public function importXmlString(string $xmlContent, string $sourceName = 'upload.xml', ?int $forcedKategoriId = null, string $mode = 'replace'): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $msg = ! empty($errors) ? $errors[0]->message : 'Format XML tidak valid.';
            throw new \Exception("Gagal membaca berkas Moodle XML: {$msg}");
        }

        // 1. Determine Target Category
        $targetKategori = null;
        if ($forcedKategoriId) {
            $targetKategori = SpmbTpaKategori::find($forcedKategoriId);
        }

        if (! $targetKategori) {
            $targetKategori = $this->detectCategoryFromXml($xml, $sourceName);
        }

        if (! $targetKategori) {
            // Default fallback: first active category or BINDO
            $targetKategori = SpmbTpaKategori::first();
        }

        if (! $targetKategori) {
            throw new \Exception("Tidak ada Kategori TPA yang ditemukan di sistem untuk menampung soal ini.");
        }

        // 2. Prepare Directory for Extracted Images (both public/images/soal-tpa and storage)
        $publicDir = public_path('images/soal-tpa');
        $storageDir = storage_path('app/public/spmb/tpa/moodle');

        if (! File::isDirectory($publicDir)) {
            File::makeDirectory($publicDir, 0755, true);
        }
        if (! File::isDirectory($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }

        // 3. Clear existing questions if mode is 'replace'
        if ($mode === 'replace') {
            SpmbTpaSoal::where('spmb_tpa_kategori_id', $targetKategori->id)->delete();
        }

        $nextNomorUrut = SpmbTpaSoal::where('spmb_tpa_kategori_id', $targetKategori->id)->max('nomor_urut') ?? 0;

        $importedQuestions = 0;
        $extractedImages = 0;

        foreach ($xml->question as $q) {
            $type = (string) $q['type'];

            // Only process multichoice questions
            if ($type !== 'multichoice') {
                continue;
            }

            // Extract embedded images from questiontext
            if (isset($q->questiontext->file)) {
                foreach ($q->questiontext->file as $f) {
                    $fName = (string) $f['name'];
                    $fContent = base64_decode(trim((string) $f));
                    if ($fContent !== false) {
                        File::put($publicDir . '/' . $fName, $fContent);
                        File::put($storageDir . '/' . $fName, $fContent);
                        $extractedImages++;
                    }
                }
            }

            // Extract embedded images from answers
            foreach ($q->answer as $ans) {
                if (isset($ans->file)) {
                    foreach ($ans->file as $f) {
                        $fName = (string) $f['name'];
                        $fContent = base64_decode(trim((string) $f));
                        if ($fContent !== false) {
                            File::put($publicDir . '/' . $fName, $fContent);
                            File::put($storageDir . '/' . $fName, $fContent);
                            $extractedImages++;
                        }
                    }
                }
            }

            // Clean & URL-replace question text to use public /images/soal-tpa/
            $rawText = (string) $q->questiontext->text;
            $rawText = str_replace(['@@PLUGINFILE@@/', '/storage/spmb/tpa/moodle/'], '/images/soal-tpa/', $rawText);
            $cleanQuestion = trim(strip_tags($rawText, '<img><b><i><u><br><strong><em><table><tr><td><th><tbody>'));

            // Check if there is an image in question text or standalone
            $primaryGambar = null;
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $cleanQuestion, $imgMatch)) {
                $primaryGambar = $imgMatch[1];
            }

            // Process Options A - E
            $answers = [];
            $kunciJawaban = 'A';
            $maxFraction = -999.0;
            $letters = ['A', 'B', 'C', 'D', 'E'];
            $idx = 0;

            foreach ($q->answer as $ans) {
                if ($idx >= 5) {
                    break;
                }
                $letter = $letters[$idx];
                $fraction = (float) $ans['fraction'];
                $ansRaw = (string) $ans->text;
                $ansRaw = str_replace(['@@PLUGINFILE@@/', '/storage/spmb/tpa/moodle/'], '/images/soal-tpa/', $ansRaw);
                $ansClean = trim(strip_tags($ansRaw, '<img><b><i><u><br><strong><em><table><tr><td><th><tbody>'));

                $answers[$letter] = $ansClean;

                if ($fraction > $maxFraction) {
                    $maxFraction = $fraction;
                    $kunciJawaban = $letter;
                }
                $idx++;
            }

            // Pembahasan / General Feedback
            $feedbackRaw = (string) $q->generalfeedback->text;
            $feedbackClean = null;
            if (! empty(trim($feedbackRaw))) {
                $feedbackRaw = str_replace(['@@PLUGINFILE@@/', '/storage/spmb/tpa/moodle/'], '/images/soal-tpa/', $feedbackRaw);
                $feedbackClean = trim(strip_tags($feedbackRaw, '<img><b><i><u><br><strong><em>'));
            }

            // Bobot
            $bobot = (int) round((float) ($q->defaultgrade ?? 1));
            if ($bobot < 1) {
                $bobot = 1;
            }

            $nextNomorUrut++;

            SpmbTpaSoal::create([
                'spmb_tpa_kategori_id' => $targetKategori->id,
                'nomor_urut' => $nextNomorUrut,
                'pertanyaan' => $cleanQuestion,
                'gambar' => null, // Inline <img> is already preserved in pertanyaan
                'tipe_soal' => 'pilihan_ganda',
                'opsi_a' => $answers['A'] ?? '-',
                'opsi_b' => $answers['B'] ?? '-',
                'opsi_c' => $answers['C'] ?? '-',
                'opsi_d' => $answers['D'] ?? '-',
                'opsi_e' => $answers['E'] ?? null,
                'kunci_jawaban' => $kunciJawaban,
                'tingkat_kesulitan' => 'Sedang',
                'bobot' => $bobot,
                'pembahasan' => $feedbackClean,
                'is_active' => true,
            ]);

            $importedQuestions++;
        }

        return [
            'success' => true,
            'kategori_id' => $targetKategori->id,
            'kategori_nama' => $targetKategori->nama,
            'kategori_kode' => $targetKategori->kode,
            'imported_count' => $importedQuestions,
            'extracted_images' => $extractedImages,
            'source_name' => $sourceName,
        ];
    }

    /**
     * Import all 4 standard XML files from database/data or storage if present.
     */
    public function importAllLocalFiles(string $mode = 'replace'): array
    {
        $dir = database_path('data/soal-xml-moodle');
        if (! File::isDirectory($dir) || empty(File::glob($dir . '/*.xml'))) {
            $dir = storage_path('app/public/soal-xml-moodle');
        }

        if (! File::isDirectory($dir)) {
            throw new \Exception("Direktori file XML tidak ditemukan di {$dir}.");
        }

        $files = File::glob($dir . '/*.xml');
        if (empty($files)) {
            throw new \Exception("Tidak ada file .xml yang ditemukan di {$dir}.");
        }

        $results = [];
        $totalImported = 0;
        $totalImages = 0;

        foreach ($files as $file) {
            $res = $this->importFile($file, null, $mode);
            $results[] = $res;
            $totalImported += $res['imported_count'];
            $totalImages += $res['extracted_images'];
        }

        return [
            'total_files' => count($files),
            'total_imported' => $totalImported,
            'total_images' => $totalImages,
            'details' => $results,
        ];
    }

    /**
     * Helper to detect category based on XML category tag or filename.
     */
    private function detectCategoryFromXml(\SimpleXMLElement $xml, string $filename): ?SpmbTpaKategori
    {
        $categoryText = '';
        foreach ($xml->question as $q) {
            if ((string) $q['type'] === 'category') {
                $categoryText = (string) $q->category->text;
                break;
            }
        }

        $haystack = strtoupper($categoryText . ' ' . $filename);

        if (str_contains($haystack, 'BAHASA INDONESIA') || str_contains($haystack, 'BINDO')) {
            return SpmbTpaKategori::where('kode', 'BINDO')->first();
        }
        if (str_contains($haystack, 'MATEMATIKA') || str_contains($haystack, 'MTK')) {
            return SpmbTpaKategori::where('kode', 'MTK')->first();
        }
        if (str_contains($haystack, 'BAHASA INGGRIS') || str_contains($haystack, 'BING')) {
            return SpmbTpaKategori::where('kode', 'BING')->first();
        }
        if (str_contains($haystack, 'TIK')) {
            return SpmbTpaKategori::where('kode', 'TIK')->first();
        }

        return null;
    }
}
