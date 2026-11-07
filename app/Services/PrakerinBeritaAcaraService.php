<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\PrakerinBimbinganJudul;
use App\Models\PrakerinBimbinganLaporan;
use App\Models\PrakerinBimbinganTahap;
use App\Models\UserDigitalSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrakerinBeritaAcaraService
{
    /**
     * Generate Berita Acara Bimbingan per Tahap (PDF).
     * Mendukung objek PrakerinBimbinganBeritaAcara spesifik maupun PrakerinBimbinganTahap.
     */
    public function generateBeritaAcaraPdf($target)
    {
        $beritaAcara = null;
        if ($target instanceof \App\Models\PrakerinBimbinganBeritaAcara) {
            $beritaAcara = $target;
            $tahap = $beritaAcara->tahap;
        } else {
            $tahap = $target;
            $beritaAcara = $tahap->latestBeritaAcara;
        }

        $tahap->load([
            'laporan.penempatan.siswa.rombels.kelas',
            'laporan.penempatan.industri',
            'laporan.penempatan.guruPembimbing.dapodikGuru',
            'laporan.penempatan.guruPembimbing.user.masterGuru.dapodikGuru',
            'reviewer.masterGuru.dapodikGuru',
            'anotasis',
        ]);

        $laporan = $tahap->laporan;
        $penempatan = $laporan->penempatan;
        $guru = $penempatan->guruPembimbing;
        $guruUser = ($beritaAcara?->pembimbing) ?? ($guru?->user ?? $tahap->reviewer);
        $guruNip = $guru?->dapodikGuru?->nip ?? $guruUser?->masterGuru?->dapodikGuru?->nip;

        // Ambil tanda tangan digital guru jika ada
        $sig = $guruUser ? UserDigitalSignature::where('user_id', $guruUser->id)->first() : null;

        $nomorBeritaAcara = $beritaAcara?->nomor_berita_acara
            ?? $tahap->nomor_berita_acara
            ?? ('BA-PKL/' . $tahap->id . '/' . date('Y'));

        $isAcc = $beritaAcara ? ($beritaAcara->jenis === 'disetujui') : ($tahap->status === 'disetujui');
        $revisiKe = $beritaAcara?->revisi_ke;
        $catatanPembimbing = $beritaAcara?->catatan_pembimbing ?? $tahap->catatan_pembimbing;
        $tanggalReview = $beritaAcara?->diterbitkan_at ?? $tahap->reviewed_at ?? now();

        // Terbitkan / sinkronkan DigitalDocument resmi jika akun guru tersedia
        $digDoc = null;
        if ($guruUser) {
            $docTitle = ($isAcc ? 'Berita Acara Pengesahan (ACC)' : ('Berita Acara Revisi' . ($revisiKe ? ' (Ke-' . $revisiKe . ')' : '')))
                . ' — ' . $tahap->judul_tahap . ' (' . ($penempatan->siswa?->nama_lengkap ?? '-') . ')';
            $refId = $beritaAcara ? $beritaAcara->id : $tahap->id;
            $hashParts = [
                'BERITA_ACARA_PRAKERIN',
                (string) $refId,
                $nomorBeritaAcara,
                (string) ($penempatan->siswa?->nis ?? ''),
                $isAcc ? 'ACC' : 'REVISI',
                (string) $tanggalReview->timestamp,
            ];
            $digDoc = \App\Models\DigitalDocument::autoSign(
                $guruUser,
                'BERITA_ACARA_PRAKERIN',
                $docTitle,
                $refId,
                $hashParts
            );
        }

        // QR Code Digital Verification URL
        $verifyData = [
            'doc' => $isAcc ? 'BERITA_ACARA_ACC_BIMBINGAN_PRAKERIN' : 'BERITA_ACARA_REVISI_BIMBINGAN_PRAKERIN',
            'nomor' => $nomorBeritaAcara,
            'siswa' => $penempatan->siswa?->nama_lengkap,
            'nis' => $penempatan->siswa?->nis,
            'bab' => $tahap->judul_tahap,
            'status' => $isAcc ? 'disetujui' : 'perlu_revisi',
            'revisi_ke' => $revisiKe,
            'guru' => $guru?->nama_lengkap ?? $guruUser?->name,
            'tanggal' => $tanggalReview->format('d-m-Y H:i'),
        ];

        $verifyPayload = $digDoc 
            ? route('verifikasi.dokumen', $digDoc->token) 
            : route('verifikasi.bimbingan-prakerin', base64_encode(json_encode($verifyData)));

        $qrCodeSvg = QrCode::format('svg')
            ->size(90)
            ->margin(0)
            ->generate($verifyPayload);

        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.berita-acara-bimbingan-prakerin', [
            'tahap' => $tahap,
            'beritaAcara' => $beritaAcara,
            'nomorBeritaAcara' => $nomorBeritaAcara,
            'isAcc' => $isAcc,
            'revisiKe' => $revisiKe,
            'catatanPembimbing' => $catatanPembimbing,
            'tanggalReview' => $tanggalReview,
            'laporan' => $laporan,
            'penempatan' => $penempatan,
            'siswa' => $penempatan->siswa,
            'kelas' => $penempatan->siswa?->rombels->first()?->kelas?->nama_kelas ?? 'XII',
            'guru' => $guru,
            'guruUser' => $guruUser,
            'guruNip' => $guruNip,
            'sig' => $sig,
            'qrCodeBase64' => $qrCodeBase64,
            'anotasis' => $tahap->anotasis,
            'kopBase64' => $this->getKopSuratUkkBase64(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate Berita Acara Review / Pengajuan Judul Laporan (PDF).
     */
    public function generateBeritaAcaraJudulPdf(PrakerinBimbinganJudul $judul)
    {
        $judul->load([
            'laporan.penempatan.siswa.rombels.kelas',
            'laporan.penempatan.industri',
            'laporan.penempatan.guruPembimbing.dapodikGuru',
            'laporan.penempatan.guruPembimbing.user.masterGuru.dapodikGuru',
            'laporan.penempatan.rombelPkl',
            'reviewer.masterGuru.dapodikGuru',
        ]);

        $laporan = $judul->laporan;
        $penempatan = $laporan->penempatan;
        $guru = $penempatan->guruPembimbing;
        $guruUser = $judul->reviewer ?? $guru?->user;
        $guruNip = $guru?->dapodikGuru?->nip ?? $guruUser?->masterGuru?->dapodikGuru?->nip;

        // Ambil tanda tangan digital guru jika ada
        $sig = $guruUser ? UserDigitalSignature::where('user_id', $guruUser->id)->first() : null;

        $isAcc = $judul->status === 'disetujui';
        $nomorBeritaAcara = $judul->nomor_berita_acara
            ?? (($isAcc ? 'BA-JUDUL-ACC/' : 'BA-JUDUL-TOLAK/') . date('Ymd') . '/' . str_pad($laporan->id, 4, '0', STR_PAD_LEFT));

        $catatanPembimbing = $judul->catatan_pembimbing;
        $tanggalReview = $judul->reviewed_at ?? now();

        // Terbitkan / sinkronkan DigitalDocument resmi jika akun guru tersedia
        $digDoc = null;
        if ($guruUser) {
            $docTitle = ($isAcc ? 'Berita Acara Persetujuan Judul Laporan' : 'Berita Acara Penolakan / Revisi Judul Laporan')
                . ' — ' . ($penempatan->siswa?->nama_lengkap ?? '-') . ' (' . $judul->judul_laporan . ')';
            $refId = $judul->id;
            $hashParts = [
                'BERITA_ACARA_JUDUL_PRAKERIN',
                (string) $refId,
                $nomorBeritaAcara,
                (string) ($penempatan->siswa?->nis ?? ''),
                $isAcc ? 'ACC_JUDUL' : 'TOLAK_JUDUL',
                (string) $tanggalReview->timestamp,
            ];
            $digDoc = \App\Models\DigitalDocument::autoSign(
                $guruUser,
                'BERITA_ACARA_JUDUL_PRAKERIN',
                $docTitle,
                $refId,
                $hashParts
            );
        }

        // QR Code Digital Verification URL
        $verifyData = [
            'doc' => $isAcc ? 'BERITA_ACARA_ACC_JUDUL_PRAKERIN' : 'BERITA_ACARA_PENOLAKAN_JUDUL_PRAKERIN',
            'nomor' => $nomorBeritaAcara,
            'siswa' => $penempatan->siswa?->nama_lengkap,
            'nis' => $penempatan->siswa?->nis,
            'judul' => $judul->judul_laporan,
            'status' => $judul->status,
            'pengajuan_ke' => $judul->pengajuan_ke,
            'guru' => $guru?->nama_lengkap ?? $guruUser?->name,
            'tanggal' => $tanggalReview->format('d-m-Y H:i'),
        ];

        $verifyPayload = $digDoc
            ? route('verifikasi.dokumen', $digDoc->token)
            : route('verifikasi.bimbingan-prakerin', base64_encode(json_encode($verifyData)));

        $qrCodeSvg = QrCode::format('svg')
            ->size(90)
            ->margin(0)
            ->generate($verifyPayload);

        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.berita-acara-judul-prakerin', [
            'judul' => $judul,
            'nomorBeritaAcara' => $nomorBeritaAcara,
            'isAcc' => $isAcc,
            'catatanPembimbing' => $catatanPembimbing,
            'tanggalReview' => $tanggalReview,
            'laporan' => $laporan,
            'penempatan' => $penempatan,
            'siswa' => $penempatan->siswa,
            'kelas' => $penempatan->siswa?->rombels->first()?->kelas?->nama_kelas ?? 'XII',
            'guru' => $guru,
            'guruUser' => $guruUser,
            'guruNip' => $guruNip,
            'sig' => $sig,
            'qrCodeBase64' => $qrCodeBase64,
            'kopBase64' => $this->getKopSuratUkkBase64(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate Rekap Riwayat Bimbingan Lengkap Siswa (PDF).
     */
    public function generateRiwayatBimbinganPdf(PrakerinBimbinganLaporan $laporan)
    {
        $laporan->load([
            'penempatan.siswa.rombels.kelas',
            'penempatan.industri',
            'penempatan.guruPembimbing.dapodikGuru',
            'penempatan.guruPembimbing.user.masterGuru.dapodikGuru',
            'penempatan.rombelPkl',
            'judulRiwayats.reviewer',
            'tahaps.anotasis',
            'tahaps.reviewer',
            'accBy.masterGuru.dapodikGuru',
        ]);

        $penempatan = $laporan->penempatan;
        $guru = $penempatan->guruPembimbing;
        $guruUser = $guru?->user ?? $laporan->accBy;
        $guruNip = $guru?->dapodikGuru?->nip ?? $guruUser?->masterGuru?->dapodikGuru?->nip;

        $sig = $guruUser ? UserDigitalSignature::where('user_id', $guruUser->id)->first() : null;

        // Auto sign rekap riwayat bimbingan jika akun guru tersedia
        $digDoc = null;
        if ($guruUser) {
            $docTitle = 'Rekap Riwayat Bimbingan Prakerin — ' . ($penempatan->siswa?->nama_lengkap ?? '-') . ' (' . ($laporan->judul_laporan ?? 'Laporan PKL') . ')';
            $refId = $laporan->id;
            $hashParts = [
                'REKAP_BIMBINGAN_PRAKERIN',
                (string) $refId,
                (string) ($penempatan->siswa?->nis ?? ''),
                (string) ($laporan->status_laporan),
                (string) ($laporan->acc_at?->timestamp ?? time()),
            ];
            $digDoc = \App\Models\DigitalDocument::autoSign(
                $guruUser,
                'REKAP_BIMBINGAN_PRAKERIN',
                $docTitle,
                $refId,
                $hashParts
            );
        }

        // QR Code untuk Pengesahan ACC Final atau Riwayat
        $verifyData = [
            'doc' => 'REKAP_RIWAYAT_BIMBINGAN_PRAKERIN',
            'laporan_id' => $laporan->id,
            'siswa' => $penempatan->siswa?->nama_lengkap,
            'nis' => $penempatan->siswa?->nis,
            'judul' => $laporan->judul_laporan,
            'guru' => $guru?->nama_lengkap ?? $guruUser?->name,
            'status_laporan' => $laporan->status_laporan,
            'acc_at' => $laporan->acc_at?->format('d-m-Y H:i') ?? '-',
        ];

        $verifyPayload = $digDoc
            ? route('verifikasi.dokumen', $digDoc->token)
            : route('verifikasi.riwayat-bimbingan', base64_encode(json_encode($verifyData)));

        $qrCodeSvg = QrCode::format('svg')
            ->size(95)
            ->margin(0)
            ->generate($verifyPayload);

        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.rekap-riwayat-bimbingan-prakerin', [
            'laporan' => $laporan,
            'penempatan' => $penempatan,
            'siswa' => $penempatan->siswa,
            'kelas' => $penempatan->siswa?->rombels->first()?->kelas?->nama_kelas ?? 'XII',
            'guru' => $guru,
            'guruUser' => $guruUser,
            'guruNip' => $guruNip,
            'sig' => $sig,
            'qrCodeBase64' => $qrCodeBase64,
            'tahaps' => $laporan->tahaps,
            'judulRiwayats' => $laporan->judulRiwayats->sortBy('pengajuan_ke'),
            'kopBase64' => $this->getKopSuratUkkBase64(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Ambil data kop surat UKK (yang diupload super admin) dalam bentuk Base64 Data URI.
     */
    public function getKopSuratUkkBase64(): ?string
    {
        $setting = AppSetting::first();
        if ($setting) {
            return $setting->getKopSuratUkkBase64();
        }

        return null;
    }
}
