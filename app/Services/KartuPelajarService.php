<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\User;
use App\Models\UserDigitalSignature;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorPNG;
use ZipArchive;

class KartuPelajarService
{
    // Dimensi standar kartu CR80 (8.6 x 5.4 cm) pada 300 DPI
    public const CARD_WIDTH = 1016;
    public const CARD_HEIGHT = 638;

    /**
     * Dapatkan data Kepala Sekolah, tanda tangan digital, dan NIP.
     */
    public function getKepalaSekolahData(): array
    {
        $transcriptConfig = \App\Models\TranscriptConfig::first();

        $kepsek = null;
        try {
            $kepsek = User::whereHas('roles', function ($q) {
                $q->where('name', 'Kepala Sekolah');
            })->with(['masterGuru.dapodikGuru'])->first();
        } catch (\Throwable $e) {
            $kepsek = null;
        }

        $nama = $kepsek?->name ?? $transcriptConfig?->principal_name ?? 'Kepala Sekolah';
        $nip = $kepsek?->nip ?? $kepsek?->masterGuru?->dapodikGuru?->nip ?? $transcriptConfig?->principal_nip ?? '-';
        $signaturePath = null;

        if ($kepsek) {
            $digitalSig = UserDigitalSignature::where('user_id', $kepsek->id)->first();
            if ($digitalSig && $digitalSig->ttd_image_path) {
                $fullPath = Storage::disk('public')->path($digitalSig->ttd_image_path);
                if (file_exists($fullPath)) {
                    $signaturePath = $fullPath;
                }
            }
        }

        if (! $signaturePath && $transcriptConfig?->manual_signature_enabled && $transcriptConfig?->manual_signature_path) {
            $fullPath = Storage::disk('public')->path($transcriptConfig->manual_signature_path);
            if (file_exists($fullPath)) {
                $signaturePath = $fullPath;
            }
        }

        return [
            'user' => $kepsek,
            'nama' => $nama,
            'nip' => $nip,
            'signature_path' => $signaturePath,
        ];
    }

    /**
     * Dapatkan barcode PNG base64 atau binary.
     */
    public function generateBarcodePng(string $nis): string
    {
        $generator = new BarcodeGeneratorPNG();
        return $generator->getBarcode($nis, $generator::TYPE_CODE_128, 2, 60);
    }

