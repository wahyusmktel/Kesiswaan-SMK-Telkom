<?php

namespace App\Services;

use App\Models\PrakerinBimbinganLaporan;
use App\Models\PrakerinBimbinganTahap;
use App\Models\UserDigitalSignature;
use Barryvdh\DomPDF\Facade\Pdf;
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
            'laporan.penempatan.guruPembimbing.user',
            'reviewer',
            'anotasis',
        ]);

        $laporan = $tahap->laporan;
        $penempatan = $laporan->penempatan;
        $guru = $penempatan->guruPembimbing;
        $guruUser = ($beritaAcara?->pembimbing) ?? ($guru?->user ?? $tahap->reviewer);

        // Ambil tanda tangan digital guru jika ada
        $sig = $guruUser ? UserDigitalSignature::where('user_id', $guruUser->id)->first() : null;

        $nomorBeritaAcara = $beritaAcara?->nomor_berita_acara
            ?? $tahap->nomor_berita_acara
            ?? ('BA-PKL/' . $tahap->id . '/' . date('Y'));

        $isAcc = $beritaAcara ? ($beritaAcara->jenis === 'disetujui') : ($tahap->status === 'disetujui');
        $revisiKe = $beritaAcara?->revisi_ke;
        $catatanPembimbing = $beritaAcara?->catatan_pembimbing ?? $tahap->catatan_pembimbing;
        $tanggalReview = $beritaAcara?->diterbitkan_at ?? $tahap->reviewed_at ?? now();

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

        $verifyPayload = url('/verifikasi/bimbingan-prakerin/' . base64_encode(json_encode($verifyData)));

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
            'sig' => $sig,
            'qrCodeBase64' => $qrCodeBase64,
            'anotasis' => $tahap->anotasis,
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
            'penempatan.guruPembimbing.user',
            'penempatan.rombelPkl',
            'tahaps.anotasis',
            'tahaps.reviewer',
            'accBy',
        ]);

        $penempatan = $laporan->penempatan;
        $guru = $penempatan->guruPembimbing;
        $guruUser = $guru?->user ?? $laporan->accBy;

        $sig = $guruUser ? UserDigitalSignature::where('user_id', $guruUser->id)->first() : null;

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

        $verifyPayload = url('/verifikasi/riwayat-bimbingan/' . base64_encode(json_encode($verifyData)));

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
            'sig' => $sig,
            'qrCodeBase64' => $qrCodeBase64,
            'tahaps' => $laporan->tahaps,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }
}
