<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\WaliKelas\PerizinanController as WaliKelasPerizinanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MasterData\MasterSiswaController;
use App\Http\Controllers\MasterData\RombelController;
use App\Http\Controllers\Kesiswaan\MonitoringIzinController;
use App\Http\Controllers\Kesiswaan\DashboardController;
use App\Http\Controllers\WaliKelas\DashboardController as WaliKelasDashboardController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\BK\DashboardController as BKDashboardController;
use App\Http\Controllers\BK\MonitoringController as BKMonitoringController;
use App\Http\Controllers\Piket\DashboardController as PiketDashboardController;
use App\Http\Controllers\Piket\MonitoringController as PiketMonitoringController;
use App\Http\Controllers\Kurikulum\MataPelajaranController;
use App\Http\Controllers\Kurikulum\MasterGuruController;
use App\Http\Controllers\Kurikulum\JadwalPelajaranController;
use App\Http\Controllers\Kurikulum\DashboardController as KurikulumDashboardController;
use App\Http\Controllers\GuruKelas\DashboardController as GuruKelasDashboardController;
use App\Http\Controllers\Siswa\IzinMeninggalkanKelasController;
use App\Http\Controllers\GuruKelas\PersetujuanIzinKeluarController;
use App\Http\Controllers\Piket\PersetujuanIzinKeluarController as PiketPersetujuanIzinKeluarController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\Security\VerifikasiController as SecurityVerifikasiController;
use App\Http\Controllers\Kurikulum\JamPelajaranController;
use App\Http\Controllers\Piket\PenangananTerlambatController;
use App\Http\Controllers\Security\PendataanTerlambatController;
use App\Http\Controllers\Piket\VerifikasiTerlambatController;
use App\Http\Controllers\PublicVerifikasiController;
use App\Http\Controllers\GuruKelas\VerifikasiTerlambatController as GuruKelasVerifikasiTerlambatController;
use App\Http\Controllers\Dispensasi\PengajuanDispensasiController;
use App\Http\Controllers\Kesiswaan\PersetujuanDispensasiController;
use App\Http\Controllers\Prakerin\IndustriController;
use App\Http\Controllers\Prakerin\PenempatanController;
use App\Http\Controllers\Prakerin\JurnalSiswaController;
use App\Http\Controllers\Prakerin\MonitoringPembimbingController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\MasterData\TahunPelajaranController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\Admin\SuperAdminController;
use App\Http\Controllers\SDM\NdeReferensiController;
use App\Http\Controllers\Shared\NotaDinasController;

Route::get('/verifikasi/surat/{uuid}', [VerifikasiController::class, 'show'])->name('verifikasi.surat');
Route::get('/verifikasi/kartu/{nis}', [VerifikasiController::class, 'kartuPelajar'])->name('verifikasi.kartu');
// ==================================
//     BATAS ROUTE PUBLIK
// ==================================

// ==================================================
//      ROUTE UNTUK HALAMAN VERIFIKASI PUBLIK
// ==================================================
Route::get('/verifikasi/surat-terlambat/{uuid}', [PublicVerifikasiController::class, 'showSuratTerlambat'])
    ->name('verifikasi.surat-terlambat');

Route::get('/verifikasi/dispensasi/{dispensasi}', [PublicVerifikasiController::class, 'showDispensasi'])
    ->name('verifikasi.dispensasi');