    /**
     * Generate kartu pelajar dalam format GD Image.
     */
    public function generateCardImage(MasterSiswa $siswa, ?string $namaKelas = null): \GdImage
    {
        $width = self::CARD_WIDTH;
        $height = self::CARD_HEIGHT;

        $img = imagecreatetruecolor($width, $height);

        // Warna Palet (Tema: Merah, Putih, Gray)
        $white       = imagecolorallocate($img, 255, 255, 255);
        $bgLight     = imagecolorallocate($img, 250, 250, 252);
        $redDark     = imagecolorallocate($img, 185, 28, 28);    // Red-700
        $redPrimary  = imagecolorallocate($img, 220, 38, 38);   // Red-600
        $redAccent   = imagecolorallocate($img, 239, 68, 68);   // Red-500
        $grayLight   = imagecolorallocate($img, 243, 244, 246); // Gray-100
        $grayBorder  = imagecolorallocate($img, 229, 231, 235); // Gray-200
        $grayText    = imagecolorallocate($img, 107, 114, 128); // Gray-500
        $grayDark    = imagecolorallocate($img, 55, 65, 81);    // Gray-700
        $black       = imagecolorallocate($img, 17, 24, 39);    // Gray-900

        // Background Utama
        imagefilledrectangle($img, 0, 0, $width, $height, $white);

        // Aksen Garis & Border Kartu Halus
        imagerectangle($img, 0, 0, $width - 1, $height - 1, $grayBorder);

        // Header Background Merah
        $headerHeight = 115;
        imagefilledrectangle($img, 0, 0, $width, $headerHeight, $redPrimary);

        // Aksen Garis Modern pada Header (Warna Gray & Merah Gelap)
        imagefilledpolygon($img, [
            $width - 260, 0,
            $width, 0,
            $width, $headerHeight,
            $width - 160, $headerHeight
        ], $redDark);

        imagefilledpolygon($img, [
            $width - 150, 0,
            $width - 120, 0,
            $width - 20, $headerHeight,
            $width - 50, $headerHeight
        ], $redAccent);

        // Garis batas header bawah tipis (Abu-abu emas)
        imagefilledrectangle($img, 0, $headerHeight, $width, $headerHeight + 4, $grayBorder);

        // Logo Sekolah di Kiri Header
        $this->drawSchoolLogo($img, 24, 15, 85, 85);

        // Teks Header Sekolah (Gunakan TrueType Font jika ada, atau imagestring fallback)
        $fontBold = $this->getFontPath('bold');
        $fontRegular = $this->getFontPath('regular');

        if ($fontBold && file_exists($fontBold)) {
            imagettftext($img, 18, 0, 125, 42, $white, $fontBold, 'SMK TELKOM LAMPUNG');
            imagettftext($img, 14, 0, 125, 70, $white, $fontBold, 'KARTU TANDA PELAJAR');
            imagettftext($img, 9, 0, 125, 94, $grayLight, $fontRegular ?: $fontBold, 'Jl. P. Ranau No. 88, Airan Raya, Kec. Jati Agung, Lampung Selatan');
        } else {
            imagestring($img, 5, 125, 20, 'SMK TELKOM LAMPUNG', $white);
            imagestring($img, 4, 125, 45, 'KARTU TANDA PELAJAR', $white);
            imagestring($img, 2, 125, 75, 'Jl. P. Ranau No. 88, Airan Raya, Lampung Selatan', $grayLight);
        }

        // ================= Area Kiri: Foto Siswa & Barcode =================
        $photoX = 40;
        $photoY = 145;
        $photoW = 200;
        $photoH = 260;

        // Bingkai Foto (Background Gray dengan Border)
        imagefilledrectangle($img, $photoX - 3, $photoY - 3, $photoX + $photoW + 3, $photoY + $photoH + 3, $grayBorder);
        imagefilledrectangle($img, $photoX, $photoY, $photoX + $photoW, $photoY + $photoH, $grayLight);

        // Gambar Foto Siswa
        $this->drawStudentPhoto($img, $siswa, $photoX, $photoY, $photoW, $photoH);

        // Barcode 1D (NIS) di Bawah Foto
        $barcodeY = $photoY + $photoH + 20;
        $this->drawBarcode($img, $siswa->nis, $photoX - 5, $barcodeY, 210, 60);

        // ================= Area Kanan: Biodata Siswa =================
        $infoX = 280;
        $infoY = 160;

        // Nama Siswa (Besar & Tebal)
        $namaSiswa = strtoupper($siswa->nama_lengkap);
        if ($fontBold && file_exists($fontBold)) {
            imagettftext($img, 18, 0, $infoX, $infoY + 15, $black, $fontBold, $namaSiswa);
        } else {
            imagestring($img, 5, $infoX, $infoY, $namaSiswa, $black);
        }

        // Garis Pembatas Nama
        imagefilledrectangle($img, $infoX, $infoY + 28, $infoX + 440, $infoY + 30, $redPrimary);

        // Detail Biodata
        $details = [
            'NIS / NIPD'       => $siswa->nis,
            'Kelas'            => $namaKelas ?: ($siswa->rombels->first()?->kelas?->nama_kelas ?? '-'),
            'Tempat, Tgl Lahir'=> ($siswa->tempat_lahir ? $siswa->tempat_lahir . ', ' : '') . ($siswa->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d F Y') : '-'),
            'Jenis Kelamin'    => $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin === 'P' ? 'Perempuan' : '-'),
            'Alamat'           => Str::limit($siswa->alamat ?: '-', 55),
        ];

        $currY = $infoY + 65;
        $lineGap = 36;
        $labelWidth = 170;

        foreach ($details as $label => $val) {
            if ($fontBold && file_exists($fontBold)) {
                imagettftext($img, 11, 0, $infoX, $currY, $grayText, $fontRegular ?: $fontBold, $label);
                imagettftext($img, 11, 0, $infoX + $labelWidth - 20, $currY, $grayText, $fontRegular ?: $fontBold, ':');
                imagettftext($img, 11, 0, $infoX + $labelWidth, $currY, $grayDark, $fontBold, (string) $val);
            } else {
                imagestring($img, 3, $infoX, $currY - 12, $label, $grayText);
                imagestring($img, 3, $infoX + $labelWidth - 15, $currY - 12, ':', $grayText);
                imagestring($img, 3, $infoX + $labelWidth, $currY - 12, (string) $val, $grayDark);
            }
            $currY += $lineGap;
        }

        // ================= Area Bawah Kanan: Tanda Tangan Kepala Sekolah =================
        $kepsekData = $this->getKepalaSekolahData();
        $ttdX = 710;
        $ttdY = 410;

        if ($fontBold && file_exists($fontBold)) {
            imagettftext($img, 10, 0, $ttdX, $ttdY, $grayDark, $fontRegular ?: $fontBold, 'Kepala Sekolah,');
        } else {
            imagestring($img, 2, $ttdX, $ttdY - 10, 'Kepala Sekolah,', $grayDark);
        }

        // Gambar Tanda Tangan Digital jika ada
        if (!empty($kepsekData['signature_path']) && file_exists($kepsekData['signature_path'])) {
            $this->drawImageToCanvas($img, $kepsekData['signature_path'], $ttdX - 10, $ttdY + 8, 140, 75);
        }

        // Nama & NIP Kepala Sekolah
        $nameY = $ttdY + 95;
        if ($fontBold && file_exists($fontBold)) {
            imagettftext($img, 10, 0, $ttdX, $nameY, $black, $fontBold, $kepsekData['nama']);
            // Underline nama kepsek
            $bbox = imagettfbbox(10, 0, $fontBold, $kepsekData['nama']);
            $textWidth = abs($bbox[4] - $bbox[0]);
            imagefilledrectangle($img, $ttdX, $nameY + 3, $ttdX + $textWidth, $nameY + 4, $black);

            imagettftext($img, 9, 0, $ttdX, $nameY + 20, $grayDark, $fontRegular ?: $fontBold, 'NIP. ' . $kepsekData['nip']);
        } else {
            imagestring($img, 3, $ttdX, $nameY - 10, $kepsekData['nama'], $black);
            imagestring($img, 2, $ttdX, $nameY + 8, 'NIP. ' . $kepsekData['nip'], $grayDark);
        }

        // Strip Footer Merah Tipis di Bagian Paling Bawah
        imagefilledrectangle($img, 0, $height - 14, $width, $height, $redPrimary);

        return $img;
    }

