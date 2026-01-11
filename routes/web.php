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
use App\Http\Controllers\Admin\PermissionManagementController;
use App\Http\Controllers\SDM\NdeReferensiController;
use App\Http\Controllers\Shared\NotaDinasController;
use App\Http\Controllers\MasterData\DapodikSiswaController;
use App\Http\Controllers\Operator\DapodikManagementController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Kurikulum\DistribusiMapelController;
use App\Http\Controllers\Kurikulum\AnalisaKurikulumController;
use App\Http\Controllers\Security\GateTerminalController;
use App\Http\Controllers\Auth\SecurityLoginController;
use App\Http\Controllers\WaliKelas\WaliKelasMentoringController;
use App\Http\Controllers\BK\BKPembinaanTerlambatController;
use App\Http\Controllers\Shared\CoachingAnalyticsController;

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/verifikasi/surat/{uuid}', [VerifikasiController::class, 'show'])->name('verifikasi.surat');
Route::get('/verifikasi/kartu/{nis}', [VerifikasiController::class, 'kartuPelajar'])->name('verifikasi.kartu');

// Legal Pages
Route::get('/privacy', function () { return view('pages.legal.privacy'); })->name('privacy');
Route::get('/terms', function () { return view('pages.legal.terms'); })->name('terms');
Route::get('/security', function () { return view('pages.legal.security'); })->name('security');

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