// Route Pengaduan Orang Tua (Publik)
Route::get('/pengaduan', [PengaduanController::class, 'create'])->name('pengaduan.create');
Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:Waka Kesiswaan'])->prefix('admin')->name('admin.')->group(function () {
    // ... route lain ...
    Route::resource('roles', RoleController::class)->except(['create', 'edit', 'show']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    // ... route dashboard dan profile dari Breeze

    // Nota Dinas Elektronik
    Route::prefix('nde')->name('shared.nde.')->group(function () {
        Route::get('/', [NotaDinasController::class, 'index'])->name('index');
        Route::get('/create', [NotaDinasController::class, 'create'])->name('create');
        Route::post('/', [NotaDinasController::class, 'store'])->name('store');
        Route::get('/{id}', [NotaDinasController::class, 'show'])->name('show');
        Route::get('/{id}/download', [NotaDinasController::class, 'download'])->name('download');
    });

    // Grup untuk route yang memerlukan peran Waka Kesiswaan
    Route::middleware(['role:Waka Kesiswaan'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Route untuk Perizinan
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
    Route::put('/izin/{perizinan}', [IzinController::class, 'update'])->name('izin.update');
    Route::delete('/izin/{perizinan}', [IzinController::class, 'destroy'])->name('izin.destroy');

    // Grup Route untuk Wali Kelas
    Route::middleware(['role:Wali Kelas'])->prefix('wali-kelas')->name('wali-kelas.')->group(function () {
        Route::get('/dashboard', [WaliKelasDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/perizinan', [WaliKelasPerizinanController::class, 'index'])->name('perizinan.index');
        Route::patch('/perizinan/{perizinan}/approve', [WaliKelasPerizinanController::class, 'approve'])->name('perizinan.approve');
        Route::patch('/perizinan/{perizinan}/reject', [WaliKelasPerizinanController::class, 'reject'])->name('perizinan.reject');
        // Nanti kita tambahkan route untuk approve & reject di sini
    });

    // Grup untuk Data Master, bisa diakses oleh Waka Kesiswaan/Admin
    Route::middleware(['role:Waka Kesiswaan'])->prefix('master-data')->name('master-data.')->group(function () {
        Route::resource('kelas', KelasController::class);

        Route::post('siswa/generate-akun-masal', [MasterSiswaController::class, 'generateAkunMasal'])->name('siswa.generate-akun-masal'); // <-- Route Generate Masal
        Route::post('siswa/{master_siswa}/generate-akun', [MasterSiswaController::class, 'generateAkun'])->name('siswa.generate-akun');
        Route::post('siswa/{master_siswa}/reset-password', [MasterSiswaController::class, 'resetPassword'])->name('siswa.reset-password'); // <-- Route Reset Password
        Route::resource('siswa', MasterSiswaController::class);

        Route::post('rombel/{rombel}/add-siswa', [RombelController::class, 'addSiswa'])->name('rombel.add-siswa');
        Route::delete('rombel/{rombel}/remove-siswa/{siswa}', [RombelController::class, 'removeSiswa'])->name('rombel.remove-siswa');
        Route::resource('rombel', RombelController::class);
        Route::post('/siswa/import', [MasterSiswaController::class, 'import'])->name('siswa.import');

        // Route Tahun Pelajaran
        Route::resource('tahun-pelajaran', TahunPelajaranController::class)->except(['create', 'edit', 'show']);
        Route::patch('tahun-pelajaran/{tahunPelajaran}/activate', [TahunPelajaranController::class, 'activate'])->name('tahun-pelajaran.activate');
    });

    // Grup Route untuk Kesiswaan
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function () {
        // Shared routes for Waka Kesiswaan and Guru BK
        Route::middleware(['role:Waka Kesiswaan|Guru BK'])->group(function () {
            Route::post('poin-peraturan/category', [\App\Http\Controllers\Kesiswaan\PoinPeraturanController::class, 'storeCategory'])->name('poin-peraturan.storeCategory');
            Route::resource('poin-peraturan', \App\Http\Controllers\Kesiswaan\PoinPeraturanController::class);
            Route::resource('input-pelanggaran', \App\Http\Controllers\Kesiswaan\PelanggaranSiswaController::class);
            Route::resource('input-prestasi', \App\Http\Controllers\Kesiswaan\PrestasiSiswaController::class);
            Route::resource('input-pemutihan', \App\Http\Controllers\Kesiswaan\PemutihanPoinController::class);
            Route::patch('input-pemutihan/{pemutihan}/approve', [\App\Http\Controllers\Kesiswaan\PemutihanPoinController::class, 'approve'])->name('input-pemutihan.approve');
            Route::patch('input-pemutihan/{pemutihan}/reject', [\App\Http\Controllers\Kesiswaan\PemutihanPoinController::class, 'reject'])->name('input-pemutihan.reject');
        });

        // Accessible by all authenticated users (students can print their own)
        Route::get('input-pemutihan/{pemutihan}/print', [\App\Http\Controllers\Kesiswaan\PemutihanPoinController::class, 'printPdf'])->name('input-pemutihan.print');

        // Waka Kesiswaan specific routes
        Route::middleware(['role:Waka Kesiswaan'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
            Route::get('/monitoring-izin', [MonitoringIzinController::class, 'index'])->name('monitoring-izin.index');
            Route::get('/riwayat-izin-keluar', [MonitoringIzinController::class, 'riwayatIzinKeluar'])->name('riwayat-izin-keluar.index');

            // Route untuk Persetujuan Dispensasi
            Route::get('/persetujuan-dispensasi', [PersetujuanDispensasiController::class, 'index'])->name('persetujuan-dispensasi.index');
            Route::get('/persetujuan-dispensasi/{dispensasi}', [PersetujuanDispensasiController::class, 'show'])->name('persetujuan-dispensasi.show');
            Route::patch('/persetujuan-dispensasi/{dispensasi}/approve', [PersetujuanDispensasiController::class, 'approve'])->name('persetujuan-dispensasi.approve');
            Route::patch('/persetujuan-dispensasi/{dispensasi}/reject', [PersetujuanDispensasiController::class, 'reject'])->name('persetujuan-dispensasi.reject');
            Route::get('/persetujuan-dispensasi/{dispensasi}/print', [PersetujuanDispensasiController::class, 'printPdf'])->name('persetujuan-dispensasi.print');

            // Route Pengaduan untuk Admin Kesiswaan
            Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan.index');
            Route::patch('/pengaduan/{pengaduan}/status', [PengaduanController::class, 'updateStatus'])->name('pengaduan.update-status');

            // Route Panggilan Orang Tua (Approval & Management)
            Route::get('panggilan-ortu', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'index'])->name('panggilan-ortu.index');
            Route::post('panggilan-ortu', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'store'])->name('panggilan-ortu.store');
            Route::patch('panggilan-ortu/{panggilan}/status', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'updateStatus'])->name('panggilan-ortu.update-status');
            Route::patch('panggilan-ortu/{panggilan}/approve', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'approve'])->name('panggilan-ortu.approve');
            Route::patch('panggilan-ortu/{panggilan}/reject', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'reject'])->name('panggilan-ortu.reject');
            Route::delete('panggilan-ortu/{panggilan}', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'destroy'])->name('panggilan-ortu.destroy');

            // Monitoring BK untuk Waka Kesiswaan
            Route::get('monitoring-bk/pembinaan', [\App\Http\Controllers\BK\PembinaanRutinController::class, 'index'])->name('monitoring-bk.pembinaan');
            Route::get('monitoring-bk/konsultasi', [\App\Http\Controllers\BK\KonsultasiJadwalController::class, 'index'])->name('monitoring-bk.konsultasi');
        });
    });

    // Route Panggilan yang bisa diakses Siswa (Hanya Cetak)
    Route::get('kesiswaan/panggilan-ortu/{panggilan}/print', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'printPdf'])
        ->middleware(['auth'])
        ->name('kesiswaan.panggilan-ortu.print');

    // Grup Route untuk Siswa
    Route::middleware(['role:Siswa|siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard.index');

        // Route untuk Izin Meninggalkan Kelas
        Route::get('/izin-keluar-kelas', [IzinMeninggalkanKelasController::class, 'index'])->name('izin-keluar-kelas.index');
        Route::post('/izin-keluar-kelas', [IzinMeninggalkanKelasController::class, 'store'])->name('izin-keluar-kelas.store');

        // Route Riwayat Catatan (Pelanggaran & Keterlambatan)
        Route::get('/riwayat-catatan', [\App\Http\Controllers\Siswa\RiwayatCatatanController::class, 'index'])->name('riwayat-catatan.index');

        // Route untuk Jurnal Prakerin
        Route::get('/jurnal-prakerin', [JurnalSiswaController::class, 'index'])->name('jurnal-prakerin.index');
        Route::post('/jurnal-prakerin', [JurnalSiswaController::class, 'store'])->name('jurnal-prakerin.store');

        // Route BK Siswa
        Route::get('/bk', [\App\Http\Controllers\Siswa\BKController::class, 'index'])->name('bk.index');
        Route::post('/bk/jadwal', [\App\Http\Controllers\Siswa\BKController::class, 'storeJadwal'])->name('bk.jadwal.store');
        
        // Chat
        Route::get('/chat', [\App\Http\Controllers\BK\ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/start/{guru}', [\App\Http\Controllers\BK\ChatController::class, 'startChat'])->name('chat.start');

        // ISO Docs
        Route::get('/bk/konsultasi/{jadwal}/print-jadwal', [\App\Http\Controllers\BK\ConsultationDocController::class, 'printSchedule'])->name('bk.konsultasi.print-jadwal');
        Route::get('/bk/konsultasi/{jadwal}/print-report', [\App\Http\Controllers\BK\ConsultationDocController::class, 'printReport'])->name('bk.konsultasi.print-report');

        // Kartu Pelajar Digital
        Route::get('/kartu-pelajar', [\App\Http\Controllers\Siswa\KartuPelajarController::class, 'index'])->name('kartu-pelajar.index');
    });

    // Grup Route untuk Guru BK
    Route::middleware(['role:Guru BK'])->prefix('bk')->name('bk.')->group(function () {
        Route::get('/dashboard', [BKDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/monitoring-izin', [BKMonitoringController::class, 'index'])->name('monitoring.index');

        // Pembinaan Rutin
        Route::get('/pembinaan', [\App\Http\Controllers\BK\PembinaanRutinController::class, 'index'])->name('pembinaan.index');
        Route::post('/pembinaan', [\App\Http\Controllers\BK\PembinaanRutinController::class, 'store'])->name('pembinaan.store');
        Route::delete('/pembinaan/{pembinaan}', [\App\Http\Controllers\BK\PembinaanRutinController::class, 'destroy'])->name('pembinaan.destroy');

        // Jadwal Konsultasi
        Route::get('/konsultasi', [\App\Http\Controllers\BK\KonsultasiJadwalController::class, 'index'])->name('konsultasi.index');
        Route::post('/konsultasi/store-bk', [\App\Http\Controllers\BK\KonsultasiJadwalController::class, 'storeByBK'])->name('konsultasi.store-bk');
        Route::patch('/konsultasi/{jadwal}/status', [\App\Http\Controllers\BK\KonsultasiJadwalController::class, 'updateStatus'])->name('konsultasi.update-status');

        // Chat
        Route::get('/chat', [\App\Http\Controllers\BK\ChatController::class, 'index'])->name('chat.index');

        // ISO Docs
        Route::get('/konsultasi/{jadwal}/print-jadwal', [\App\Http\Controllers\BK\ConsultationDocController::class, 'printSchedule'])->name('konsultasi.print-jadwal');
        Route::get('/konsultasi/{jadwal}/print-report', [\App\Http\Controllers\BK\ConsultationDocController::class, 'printReport'])->name('konsultasi.print-report');

        // Monitoring Pelanggaran & Keterlambatan
        Route::get('/monitoring-catatan', [\App\Http\Controllers\BK\MonitoringCatatanController::class, 'index'])->name('monitoring-catatan.index');
        Route::get('/monitoring-catatan/{siswa}', [\App\Http\Controllers\BK\MonitoringCatatanController::class, 'show'])->name('monitoring-catatan.show');
        Route::post('/panggilan-proposal', [\App\Http\Controllers\BK\PanggilanProposalController::class, 'store'])->name('panggilan-proposal.store');
    });

    // API Chat (Shared)
    Route::middleware(['auth'])->prefix('api/chat')->name('api.chat.')->group(function() {
        Route::get('/unread-count', [\App\Http\Controllers\BK\ChatController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/rooms/{room}', [\App\Http\Controllers\BK\ChatController::class, 'show'])->name('show');
        Route::post('/rooms/{room}/send', [\App\Http\Controllers\BK\ChatController::class, 'sendMessage'])->name('send');
    });

    // Grup Route untuk Guru Piket
    Route::middleware(['role:Guru Piket'])->prefix('piket')->name('piket.')->group(function () {
        Route::get('/dashboard', [PiketDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/monitoring-izin', [PiketMonitoringController::class, 'index'])->name('monitoring.index');

        // Route untuk Persetujuan Izin Meninggalkan Kelas
        Route::get('/persetujuan-izin-keluar', [PiketPersetujuanIzinKeluarController::class, 'index'])->name('persetujuan-izin-keluar.index');
        Route::get('/riwayat-izin-keluar', [PiketPersetujuanIzinKeluarController::class, 'riwayat'])->name('persetujuan-izin-keluar.riwayat');
        Route::patch('/persetujuan-izin-keluar/{izin}/approve', [PiketPersetujuanIzinKeluarController::class, 'approve'])->name('persetujuan-izin-keluar.approve');
        Route::patch('/persetujuan-izin-keluar/{izin}/reject', [PiketPersetujuanIzinKeluarController::class, 'reject'])->name('persetujuan-izin-keluar.reject');
        Route::get('/persetujuan-izin-keluar/{izin}/print', [PiketPersetujuanIzinKeluarController::class, 'printPdf'])->name('persetujuan-izin-keluar.print');

        // Route untuk Penanganan Keterlambatan
        Route::get('/penanganan-terlambat', [PenangananTerlambatController::class, 'index'])->name('penanganan-terlambat.index');
        Route::post('/penanganan-terlambat', [PenangananTerlambatController::class, 'store'])->name('penanganan-terlambat.store');
        Route::get('/penanganan-terlambat/{keterlambatan}/print', [PenangananTerlambatController::class, 'printPdf'])->name('penanganan-terlambat.print');

        // Route untuk Verifikasi Keterlambatan
        Route::get('/verifikasi-terlambat', [VerifikasiTerlambatController::class, 'index'])->name('verifikasi-terlambat.index');
        Route::get('/verifikasi-terlambat/{keterlambatan}', [VerifikasiTerlambatController::class, 'show'])->name('verifikasi-terlambat.show');
        Route::put('/verifikasi-terlambat/{keterlambatan}', [VerifikasiTerlambatController::class, 'update'])->name('verifikasi-terlambat.update');

        // Route untuk Absensi Guru
        Route::get('/absensi-guru', [\App\Http\Controllers\Piket\AbsensiGuruController::class, 'index'])->name('absensi-guru.index');
        Route::post('/absensi-guru', [\App\Http\Controllers\Piket\AbsensiGuruController::class, 'store'])->name('absensi-guru.store');
        Route::patch('/absensi-guru/{id}', [\App\Http\Controllers\Piket\AbsensiGuruController::class, 'update'])->name('absensi-guru.update');
    });

    // Route ini kita namakan 'api.siswa.search' sesuai panggilan di JavaScript
    Route::get('/api/siswa/search', [PenangananTerlambatController::class, 'search'])
        ->middleware(['auth']) // Pastikan hanya user login yang bisa akses
        ->name('api.siswa.search');

    // Grup Route untuk Kurikulum
    Route::middleware(['role:Kurikulum'])->prefix('kurikulum')->name('kurikulum.')->group(function () {
        Route::get('/dashboard', [KurikulumDashboardController::class, 'index'])->name('dashboard.index');
        Route::resource('mata-pelajaran', MataPelajaranController::class);
        Route::post('master-guru/{master_guru}/generate-akun', [MasterGuruController::class, 'generateAkun'])->name('master-guru.generate-akun');
        Route::resource('master-guru', MasterGuruController::class);

        // Monitoring Absensi Guru
        Route::get('/monitoring-absensi-guru', [\App\Http\Controllers\Kurikulum\MonitoringAbsensiGuruController::class, 'index'])->name('monitoring-absensi-guru.index');
        Route::get('/monitoring-absensi-guru/export', [\App\Http\Controllers\Kurikulum\MonitoringAbsensiGuruController::class, 'export'])->name('monitoring-absensi-guru.export');

        // Monitoring Absensi Per Kelas
        Route::get('/monitoring-absensi-per-kelas', [\App\Http\Controllers\Kurikulum\MonitoringAbsensiPerKelasController::class, 'index'])->name('monitoring-absensi-per-kelas.index');
        Route::get('/monitoring-absensi-per-kelas/export', [\App\Http\Controllers\Kurikulum\MonitoringAbsensiPerKelasController::class, 'export'])->name('monitoring-absensi-per-kelas.export');
        Route::get('jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('jadwal-pelajaran.index');
        Route::get('jadwal-pelajaran/export-pdf', [JadwalPelajaranController::class, 'exportPdf'])->name('jadwal-pelajaran.export-pdf');
        Route::get('jadwal-pelajaran/{rombel}', [JadwalPelajaranController::class, 'show'])->name('jadwal-pelajaran.show');
        Route::post('jadwal-pelajaran/{rombel}', [JadwalPelajaranController::class, 'store'])->name('jadwal-pelajaran.store');

        //Route untuk Jam Pelajaran
        Route::resource('jam-pelajaran', JamPelajaranController::class);

        Route::post('mata-pelajaran/import', [MataPelajaranController::class, 'import'])->name('mata-pelajaran.import');
        Route::post('master-guru/import', [MasterGuruController::class, 'import'])->name('master-guru.import');
    });

    // Grup Route untuk Guru Kelas
    Route::middleware(['role:Guru Kelas'])->prefix('guru-kelas')->name('guru-kelas.')->group(function () {
        Route::get('/dashboard', [GuruKelasDashboardController::class, 'index'])->name('dashboard.index');

        // Route untuk Persetujuan Izin Meninggalkan Kelas
        Route::get('/persetujuan-izin-keluar', [PersetujuanIzinKeluarController::class, 'index'])->name('persetujuan-izin-keluar.index');
        Route::get('/riwayat-izin-keluar', [PersetujuanIzinKeluarController::class, 'riwayat'])->name('persetujuan-izin-keluar.riwayat');
        Route::patch('/persetujuan-izin-keluar/{izin}/approve', [PersetujuanIzinKeluarController::class, 'approve'])->name('persetujuan-izin-keluar.approve');
        Route::patch('/persetujuan-izin-keluar/{izin}/reject', [PersetujuanIzinKeluarController::class, 'reject'])->name('persetujuan-izin-keluar.reject');

        // Route baru untuk verifikasi keterlambatan via scan
        Route::get('/verifikasi-terlambat/scan/{uuid}', [GuruKelasVerifikasiTerlambatController::class, 'scanAndVerify'])->name('verifikasi-terlambat.scan');
    });

    // Grup Route untuk Guru (Submission)
    Route::middleware(['role:Guru Kelas'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/izin', [\App\Http\Controllers\Guru\IzinGuruController::class, 'index'])->name('izin.index');
        Route::get('/izin/create', [\App\Http\Controllers\Guru\IzinGuruController::class, 'create'])->name('izin.create');
        Route::post('/izin', [\App\Http\Controllers\Guru\IzinGuruController::class, 'store'])->name('izin.store');
        Route::get('/izin/schedules', [\App\Http\Controllers\Guru\IzinGuruController::class, 'getSchedules'])->name('izin.schedules');
    });

    // Grup Route untuk Piket (Approval Stage 1)
    Route::middleware(['role:Guru Piket'])->prefix('piket')->name('piket.')->group(function () {
        Route::get('/persetujuan-izin-guru', [\App\Http\Controllers\Piket\PersetujuanIzinGuruController::class, 'index'])->name('persetujuan-izin-guru.index');
        Route::patch('/persetujuan-izin-guru/{izin}/approve', [\App\Http\Controllers\Piket\PersetujuanIzinGuruController::class, 'approve'])->name('persetujuan-izin-guru.approve');
        Route::patch('/persetujuan-izin-guru/{izin}/reject', [\App\Http\Controllers\Piket\PersetujuanIzinGuruController::class, 'reject'])->name('persetujuan-izin-guru.reject');
    });

    // Grup Route untuk Kurikulum (Approval Stage 2)
    Route::middleware(['role:Kurikulum'])->prefix('kurikulum')->name('kurikulum.')->group(function () {
        Route::get('/persetujuan-izin-guru', [\App\Http\Controllers\Kurikulum\PersetujuanIzinGuruController::class, 'index'])->name('persetujuan-izin-guru.index');
        Route::patch('/persetujuan-izin-guru/{izin}/approve', [\App\Http\Controllers\Kurikulum\PersetujuanIzinGuruController::class, 'approve'])->name('persetujuan-izin-guru.approve');
        Route::patch('/persetujuan-izin-guru/{izin}/reject', [\App\Http\Controllers\Kurikulum\PersetujuanIzinGuruController::class, 'reject'])->name('persetujuan-izin-guru.reject');
    });

    // Grup Route untuk SDM (Approval Stage 3 - Final)
    Route::middleware(['role:KAUR SDM'])->prefix('sdm')->name('sdm.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SDM\DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/persetujuan-izin-guru', [\App\Http\Controllers\SDM\PersetujuanIzinGuruController::class, 'index'])->name('persetujuan-izin-guru.index');
        Route::patch('/persetujuan-izin-guru/{izin}/approve', [\App\Http\Controllers\SDM\PersetujuanIzinGuruController::class, 'approve'])->name('persetujuan-izin-guru.approve');
        Route::patch('/persetujuan-izin-guru/{izin}/reject', [\App\Http\Controllers\SDM\PersetujuanIzinGuruController::class, 'reject'])->name('persetujuan-izin-guru.reject');

        // Monitoring & Rekapitulasi
        Route::get('/monitoring', [\App\Http\Controllers\SDM\DashboardController::class, 'monitoring'])->name('monitoring.index');
        Route::get('/rekapitulasi', [\App\Http\Controllers\SDM\RekapitulasiController::class, 'index'])->name('rekapitulasi.index');
        Route::get('/rekapitulasi/export-excel', [\App\Http\Controllers\SDM\RekapitulasiController::class, 'exportExcel'])->name('rekapitulasi.export-excel');
        Route::get('/rekapitulasi/export-pdf', [\App\Http\Controllers\SDM\RekapitulasiController::class, 'exportPdf'])->name('rekapitulasi.export-pdf');

        // NDE Referensi
        Route::resource('nde-referensi', NdeReferensiController::class)->except(['create', 'edit', 'show']);
    });

    // Public/Shared print route for approved permits
    Route::get('/sdm/persetujuan-izin-guru/{izin}/print', [\App\Http\Controllers\SDM\PersetujuanIzinGuruController::class, 'printPdf'])
        ->middleware(['role:KAUR SDM|Guru Kelas'])
        ->name('sdm.persetujuan-izin-guru.print');

    // Grup Route untuk Security
    Route::middleware(['role:Security'])->prefix('security')->name('security.')->group(function () {
        Route::get('/verifikasi-izin', [SecurityVerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/riwayat-izin', [SecurityVerifikasiController::class, 'riwayat'])->name('verifikasi.riwayat');
        Route::get('/scan-qr', [SecurityVerifikasiController::class, 'scanQr'])->name('verifikasi.scan');
        Route::get('/verifikasi-via-scan/{uuid}', [SecurityVerifikasiController::class, 'showScanResult'])->name('verifikasi.show-scan');

        // Route baru untuk aksi verifikasi keluar & cetak otomatis
        Route::get('/verifikasi-via-scan/{uuid}', [SecurityVerifikasiController::class, 'showScanResult'])->name('verifikasi.show-scan');
        Route::get('/verifikasi-via-scan/{uuid}/process', [SecurityVerifikasiController::class, 'processScanAction'])->name('verifikasi.process-scan');

        Route::patch('/verifikasi-izin/{izin}/keluar', [SecurityVerifikasiController::class, 'verifyKeluar'])->name('verifikasi.keluar');
        Route::patch('/verifikasi-izin/{izin}/kembali', [SecurityVerifikasiController::class, 'verifyKembali'])->name('verifikasi.kembali');
        Route::get('/verifikasi-izin/{izin}/print', [SecurityVerifikasiController::class, 'printPdf'])->name('verifikasi.print');

        // Route untuk Pendataan Keterlambatan
        Route::get('/pendataan-terlambat', [PendataanTerlambatController::class, 'index'])->name('pendataan-terlambat.index');
        Route::post('/pendataan-terlambat', [PendataanTerlambatController::class, 'store'])->name('pendataan-terlambat.store');
    });

    // Grup Route untuk Pengajuan Dispensasi (bisa diakses beberapa peran)
    Route::middleware(['auth'])->prefix('dispensasi')->name('dispensasi.')->group(function () {
        Route::get('/pengajuan', [PengajuanDispensasiController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/create', [PengajuanDispensasiController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanDispensasiController::class, 'store'])->name('pengajuan.store');
    });

    // Grup Route untuk Prakerin
    Route::middleware(['role:Koordinator Prakerin'])->prefix('prakerin')->name('prakerin.')->group(function () {
        Route::resource('industri', IndustriController::class);
        Route::resource('penempatan', PenempatanController::class);
    });

    // Grup Route untuk Guru Pembimbing (bisa diakses Guru Kelas)
    Route::middleware(['role:Guru Kelas'])->prefix('pembimbing-prakerin')->name('pembimbing-prakerin.')->group(function () {
        Route::get('/monitoring', [MonitoringPembimbingController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/{penempatan}', [MonitoringPembimbingController::class, 'show'])->name('monitoring.show');
        Route::patch('/monitoring/jurnal/{jurnal}', [MonitoringPembimbingController::class, 'updateJurnal'])->name('monitoring.updateJurnal');
    });

    // Grup Route untuk Super Admin
    Route::middleware(['role:Super Admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Change Log
    Route::get('/changelog', [\App\Http\Controllers\Shared\ChangeLogController::class, 'index'])->name('changelog.index');
});

require __DIR__ . '/auth.php';