    /**
     * Dapatkan path font TrueType jika tersedia.
     */
    private function getFontPath(string $type = 'regular'): ?string
    {
        $candidates = [
            'bold' => [
                'C:/Windows/Fonts/arialbd.ttf',
                'C:/Windows/Fonts/calibrib.ttf',
                'C:/Windows/Fonts/segoeuib.ttf',
                base_path('vendor/dompdf/dompdf/lib/fonts/Helvetica-Bold.ttf'),
            ],
            'regular' => [
                'C:/Windows/Fonts/arial.ttf',
                'C:/Windows/Fonts/calibri.ttf',
                'C:/Windows/Fonts/segoeui.ttf',
                base_path('vendor/dompdf/dompdf/lib/fonts/Helvetica.ttf'),
            ],
        ];

        foreach ($candidates[$type] ?? [] as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Gambar logo sekolah ke canvas.
     */
    private function drawSchoolLogo(\GdImage $canvas, int $x, int $y, int $targetW, int $targetH): void
    {
        $logoPath = null;
        $appSetting = AppSetting::first();

        if ($appSetting && $appSetting->logo && Storage::disk('public')->exists($appSetting->logo)) {
            $logoPath = Storage::disk('public')->path($appSetting->logo);
        } elseif (file_exists(public_path('loader.png'))) {
            $logoPath = public_path('loader.png');
        } elseif (file_exists(public_path('images/asset-report/smk-telkom-lampung-white.png'))) {
            $logoPath = public_path('images/asset-report/smk-telkom-lampung-white.png');
        }

        if ($logoPath && file_exists($logoPath)) {
            $this->drawImageToCanvas($canvas, $logoPath, $x, $y, $targetW, $targetH, true);
        } else {
            // Draw placeholder white emblem
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagearc($canvas, $x + ($targetW / 2), $y + ($targetH / 2), $targetW, $targetH, 0, 360, $white);
            imagestring($canvas, 3, $x + 20, $y + 35, 'TELKOM', $white);
        }
    }

    /**
     * Gambar foto siswa ke canvas dengan resize & crop proporsional.
     */
    private function drawStudentPhoto(\GdImage $canvas, MasterSiswa $siswa, int $x, int $y, int $targetW, int $targetH): void
    {
        $photoPath = null;

        if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
            $photoPath = Storage::disk('public')->path($siswa->foto);
        } elseif ($siswa->user?->avatar && Storage::disk('public')->exists($siswa->user->avatar)) {
            $photoPath = Storage::disk('public')->path($siswa->user->avatar);
        }

        if ($photoPath && file_exists($photoPath)) {
            $this->drawImageToCanvas($canvas, $photoPath, $x, $y, $targetW, $targetH);
        } else {
            // Gambar siluet avatar default
            $gray = imagecolorallocate($canvas, 156, 163, 175);
            $bg = imagecolorallocate($canvas, 243, 244, 246);
            imagefilledrectangle($canvas, $x, $y, $x + $targetW, $y + $targetH, $bg);

            // Lingkaran kepala
            imagefilledellipse($canvas, $x + ($targetW / 2), $y + 90, 80, 80, $gray);
            // Bahu/badan
            imagefilledarc($canvas, $x + ($targetW / 2), $y + 240, 160, 140, 180, 360, $gray, IMG_ARC_PIE);
            imagestring($canvas, 2, $x + 35, $y + $targetH - 30, 'Foto Siswa', $gray);
        }
    }

    /**
     * Gambar barcode ke canvas.
     */
    private function drawBarcode(\GdImage $canvas, string $nis, int $x, int $y, int $targetW, int $targetH): void
    {
        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($nis, $generator::TYPE_CODE_128, 2, 45);

            $barcodeImg = imagecreatefromstring($barcodeData);
            if ($barcodeImg) {
                $bW = imagesx($barcodeImg);
                $bH = imagesy($barcodeImg);

                // Buat background putih untuk barcode
                $white = imagecolorallocate($canvas, 255, 255, 255);
                $grayDark = imagecolorallocate($canvas, 31, 41, 55);
                imagefilledrectangle($canvas, $x, $y, $x + $targetW, $y + $targetH + 18, $white);

                // Copy barcode
                imagecopyresampled($canvas, $barcodeImg, $x + 5, $y + 2, 0, 0, $targetW - 10, $targetH, $bW, $bH);
                imagedestroy($barcodeImg);

                // Teks NIS di bawah barcode
                $fontBold = $this->getFontPath('bold');
                if ($fontBold && file_exists($fontBold)) {
                    $bbox = imagettfbbox(9, 0, $fontBold, $nis);
                    $tw = abs($bbox[4] - $bbox[0]);
                    $textX = $x + (($targetW - $tw) / 2);
                    imagettftext($canvas, 9, 0, (int) $textX, $y + $targetH + 14, $grayDark, $fontBold, $nis);
                } else {
                    imagestring($canvas, 2, $x + 40, $y + $targetH + 2, $nis, $grayDark);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Gagal generate barcode GD: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk menggambar file gambar ke canvas dengan proporsional scaling.
     */
    private function drawImageToCanvas(\GdImage $canvas, string $filePath, int $dstX, int $dstY, int $dstW, int $dstH, bool $preserveAspect = false): void
    {
        $info = @getimagesize($filePath);
        if (!$info) return;

        $mime = $info['mime'] ?? '';
        $src = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($filePath),
            'image/png'  => @imagecreatefrompng($filePath),
            'image/webp' => @imagecreatefromwebp($filePath),
            default      => null,
        };

        if (!$src) return;

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        if ($preserveAspect) {
            // Hitung skala aspect ratio terbaik
            $ratio = min($dstW / $srcW, $dstH / $srcH);
            $newW = (int) ($srcW * $ratio);
            $newH = (int) ($srcH * $ratio);
            $offsetX = $dstX + (int) (($dstW - $newW) / 2);
            $offsetY = $dstY + (int) (($dstH - $newH) / 2);

            imagecopyresampled($canvas, $src, $offsetX, $offsetY, 0, 0, $newW, $newH, $srcW, $srcH);
        } else {
            // Crop center to fill target box
            $srcRatio = $srcW / $srcH;
            $dstRatio = $dstW / $dstH;

            if ($srcRatio > $dstRatio) {
                $cropW = (int) ($srcH * $dstRatio);
                $cropH = $srcH;
                $cropX = (int) (($srcW - $cropW) / 2);
                $cropY = 0;
            } else {
                $cropW = $srcW;
                $cropH = (int) ($srcW / $dstRatio);
                $cropX = 0;
                $cropY = (int) (($srcH - $cropH) / 2);
            }

            imagecopyresampled($canvas, $src, $dstX, $dstY, $cropX, $cropY, $dstW, $dstH, $cropW, $cropH);
        }

        imagedestroy($src);
    }

    /**
     * Simpan kartu pelajar menjadi file JPG.
     */
    public function saveCardAsJpg(MasterSiswa $siswa, string $destinationPath, ?string $namaKelas = null): bool
    {
        $img = $this->generateCardImage($siswa, $namaKelas);
        $dir = dirname($destinationPath);
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }
        $saved = imagejpeg($img, $destinationPath, 95);
        imagedestroy($img);
        return $saved;
    }

    /**
     * Generate ZIP archive kartu pelajar (per kelas / pilihan).
     * Format nama file: {nama kelas}_{nama siswa}_{nis}.jpg
     */
    public function generateZipArchive(Collection $siswaList, ?Rombel $rombel = null): string
    {
        $zipFileName = 'kartu_pelajar_' . ($rombel ? Str::slug($rombel->kelas->nama_kelas ?? 'rombel') : 'masal') . '_' . time() . '.zip';
        $tempDir = storage_path('app/temp/kartu_pelajar_' . uniqid());
        File::ensureDirectoryExists($tempDir);

        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipFileName;
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Tidak dapat membuat file ZIP pada: {$zipPath}");
        }

        $defaultKelas = $rombel?->kelas?->nama_kelas ?? 'Tanpa Kelas';

        foreach ($siswaList as $siswa) {
            $namaKelas = $siswa->rombels->first()?->kelas?->nama_kelas ?? $defaultKelas;
            
            // Format nama file: nama kelas_nama siswa_nis.jpg
            $rawFileName = "{$namaKelas}_{$siswa->nama_lengkap}_{$siswa->nis}.jpg";
            $cleanFileName = preg_replace('/[\\\\\/:*?"<>|]/', '-', $rawFileName);

            $tempCardJpg = $tempDir . DIRECTORY_SEPARATOR . $cleanFileName;
            $this->saveCardAsJpg($siswa, $tempCardJpg, $namaKelas);

            if (file_exists($tempCardJpg)) {
                $zip->addFile($tempCardJpg, $cleanFileName);
            }
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * Upload masal foto siswa berdasarkan NIS.
     * Menerima file ZIP atau multiple image files.
     */
    public function processMassPhotoUpload(array|UploadedFile $files): array
    {
        Storage::disk('public')->makeDirectory('siswa_photos');

        $matched = 0;
        $unmatched = [];
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

        // Jika input adalah file ZIP
        if ($files instanceof UploadedFile && in_array(strtolower($files->getClientOriginalExtension()), ['zip'])) {
            $zip = new ZipArchive();
            if ($zip->open($files->getRealPath()) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entryName = $zip->getNameIndex($i);
                    // Lewati folder atau file sistem
                    if (str_ends_with($entryName, '/') || str_starts_with(basename($entryName), '.')) {
                        continue;
                    }

                    $ext = strtolower(pathinfo($entryName, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExts)) {
                        continue;
                    }

                    $nis = trim(pathinfo($entryName, PATHINFO_FILENAME));
                    $siswa = MasterSiswa::where('nis', $nis)->first();

                    if ($siswa) {
                        $stream = $zip->getStream($entryName);
                        if ($stream) {
                            $content = stream_get_contents($stream);
                            fclose($stream);

                            $savePath = "siswa_photos/{$nis}.jpg";
                            Storage::disk('public')->put($savePath, $content);
                            $siswa->update(['foto' => $savePath]);
                            $matched++;
                        }
                    } else {
                        $unmatched[] = basename($entryName);
                    }
                }
                $zip->close();
            }
        } else {
            // Jika multiple image files
            $fileArray = is_array($files) ? $files : [$files];

            foreach ($fileArray as $file) {
                if (!$file instanceof UploadedFile) continue;

                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, $allowedExts)) continue;

                $nis = trim(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $siswa = MasterSiswa::where('nis', $nis)->first();

                if ($siswa) {
                    $savePath = "siswa_photos/{$nis}.jpg";
                    Storage::disk('public')->put($savePath, file_get_contents($file->getRealPath()));
                    $siswa->update(['foto' => $savePath]);
                    $matched++;
                } else {
                    $unmatched[] = $file->getClientOriginalName();
                }
            }
        }

        return [
            'success' => true,
            'matched' => $matched,
            'unmatched' => $unmatched,
            'total' => $matched + count($unmatched),
        ];
    }
}