// Documentation Routes (Public)
Route::group(['prefix' => 'panduan', 'as' => 'docs.'], function () {
    Route::get('/', [App\Http\Controllers\DocumentationController::class, 'index'])->name('index');
    Route::get('/guru-piket', [App\Http\Controllers\DocumentationController::class, 'piket'])->name('piket');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:Waka Kesiswaan|Super Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('roles', RoleController::class)->middleware('permission:view roles')->except(['create', 'edit', 'show']);
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

    // Grup untuk route yang memerlukan peran Waka Kesiswaan atau Super Admin
    Route::middleware(['role:Waka Kesiswaan|Super Admin'])->group(function () {
        Route::get('users/export-excel', [UserController::class, 'exportExcel'])->name('users.export-excel');
        Route::get('users/export-pdf', [UserController::class, 'exportPdf'])->name('users.export-pdf');
        Route::resource('users', UserController::class)->middleware('permission:view users');
    });

    // Route untuk Perizinan
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
    Route::put('/izin/{perizinan}', [IzinController::class, 'update'])->name('izin.update');
    Route::delete('/izin/{perizinan}', [IzinController::class, 'destroy'])->name('izin.destroy');

    // Grup Route untuk Wali Kelas
    Route::middleware(['role:Wali Kelas', 'permission:view wali kelas dashboard'])->prefix('wali-kelas')->name('wali-kelas.')->group(function () {
        Route::get('/dashboard', [WaliKelasDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/perizinan', [WaliKelasPerizinanController::class, 'index'])->middleware('permission:manage perizinan wali kelas')->name('perizinan.index');
        Route::patch('/perizinan/{perizinan}/approve', [WaliKelasPerizinanController::class, 'approve'])->middleware('permission:manage perizinan wali kelas')->name('perizinan.approve');
        Route::patch('/perizinan/{perizinan}/reject', [WaliKelasPerizinanController::class, 'reject'])->middleware('permission:manage perizinan wali kelas')->name('perizinan.reject');
        Route::post('/keterlambatan/{keterlambatan}/mentoring', [WaliKelasMentoringController::class, 'store'])->middleware('permission:manage mentoring wali kelas')->name('keterlambatan.mentoring');
    });

    // Route bersama untuk coaching & analisa (Wali Kelas, BK, Waka Kesiswaan)
    Route::middleware(['auth', 'permission:view coaching analytics'])->group(function() {
        Route::get('/coaching-analytics', [CoachingAnalyticsController::class, 'index'])->name('coaching-analytics.index');
        Route::get('/wali-kelas/keterlambatan/{keterlambatan}/coaching-pdf', [WaliKelasMentoringController::class, 'downloadCoaching'])->name('wali-kelas.keterlambatan.coaching-pdf');
        Route::get('/bk/keterlambatan/{keterlambatan}/coaching-pdf', [BKPembinaanTerlambatController::class, 'downloadCoaching'])->name('bk.keterlambatan.coaching-pdf');
    });

    // Grup untuk Data Master, bisa diakses oleh Waka Kesiswaan/Operator
    Route::middleware(['role:Waka Kesiswaan|Operator|Super Admin'])->prefix('master-data')->name('master-data.')->group(function () {
        Route::resource('kelas', KelasController::class)->middleware('permission:manage kelas');

        Route::post('siswa/generate-akun-masal', [MasterSiswaController::class, 'generateAkunMasal'])->name('siswa.generate-akun-masal'); // <-- Route Generate Masal
        Route::post('siswa/{master_siswa}/generate-akun', [MasterSiswaController::class, 'generateAkun'])->name('siswa.generate-akun');
        Route::post('siswa/{master_siswa}/reset-password', [MasterSiswaController::class, 'resetPassword'])->name('siswa.reset-password'); // <-- Route Reset Password
        Route::resource('siswa', MasterSiswaController::class)->middleware('permission:manage siswa');
        
        // Dapodik Siswa Routes
        Route::get('siswa/{siswa}/dapodik', [DapodikSiswaController::class, 'show'])->name('siswa.dapodik.show');
        Route::get('siswa/{siswa}/dapodik/edit', [DapodikSiswaController::class, 'edit'])->name('siswa.dapodik.edit');
        Route::put('siswa/{siswa}/dapodik', [DapodikSiswaController::class, 'update'])->name('siswa.dapodik.update');

        Route::post('rombel/{rombel}/add-siswa', [RombelController::class, 'addSiswa'])->name('rombel.add-siswa');
        Route::delete('rombel/{rombel}/remove-siswa/{siswa}', [RombelController::class, 'removeSiswa'])->name('rombel.remove-siswa');
        Route::resource('rombel', RombelController::class)->middleware('permission:manage rombel');
        Route::post('/siswa/import', [MasterSiswaController::class, 'import'])->name('siswa.import');

        // Route Tahun Pelajaran
        Route::resource('tahun-pelajaran', TahunPelajaranController::class)->middleware('permission:manage tahun pelajaran')->except(['create', 'edit', 'show']);
        Route::patch('tahun-pelajaran/{tahunPelajaran}/activate', [TahunPelajaranController::class, 'activate'])->name('tahun-pelajaran.activate');
    });

    // Grup Route untuk Operator
    Route::middleware(['role:Operator', 'permission:view operator dashboard'])->prefix('operator')->name('operator.')->group(function () {
        Route::get('/dashboard', [OperatorDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dapodik', [DapodikManagementController::class, 'index'])->middleware('permission:manage dapodik')->name('dapodik.index');
        Route::post('/dapodik/import', [DapodikManagementController::class, 'import'])->middleware('permission:manage dapodik')->name('dapodik.import');
        Route::post('/dapodik/sync', [DapodikManagementController::class, 'sync'])->middleware('permission:manage dapodik')->name('dapodik.sync');
        Route::get('/dapodik/template', [DapodikManagementController::class, 'downloadTemplate'])->middleware('permission:manage dapodik')->name('dapodik.template');

        // Dapodik Submissions (Operator Approval)
        Route::get('/dapodik/submissions', [\App\Http\Controllers\Operator\DapodikSubmissionController::class, 'index'])->middleware('permission:manage dapodik')->name('dapodik.submissions.index');
        Route::get('/dapodik/submissions/{submission}', [\App\Http\Controllers\Operator\DapodikSubmissionController::class, 'show'])->middleware('permission:manage dapodik')->name('dapodik.submissions.show');
        Route::patch('/dapodik/submissions/{submission}/approve', [\App\Http\Controllers\Operator\DapodikSubmissionController::class, 'approve'])->middleware('permission:manage dapodik')->name('dapodik.submissions.approve');
        Route::patch('/dapodik/submissions/{submission}/reject', [\App\Http\Controllers\Operator\DapodikSubmissionController::class, 'reject'])->middleware('permission:manage dapodik')->name('dapodik.submissions.reject');
    });

    // Grup Route untuk Kesiswaan
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function () {
        // Shared routes for Waka Kesiswaan and Guru BK
        Route::middleware(['role:Waka Kesiswaan|Guru BK'])->group(function () {
            Route::post('poin-peraturan/category', [\App\Http\Controllers\Kesiswaan\PoinPeraturanController::class, 'storeCategory'])->middleware('permission:manage poin pelanggaran')->name('poin-peraturan.storeCategory');
            Route::resource('poin-peraturan', \App\Http\Controllers\Kesiswaan\PoinPeraturanController::class)->middleware([
                'index' => 'permission:manage poin pelanggaran',
                'create' => 'permission:manage poin pelanggaran',
                'store' => 'permission:manage poin pelanggaran',
                'edit' => 'permission:manage poin pelanggaran',
                'update' => 'permission:manage poin pelanggaran',
                'destroy' => 'permission:manage poin pelanggaran',
            ]);
            Route::resource('input-pelanggaran', \App\Http\Controllers\Kesiswaan\PelanggaranSiswaController::class)->middleware(['permission:manage poin pelanggaran']);
            Route::resource('input-prestasi', \App\Http\Controllers\Kesiswaan\PrestasiSiswaController::class)->middleware(['permission:manage poin prestasi']);
            Route::resource('input-pemutihan', \App\Http\Controllers\Kesiswaan\PemutihanPoinController::class)->middleware(['permission:manage pemutihan poin']);
            Route::patch('input-pemutihan/{pemutihan}/approve', [\App\Http\Controllers\Kesiswaan\PemutihanPoinController::class, 'approve'])->middleware('permission:manage pemutihan poin')->name('input-pemutihan.approve');
            Route::patch('input-pemutihan/{pemutihan}/reject', [\App\Http\Controllers\Kesiswaan\PemutihanPoinController::class, 'reject'])->middleware('permission:manage pemutihan poin')->name('input-pemutihan.reject');
        });

        // Accessible by all authenticated users (students can print their own)
        Route::get('input-pemutihan/{pemutihan}/print', [\App\Http\Controllers\Kesiswaan\PemutihanPoinController::class, 'printPdf'])->name('input-pemutihan.print');

        // Waka Kesiswaan specific routes
        Route::middleware(['role:Waka Kesiswaan'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:view kesiswaan dashboard')->name('dashboard.index');
            Route::get('/monitoring-izin', [MonitoringIzinController::class, 'index'])->middleware('permission:monitoring izin')->name('monitoring-izin.index');
            Route::get('/riwayat-izin-keluar', [MonitoringIzinController::class, 'riwayatIzinKeluar'])->middleware('permission:monitoring izin')->name('riwayat-izin-keluar.index');

            // Route untuk Persetujuan Dispensasi
            Route::get('/persetujuan-dispensasi', [PersetujuanDispensasiController::class, 'index'])->middleware('permission:manage dispensasi')->name('persetujuan-dispensasi.index');
            Route::get('/persetujuan-dispensasi/{dispensasi}', [PersetujuanDispensasiController::class, 'show'])->middleware('permission:manage dispensasi')->name('persetujuan-dispensasi.show');
            Route::patch('/persetujuan-dispensasi/{dispensasi}/approve', [PersetujuanDispensasiController::class, 'approve'])->middleware('permission:manage dispensasi')->name('persetujuan-dispensasi.approve');
            Route::patch('/persetujuan-dispensasi/{dispensasi}/reject', [PersetujuanDispensasiController::class, 'reject'])->middleware('permission:manage dispensasi')->name('persetujuan-dispensasi.reject');
            Route::get('/persetujuan-dispensasi/{dispensasi}/print', [PersetujuanDispensasiController::class, 'printPdf'])->middleware('permission:manage dispensasi')->name('persetujuan-dispensasi.print');

            // Route Pengaduan untuk Admin Kesiswaan
            Route::get('/pengaduan', [PengaduanController::class, 'index'])->middleware('permission:manage panggilan ortu')->name('pengaduan.index');
            Route::patch('/pengaduan/{pengaduan}/status', [PengaduanController::class, 'updateStatus'])->middleware('permission:manage panggilan ortu')->name('pengaduan.update-status');

            // Route Panggilan Orang Tua (Approval & Management)
            Route::get('panggilan-ortu', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'index'])->middleware('permission:manage panggilan ortu')->name('panggilan-ortu.index');
            Route::post('panggilan-ortu', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'store'])->middleware('permission:manage panggilan ortu')->name('panggilan-ortu.store');
            Route::patch('panggilan-ortu/{panggilan}/status', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'updateStatus'])->middleware('permission:manage panggilan ortu')->name('panggilan-ortu.update-status');
            Route::patch('panggilan-ortu/{panggilan}/approve', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'approve'])->middleware('permission:manage panggilan ortu')->name('panggilan-ortu.approve');
            Route::patch('panggilan-ortu/{panggilan}/reject', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'reject'])->middleware('permission:manage panggilan ortu')->name('panggilan-ortu.reject');
            Route::delete('panggilan-ortu/{panggilan}', [\App\Http\Controllers\Kesiswaan\PanggilanOrangTuaController::class, 'destroy'])->middleware('permission:manage panggilan ortu')->name('panggilan-ortu.destroy');

            // Monitoring BK untuk Waka Kesiswaan
            Route::get('monitoring-bk/pembinaan', [\App\Http\Controllers\BK\PembinaanRutinController::class, 'index'])->middleware('permission:manage pembinaan rutin')->name('monitoring-bk.pembinaan');
            Route::get('monitoring-bk/konsultasi', [\App\Http\Controllers\BK\KonsultasiJadwalController::class, 'index'])->middleware('permission:manage jadwal konsultasi')->name('monitoring-bk.konsultasi');

            // Route Stella Access Card
            Route::get('/kartu-akses', [\App\Http\Controllers\Kesiswaan\KartuAksesController::class, 'index'])->middleware('permission:manage kartu akses')->name('kartu-akses.index');
            Route::get('/kartu-akses/{siswa}', [\App\Http\Controllers\Kesiswaan\KartuAksesController::class, 'show'])->middleware('permission:manage kartu akses')->name('kartu-akses.show');
            Route::get('/kartu-akses/{siswa}/cetak', [\App\Http\Controllers\Kesiswaan\KartuAksesController::class, 'cetak'])->middleware('permission:manage kartu akses')->name('kartu-akses.cetak');
            Route::post('/kartu-akses/cetak-masal', [\App\Http\Controllers\Kesiswaan\KartuAksesController::class, 'cetakMasal'])->middleware('permission:manage kartu akses')->name('kartu-akses.cetak-masal');

            // Route Database Maintenance
            Route::get('/database', [\App\Http\Controllers\Kesiswaan\DatabaseController::class, 'index'])->middleware('permission:manage database maintenance')->name('database.index');
            Route::post('/database/backup', [\App\Http\Controllers\Kesiswaan\DatabaseController::class, 'backup'])->middleware('permission:manage database maintenance')->name('database.backup');
            Route::post('/database/restore', [\App\Http\Controllers\Kesiswaan\DatabaseController::class, 'restore'])->middleware('permission:manage database maintenance')->name('database.restore');
            Route::get('/database/download/{filename}', [\App\Http\Controllers\Kesiswaan\DatabaseController::class, 'download'])->middleware('permission:manage database maintenance')->name('database.download');
            Route::delete('/database/{filename}', [\App\Http\Controllers\Kesiswaan\DatabaseController::class, 'destroy'])->middleware('permission:manage database maintenance')->name('database.destroy');
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

        // Route Riwayat Keterlambatan Khusus
        Route::get('/riwayat-keterlambatan', [\App\Http\Controllers\Siswa\RiwayatKeterlambatanController::class, 'index'])->name('riwayat-keterlambatan.index');
        Route::get('/riwayat-keterlambatan/{keterlambatan}/print', [\App\Http\Controllers\Siswa\RiwayatKeterlambatanController::class, 'printPdf'])->name('riwayat-keterlambatan.print');

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

        // Data Dapodik
        Route::get('/dapodik', [\App\Http\Controllers\Siswa\DapodikSiswaController::class, 'index'])->name('dapodik.index');
        Route::get('/dapodik/edit', [\App\Http\Controllers\Siswa\DapodikSiswaController::class, 'edit'])->name('dapodik.edit');
        Route::post('/dapodik/submission', [\App\Http\Controllers\Siswa\DapodikSiswaController::class, 'storeSubmission'])->name('dapodik.store-submission');
        Route::get('/dapodik/submissions', [\App\Http\Controllers\Siswa\DapodikSiswaController::class, 'submissions'])->name('dapodik.submissions');

        // LMS Routes
        Route::prefix('lms')->name('lms.')->group(function () {
             Route::get('/', [App\Http\Controllers\Siswa\LmsController::class, 'index'])->name('index');
             Route::get('/course/{mapel}', [App\Http\Controllers\Siswa\LmsController::class, 'show'])->name('course.show');
             Route::get('/assignment/{assignment}', [App\Http\Controllers\Siswa\LmsController::class, 'showAssignment'])->name('assignment.show');
             Route::post('/assignment/{assignment}/submit', [App\Http\Controllers\Siswa\LmsController::class, 'storeSubmission'])->name('assignment.submit');
        });
    });

    // Grup Route untuk Guru BK
    Route::middleware(['role:Guru BK', 'permission:view bk dashboard'])->prefix('bk')->name('bk.')->group(function () {
        Route::get('/dashboard', [BKDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/monitoring-izin', [BKMonitoringController::class, 'index'])->name('monitoring.index');

        // Pembinaan Rutin
        Route::get('/pembinaan', [\App\Http\Controllers\BK\PembinaanRutinController::class, 'index'])->middleware('permission:manage pembinaan rutin')->name('pembinaan.index');
        Route::post('/pembinaan', [\App\Http\Controllers\BK\PembinaanRutinController::class, 'store'])->middleware('permission:manage pembinaan rutin')->name('pembinaan.store');
        Route::delete('/pembinaan/{pembinaan}', [\App\Http\Controllers\BK\PembinaanRutinController::class, 'destroy'])->middleware('permission:manage pembinaan rutin')->name('pembinaan.destroy');

        // Jadwal Konsultasi
        Route::get('/konsultasi', [\App\Http\Controllers\BK\KonsultasiJadwalController::class, 'index'])->middleware('permission:manage jadwal konsultasi')->name('konsultasi.index');
        Route::post('/konsultasi/store-bk', [\App\Http\Controllers\BK\KonsultasiJadwalController::class, 'storeByBK'])->middleware('permission:manage jadwal konsultasi')->name('konsultasi.store-bk');
        Route::patch('/konsultasi/{jadwal}/status', [\App\Http\Controllers\BK\KonsultasiJadwalController::class, 'updateStatus'])->middleware('permission:manage jadwal konsultasi')->name('konsultasi.update-status');

        // Chat
        Route::get('/chat', [\App\Http\Controllers\BK\ChatController::class, 'index'])->middleware('permission:view chat bk')->name('chat.index');

        // ISO Docs
        Route::get('/konsultasi/{jadwal}/print-jadwal', [\App\Http\Controllers\BK\ConsultationDocController::class, 'printSchedule'])->name('konsultasi.print-jadwal');
        Route::get('/konsultasi/{jadwal}/print-report', [\App\Http\Controllers\BK\ConsultationDocController::class, 'printReport'])->name('konsultasi.print-report');

        // Monitoring Pelanggaran & Keterlambatan
        Route::get('/monitoring-catatan', [\App\Http\Controllers\BK\MonitoringCatatanController::class, 'index'])->name('monitoring-catatan.index');
        Route::get('/monitoring-catatan/{siswa}', [\App\Http\Controllers\BK\MonitoringCatatanController::class, 'show'])->name('monitoring-catatan.show');
        Route::post('/keterlambatan/{keterlambatan}/pembinaan', [BKPembinaanTerlambatController::class, 'store'])->name('keterlambatan.pembinaan');
        Route::post('/panggilan-proposal', [\App\Http\Controllers\BK\PanggilanProposalController::class, 'store'])->name('panggilan-proposal.store');
    });

    // API Chat (Shared)
    Route::middleware(['auth'])->prefix('api/chat')->name('api.chat.')->group(function() {
        Route::get('/unread-count', [\App\Http\Controllers\BK\ChatController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/rooms/{room}', [\App\Http\Controllers\BK\ChatController::class, 'show'])->name('show');
        Route::post('/rooms/{room}/send', [\App\Http\Controllers\BK\ChatController::class, 'sendMessage'])->name('send');
    });

    // Grup Route untuk Guru Piket
    Route::middleware(['role:Guru Piket', 'permission:view piket dashboard'])->prefix('piket')->name('piket.')->group(function () {
        Route::get('/dashboard', [PiketDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/monitoring-izin', [PiketMonitoringController::class, 'index'])->name('monitoring.index');

        // Route untuk Persetujuan Izin Meninggalkan Kelas
        Route::get('/persetujuan-izin-keluar', [PiketPersetujuanIzinKeluarController::class, 'index'])->middleware('permission:manage perizinan siswa')->name('persetujuan-izin-keluar.index');
        Route::get('/persetujuan-izin-keluar/create', [PiketPersetujuanIzinKeluarController::class, 'create'])->middleware('permission:manage perizinan siswa')->name('persetujuan-izin-keluar.create');
        Route::post('/persetujuan-izin-keluar', [PiketPersetujuanIzinKeluarController::class, 'store'])->middleware('permission:manage perizinan siswa')->name('persetujuan-izin-keluar.store');
        Route::get('/api/siswa/{siswa}/schedule', [PiketPersetujuanIzinKeluarController::class, 'getStudentSchedule'])->name('persetujuan-izin-keluar.student-schedule');
        Route::get('/riwayat-izin-keluar', [PiketPersetujuanIzinKeluarController::class, 'riwayat'])->name('persetujuan-izin-keluar.riwayat');
        Route::patch('/persetujuan-izin-keluar/{izin}/approve', [PiketPersetujuanIzinKeluarController::class, 'approve'])->middleware('permission:manage perizinan siswa')->name('persetujuan-izin-keluar.approve');
        Route::patch('/persetujuan-izin-keluar/{izin}/reject', [PiketPersetujuanIzinKeluarController::class, 'reject'])->middleware('permission:manage perizinan siswa')->name('persetujuan-izin-keluar.reject');
        Route::get('/persetujuan-izin-keluar/{izin}/print', [PiketPersetujuanIzinKeluarController::class, 'printPdf'])->name('persetujuan-izin-keluar.print');

        // Route untuk Penanganan Keterlambatan
        Route::get('/penanganan-terlambat', [PenangananTerlambatController::class, 'index'])->middleware('permission:manage penanganan terlambat')->name('penanganan-terlambat.index');
        Route::post('/penanganan-terlambat', [PenangananTerlambatController::class, 'store'])->middleware('permission:manage penanganan terlambat')->name('penanganan-terlambat.store');
        Route::get('/penanganan-terlambat/{keterlambatan}/print', [PenangananTerlambatController::class, 'printPdf'])->name('penanganan-terlambat.print');

        // Route untuk Verifikasi Keterlambatan
        Route::get('/verifikasi-terlambat', [VerifikasiTerlambatController::class, 'index'])->middleware('permission:manage penanganan terlambat')->name('verifikasi-terlambat.index');
        Route::get('/verifikasi-terlambat/{keterlambatan}', [VerifikasiTerlambatController::class, 'show'])->name('verifikasi-terlambat.show');
        Route::put('/verifikasi-terlambat/{keterlambatan}', [VerifikasiTerlambatController::class, 'update'])->middleware('permission:manage penanganan terlambat')->name('verifikasi-terlambat.update');

        // Route untuk Absensi Guru
        Route::get('/absensi-guru', [\App\Http\Controllers\Piket\AbsensiGuruController::class, 'index'])->middleware('permission:manage absensi guru')->name('absensi-guru.index');
        Route::post('/absensi-guru', [\App\Http\Controllers\Piket\AbsensiGuruController::class, 'store'])->middleware('permission:manage absensi guru')->name('absensi-guru.store');
        Route::patch('/absensi-guru/{id}', [\App\Http\Controllers\Piket\AbsensiGuruController::class, 'update'])->middleware('permission:manage absensi guru')->name('absensi-guru.update');
    });

    // Route ini kita namakan 'api.siswa.search' sesuai panggilan di JavaScript
    Route::get('/api/siswa/search', [PenangananTerlambatController::class, 'search'])
        ->middleware(['auth']) // Pastikan hanya user login yang bisa akses
        ->name('api.siswa.search');

    // Grup Route untuk Kurikulum
    Route::middleware(['role:Kurikulum', 'permission:view kurikulum dashboard'])->prefix('kurikulum')->name('kurikulum.')->group(function () {
        Route::get('/dashboard', [KurikulumDashboardController::class, 'index'])->name('dashboard.index');
        Route::resource('mata-pelajaran', MataPelajaranController::class)->middleware('permission:manage mata pelajaran');
        Route::post('master-guru/{master_guru}/generate-akun', [MasterGuruController::class, 'generateAkun'])->middleware('permission:manage guru')->name('master-guru.generate-akun');
        Route::resource('master-guru', MasterGuruController::class)->middleware('permission:manage guru');

        // Monitoring Absensi Guru
        Route::get('/monitoring-absensi-guru', [\App\Http\Controllers\Kurikulum\MonitoringAbsensiGuruController::class, 'index'])->middleware('permission:manage monitoring absensi guru')->name('monitoring-absensi-guru.index');
        Route::get('/monitoring-absensi-guru/export', [\App\Http\Controllers\Kurikulum\MonitoringAbsensiGuruController::class, 'export'])->middleware('permission:manage monitoring absensi guru')->name('monitoring-absensi-guru.export');

        // Monitoring Absensi Per Kelas
        Route::get('/monitoring-absensi-per-kelas', [\App\Http\Controllers\Kurikulum\MonitoringAbsensiPerKelasController::class, 'index'])->middleware('permission:manage monitoring absensi guru')->name('monitoring-absensi-per-kelas.index');
        Route::get('/monitoring-absensi-per-kelas/export', [\App\Http\Controllers\Kurikulum\MonitoringAbsensiPerKelasController::class, 'export'])->middleware('permission:manage monitoring absensi guru')->name('monitoring-absensi-per-kelas.export');
        Route::get('jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->middleware('permission:manage jadwal pelajaran')->name('jadwal-pelajaran.index');
        Route::get('jadwal-pelajaran/export-pdf', [JadwalPelajaranController::class, 'exportPdf'])->middleware('permission:manage jadwal pelajaran')->name('jadwal-pelajaran.export-pdf');
        Route::get('jadwal-pelajaran/{rombel}', [JadwalPelajaranController::class, 'show'])->middleware('permission:manage jadwal pelajaran')->name('jadwal-pelajaran.show');
        Route::post('jadwal-pelajaran/{rombel}', [JadwalPelajaranController::class, 'store'])->middleware('permission:manage jadwal pelajaran')->name('jadwal-pelajaran.store');

        //Route untuk Jam Pelajaran
        Route::resource('jam-pelajaran', JamPelajaranController::class)->middleware('permission:manage jam pelajaran');

        Route::post('mata-pelajaran/import', [MataPelajaranController::class, 'import'])->middleware('permission:manage mata pelajaran')->name('mata-pelajaran.import');
        Route::post('master-guru/import', [MasterGuruController::class, 'import'])->middleware('permission:manage guru')->name('master-guru.import');
        Route::get('distribusi-mapel', [DistribusiMapelController::class, 'index'])->middleware('permission:manage distribusi mapel')->name('distribusi-mapel.index');
        Route::get('analisa-semester', [AnalisaKurikulumController::class, 'index'])->middleware('permission:view analisa kurikulum')->name('analisa-semester.index');
        Route::get('analisa-semester/export', [AnalisaKurikulumController::class, 'export'])->middleware('permission:view analisa kurikulum')->name('analisa-semester.export');
        Route::get('analisa-semester/pdf', [AnalisaKurikulumController::class, 'exportPdf'])->middleware('permission:view analisa kurikulum')->name('analisa-semester.pdf');
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
        Route::get('/jadwal-saya', [\App\Http\Controllers\Guru\JadwalSayaController::class, 'index'])->name('jadwal-saya');

        // LMS Routes
        Route::prefix('lms')->name('lms.')->group(function () {
            Route::get('/', [App\Http\Controllers\Guru\LmsController::class, 'index'])->name('index');
            Route::get('/course/{rombel}/{mapel}', [App\Http\Controllers\Guru\LmsController::class, 'show'])->name('course.show');
            
            // Material
            Route::get('/material/create/{rombel}/{mapel}', [App\Http\Controllers\Guru\LmsController::class, 'createMaterial'])->name('material.create');
            Route::post('/material/store/{rombel}/{mapel}', [App\Http\Controllers\Guru\LmsController::class, 'storeMaterial'])->name('material.store');

            // Assignment
            Route::get('/assignment/create/{rombel}/{mapel}', [App\Http\Controllers\Guru\LmsController::class, 'createAssignment'])->name('assignment.create');
            Route::post('/assignment/store/{rombel}/{mapel}', [App\Http\Controllers\Guru\LmsController::class, 'storeAssignment'])->name('assignment.store');
            Route::get('/assignment/{assignment}', [App\Http\Controllers\Guru\LmsController::class, 'showAssignment'])->name('assignment.show');

            // Submission Grading
            Route::get('/submission/{submission}', [App\Http\Controllers\Guru\LmsController::class, 'showSubmission'])->name('submission.show');
            Route::patch('/submission/{submission}/grade', [App\Http\Controllers\Guru\LmsController::class, 'gradeSubmission'])->name('submission.grade');
        });
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
    Route::middleware(['role:KAUR SDM', 'permission:view sdm dashboard'])->prefix('sdm')->name('sdm.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SDM\DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/persetujuan-izin-guru', [\App\Http\Controllers\SDM\PersetujuanIzinGuruController::class, 'index'])->middleware('permission:manage perizinan guru')->name('persetujuan-izin-guru.index');
        Route::patch('/persetujuan-izin-guru/{izin}/approve', [\App\Http\Controllers\SDM\PersetujuanIzinGuruController::class, 'approve'])->middleware('permission:manage perizinan guru')->name('persetujuan-izin-guru.approve');
        Route::patch('/persetujuan-izin-guru/{izin}/reject', [\App\Http\Controllers\SDM\PersetujuanIzinGuruController::class, 'reject'])->middleware('permission:manage perizinan guru')->name('persetujuan-izin-guru.reject');

        // Monitoring & Rekapitulasi
        Route::get('/monitoring', [\App\Http\Controllers\SDM\DashboardController::class, 'monitoring'])->middleware('permission:view rekapitulasi sdm')->name('monitoring.index');
        Route::get('/rekapitulasi', [\App\Http\Controllers\SDM\RekapitulasiController::class, 'index'])->middleware('permission:view rekapitulasi sdm')->name('rekapitulasi.index');
        Route::get('/rekapitulasi/export-excel', [\App\Http\Controllers\SDM\RekapitulasiController::class, 'exportExcel'])->middleware('permission:view rekapitulasi sdm')->name('rekapitulasi.export-excel');
        Route::get('/rekapitulasi/export-pdf', [\App\Http\Controllers\SDM\RekapitulasiController::class, 'exportPdf'])->middleware('permission:view rekapitulasi sdm')->name('rekapitulasi.export-pdf');

        // NDE Referensi
        Route::resource('nde-referensi', NdeReferensiController::class)->middleware('permission:manage nde referensi')->except(['create', 'edit', 'show']);
    });

    // Public/Shared print route for approved permits
    Route::get('/sdm/persetujuan-izin-guru/{izin}/print', [\App\Http\Controllers\SDM\PersetujuanIzinGuruController::class, 'printPdf'])
        ->middleware(['role:KAUR SDM|Guru Kelas'])
        ->name('sdm.persetujuan-izin-guru.print');

    // Grup Route untuk Security
    Route::middleware(['role:Security', 'permission:view security dashboard'])->prefix('security')->name('security.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Security\DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/verifikasi-izin', [SecurityVerifikasiController::class, 'index'])->middleware('permission:manage verifikasi izin')->name('verifikasi.index');
        Route::get('/riwayat-izin', [SecurityVerifikasiController::class, 'riwayat'])->middleware('permission:manage verifikasi izin')->name('verifikasi.riwayat');
        Route::get('/scan-qr', [SecurityVerifikasiController::class, 'scanQr'])->middleware('permission:manage verifikasi izin')->name('verifikasi.scan');
        Route::get('/verifikasi-via-scan/{uuid}', [SecurityVerifikasiController::class, 'showScanResult'])->middleware('permission:manage verifikasi izin')->name('verifikasi.show-scan');

        // Route baru untuk aksi verifikasi keluar & cetak otomatis
        Route::get('/verifikasi-via-scan/{uuid}/process', [SecurityVerifikasiController::class, 'processScanAction'])->middleware('permission:manage verifikasi izin')->name('verifikasi.process-scan');

        Route::patch('/verifikasi-izin/{izin}/keluar', [SecurityVerifikasiController::class, 'verifyKeluar'])->middleware('permission:manage verifikasi izin')->name('verifikasi.keluar');
        Route::patch('/verifikasi-izin/{izin}/kembali', [SecurityVerifikasiController::class, 'verifyKembali'])->middleware('permission:manage verifikasi izin')->name('verifikasi.kembali');
        Route::get('/verifikasi-izin/{izin}/print', [SecurityVerifikasiController::class, 'printPdf'])->name('verifikasi.print');

        // Route untuk Pendataan Keterlambatan
        Route::get('/pendataan-terlambat', [PendataanTerlambatController::class, 'index'])->middleware('permission:manage pendataan terlambat')->name('pendataan-terlambat.index');
        Route::post('/pendataan-terlambat', [PendataanTerlambatController::class, 'store'])->middleware('permission:manage pendataan terlambat')->name('pendataan-terlambat.store');

        // Gate Terminal Routes (ATM Style)
        Route::prefix('terminal')->name('terminal.')->group(function () {
            Route::get('/', [GateTerminalController::class, 'index'])->middleware('permission:manage gate terminal')->name('index');
            Route::get('/lateness', [GateTerminalController::class, 'lateness'])->middleware('permission:manage gate terminal')->name('lateness');
            Route::get('/permit', [GateTerminalController::class, 'permit'])->middleware('permission:manage gate terminal')->name('permit');
            Route::post('/process-lateness', [GateTerminalController::class, 'processLateness'])->middleware('permission:manage gate terminal')->name('process-lateness');
            Route::post('/process-permit', [GateTerminalController::class, 'processPermit'])->middleware('permission:manage gate terminal')->name('process-permit');
        });
    });

    // Specialized Security Login Routes (Inside Auth but accessible by Security)
    Route::get('/security/login', [SecurityLoginController::class, 'showLoginForm'])->name('security.login')->withoutMiddleware(['auth', 'verified']);
    Route::post('/security/login', [SecurityLoginController::class, 'login'])->name('security.login.submit')->withoutMiddleware(['auth', 'verified']);
    Route::post('/security/logout', [SecurityLoginController::class, 'logout'])->name('security.logout');


    // Grup Route untuk Pengajuan Dispensasi (bisa diakses beberapa peran)
    Route::middleware(['auth'])->prefix('dispensasi')->name('dispensasi.')->group(function () {
        Route::get('/pengajuan', [PengajuanDispensasiController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/create', [PengajuanDispensasiController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanDispensasiController::class, 'store'])->name('pengajuan.store');
    });

    // Grup Route untuk Prakerin
    Route::middleware(['role:Koordinator Prakerin', 'permission:manage prakerin'])->prefix('prakerin')->name('prakerin.')->group(function () {
        Route::resource('industri', IndustriController::class);
        Route::resource('penempatan', PenempatanController::class);
    });

    // Grup Route untuk Guru Pembimbing (bisa diakses Guru Kelas)
    Route::middleware(['role:Guru Kelas', 'permission:monitor prakerin'])->prefix('pembimbing-prakerin')->name('pembimbing-prakerin.')->group(function () {
        Route::get('/monitoring', [MonitoringPembimbingController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/{penempatan}', [MonitoringPembimbingController::class, 'show'])->name('monitoring.show');
        Route::patch('/monitoring/jurnal/{jurnal}', [MonitoringPembimbingController::class, 'updateJurnal'])->name('monitoring.updateJurnal');
    });

    // Grup Route untuk Super Admin
    Route::middleware(['role:Super Admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');

        // Permission Management
        Route::get('/permissions', [PermissionManagementController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/{role}', [PermissionManagementController::class, 'getRolePermissions'])->name('permissions.get');
        Route::post('/permissions/{role}', [PermissionManagementController::class, 'syncPermissions'])->name('permissions.sync');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Monitoring Keterlambatan Shared
    Route::get('/monitoring-keterlambatan', [\App\Http\Controllers\Shared\MonitoringKeterlambatanController::class, 'index'])
        ->name('monitoring-keterlambatan.index');
    Route::get('/monitoring-keterlambatan/export', [\App\Http\Controllers\Shared\MonitoringKeterlambatanController::class, 'export'])
        ->name('monitoring-keterlambatan.export');
    Route::get('/monitoring-keterlambatan/{keterlambatan}', [\App\Http\Controllers\Shared\MonitoringKeterlambatanController::class, 'show'])
        ->name('monitoring-keterlambatan.show');
    Route::get('/monitoring-keterlambatan/{keterlambatan}/print-slip', [\App\Http\Controllers\Shared\MonitoringKeterlambatanController::class, 'printSlip'])
        ->name('monitoring-keterlambatan.print-slip');

    // Change Log
    Route::get('/changelog', [\App\Http\Controllers\Shared\ChangeLogController::class, 'index'])->name('changelog.index');

    // Notifications
    Route::prefix('notifications')->name('shared.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Shared\NotificationController::class, 'index'])->name('index');
        Route::get('/{id}/read', [\App\Http\Controllers\Shared\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-as-read', [\App\Http\Controllers\Shared\NotificationController::class, 'markAllAsRead'])->name('mark-all');
    });
});

Route::post('/switch-role', [\App\Http\Controllers\RoleSwitchController::class, 'switch'])
    ->name('role.switch')
    ->middleware('auth');

require __DIR__ . '/auth.php';
