<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\FingerprintAttendance;
use App\Models\GuruIzin;
use App\Models\IzinMeninggalkanKelas;
use App\Models\JadwalPelajaran;
use App\Models\Keterlambatan;
use App\Models\Perizinan;
use App\Models\SiswaPelanggaran;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public const SECTIONS = [
        'keterlambatan' => 'Monitoring Keterlambatan Siswa',
        'fingerprint' => 'Monitoring Absensi Fingerprint Guru',
        'jadwal' => 'Jadwal Mengajar Guru',
        'izin-siswa' => 'Monitoring Izin Siswa',
        'pelanggaran' => 'Monitoring Poin Pelanggaran Siswa',
        'izin-guru' => 'Monitoring Izin Guru',
    ];

    public function index(Request $request, string $section)
    {
        abort_unless(isset(self::SECTIONS[$section]), 404);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'jenis_data' => ['nullable', 'in:tidak-masuk,keluar'],
        ]);
        $from = $filters['from'] ?? now()->startOfMonth()->toDateString();
        $to = $filters['to'] ?? today()->toDateString();
        abort_if($from > $to, 422, 'Tanggal akhir harus setelah tanggal awal.');
        $search = trim($filters['q'] ?? '');
        $exitPermit = $section === 'izin-siswa' && ($filters['jenis_data'] ?? '') === 'keluar';
        $note = 'Data baca-saja. Perubahan dan persetujuan tetap dilakukan oleh unit terkait.';

        switch ($section) {
            case 'keterlambatan':
                $query = Keterlambatan::with(['siswa', 'security']);
                $dateColumn = 'waktu_dicatat_security';
                $relation = 'siswa';
                $nameColumn = 'nama_lengkap';
                $headers = ['Waktu', 'Siswa', 'Alasan', 'Status', 'Petugas'];
                $row = fn ($item) => [$item->waktu_dicatat_security?->format('d/m/Y H:i'), $item->siswa?->nama_lengkap, $item->alasan_siswa, $item->status, $item->security?->name];
                break;
            case 'fingerprint':
                $query = FingerprintAttendance::with(['appUser', 'device'])->whereHas('appUser.masterGuru');
                $dateColumn = 'timestamp';
                $relation = 'appUser';
                $nameColumn = 'name';
                $headers = ['Waktu Scan', 'Guru', 'Mesin', 'Kode Punch', 'Sumber'];
                $row = fn ($item) => [$item->timestamp?->format('d/m/Y H:i:s'), $item->appUser?->name, $item->device?->name, $item->punch, $item->entry_source ?? 'mesin'];
                $note = 'Log scan guru yang sudah dipetakan ke akun dan data master guru. Jumlah log bukan jumlah guru hadir; log kosong tidak otomatis berarti alpha.';
                break;
            case 'jadwal':
                $query = JadwalPelajaran::with(['guru', 'rombel.kelas', 'mataPelajaran'])->inActiveAcademicPeriod();
                $dateColumn = null;
                $relation = 'guru';
                $nameColumn = 'nama_lengkap';
                $headers = ['Hari', 'Jam', 'Guru', 'Kelas', 'Mata Pelajaran'];
                $row = fn ($item) => [$item->hari, $item->jam_mulai.' - '.$item->jam_selesai, $item->guru?->nama_lengkap, $item->rombel?->kelas?->nama_kelas, $item->mataPelajaran?->nama_mapel];
                $query->orderBy('hari')->orderBy('jam_mulai');
                $note = 'Jadwal mengajar pada tahun pelajaran/semester yang aktif. Cari berdasarkan nama guru.';
                break;
            case 'izin-siswa':
                if ($exitPermit) {
                    $query = IzinMeninggalkanKelas::with('siswa');
                    $relation = 'siswa';
                    $dateColumn = 'created_at';
                    $headers = ['Diajukan', 'Siswa', 'Tujuan', 'Status', 'Kembali'];
                    $row = fn ($item) => [$item->created_at?->format('d/m/Y H:i'), $item->siswa?->name, $item->tujuan, $item->status, $item->waktu_kembali_sebenarnya?->format('d/m/Y H:i')];
                } else {
                    $query = Perizinan::with('user');
                    $relation = 'user';
                    $dateColumn = 'tanggal_izin';
                    $headers = ['Tanggal Izin', 'Siswa', 'Jenis', 'Keterangan', 'Status'];
                    $row = fn ($item) => [$item->tanggal_izin, $item->user?->name, $item->jenis_izin, $item->keterangan, $item->status];
                }
                $nameColumn = 'name';
                break;
            case 'pelanggaran':
                $query = SiswaPelanggaran::with(['siswa', 'peraturan', 'pelapor']);
                $dateColumn = 'tanggal';
                $relation = 'siswa';
                $nameColumn = 'nama_lengkap';
                $headers = ['Tanggal', 'Siswa', 'Pelanggaran', 'Bobot Poin', 'Pelapor'];
                $row = fn ($item) => [$item->tanggal, $item->siswa?->nama_lengkap, $item->peraturan?->deskripsi, $item->peraturan?->bobot_poin, $item->pelapor?->name];
                $note = 'Riwayat kejadian dan bobot peraturan saat ini, bukan saldo poin bersih setelah pemutihan.';
                break;
            default:
                $query = GuruIzin::with('guru');
                $dateColumn = 'tanggal_mulai';
                $relation = 'guru';
                $nameColumn = 'nama_lengkap';
                $headers = ['Mulai', 'Selesai', 'Guru', 'Jenis Izin', 'Piket', 'Kurikulum', 'SDM'];
                $row = fn ($item) => [$item->tanggal_mulai?->format('d/m/Y'), $item->tanggal_selesai?->format('d/m/Y'), $item->guru?->nama_lengkap, $item->jenis_izin, $item->status_piket, $item->status_kurikulum, $item->status_sdm];
                break;
        }

        if ($dateColumn) {
            if ($section === 'izin-guru') {
                $query->where('tanggal_mulai', '<=', $to.' 23:59:59')
                    ->where('tanggal_selesai', '>=', $from);
            } else {
                $query->whereBetween($dateColumn, [$from, $to.' 23:59:59']);
            }
            $query->orderByDesc($dateColumn);
        }
        $query->when($search !== '', fn ($query) => $query->whereHas(
            $relation, fn ($names) => $names->where($nameColumn, 'like', '%'.$search.'%')
        ));
        $records = $query->orderByDesc('id')->paginate(25)->withQueryString()->through($row);

        return view('pages.kepala-sekolah.monitoring', [
            'title' => self::SECTIONS[$section],
            'section' => $section,
            'headers' => $headers,
            'records' => $records,
            'note' => $note,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
