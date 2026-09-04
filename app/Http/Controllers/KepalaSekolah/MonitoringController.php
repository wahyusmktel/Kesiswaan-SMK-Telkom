<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\FingerprintAttendance;
use App\Models\GuruIzin;
use App\Models\IzinMeninggalkanKelas;
use App\Models\JadwalPelajaran;
use App\Models\Keterlambatan;
use App\Models\MasterGuru;
use App\Models\Perizinan;
use App\Models\SiswaPelanggaran;
use App\Models\WorkCalendarEvent;
use App\Support\EmploymentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        if ($section === 'fingerprint') {
            return $this->fingerprint($request);
        }
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

    private function fingerprint(Request $request)
    {
        $input = $request->validate(['date' => ['nullable', 'date_format:Y-m-d']]);
        $date = Carbon::parse($input['date'] ?? today()->toDateString());
        $scans = FingerprintAttendance::query()
            ->select('app_user_id', DB::raw('MIN(timestamp) as first_scan'), DB::raw('MAX(timestamp) as last_scan'))
            ->whereNotNull('app_user_id')
            ->whereBetween('timestamp', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
            ->groupBy('app_user_id')->get()->keyBy('app_user_id');
        $holiday = WorkCalendarEvent::eventFor($date);
        $workingDay = ! $date->isWeekend() && ! $holiday;
        $dayName = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$date->dayOfWeekIso - 1];
        $schedules = JadwalPelajaran::inActiveAcademicPeriod()->where('hari', $dayName)
            ->select('master_guru_id', DB::raw('MIN(jam_mulai) as starts_at'), DB::raw('MAX(jam_selesai) as ends_at'))
            ->groupBy('master_guru_id')->get()->keyBy('master_guru_id');
        $teachers = MasterGuru::with('dapodikGuru')->orderBy('nama_lengkap')->get(['id', 'user_id', 'nama_lengkap']);
        $rows = $teachers->map(function ($teacher) use ($scans, $workingDay, $schedules, $holiday) {
            $scan = $teacher->user_id ? $scans->get($teacher->user_id) : null;
            $employment = EmploymentStatus::normalize($teacher->dapodikGuru?->status_kepegawaian);
            $recognized = in_array($employment, [EmploymentStatus::PERMANENT, EmploymentStatus::FULL_TIME, EmploymentStatus::PART_TIME], true);
            $schedule = $schedules->get($teacher->id);
            $required = $recognized && $workingDay && ($employment !== EmploymentStatus::PART_TIME || $schedule !== null);
            $obligation = match (true) {
                ! $recognized => 'Status kepegawaian perlu diperiksa',
                ! $workingDay => $holiday ? 'Libur: '.$holiday->title : 'Akhir pekan',
                $employment === EmploymentStatus::PART_TIME && ! $schedule => 'Tidak ada jadwal mengajar',
                $employment === EmploymentStatus::PART_TIME => 'Jadwal '.substr($schedule->starts_at, 0, 5).'–'.substr($schedule->ends_at, 0, 5),
                default => 'Wajib hadir hari kerja',
            };

            return [
                'id' => $teacher->id,
                'name' => $teacher->nama_lengkap,
                'employment' => $employment ?? 'Belum diisi',
                'required' => $required,
                'recognized' => $recognized,
                'obligation' => $obligation,
                'check_in' => $scan ? Carbon::parse($scan->first_scan)->format('H:i') : null,
                'check_out' => $scan && $scan->last_scan > $scan->first_scan ? Carbon::parse($scan->last_scan)->format('H:i') : null,
                'hour' => $scan ? Carbon::parse($scan->first_scan)->hour : null,
            ];
        })->values();
        $summary = [
            'total' => $rows->count(),
            'present' => $rows->whereNotNull('check_in')->count(),
            'out' => $rows->whereNotNull('check_out')->count(),
            'missing' => $rows->whereNull('check_in')->count(),
            'required' => $rows->where('required', true)->count(),
            'required_present' => $rows->where('required', true)->whereNotNull('check_in')->count(),
            'required_missing' => $rows->where('required', true)->whereNull('check_in')->count(),
            'unclassified' => $rows->where('recognized', false)->count(),
        ];
        $hours = collect(range(0, 23))->map(fn ($hour) => [
            'label' => sprintf('%02d:00', $hour),
            'count' => $rows->filter(fn ($row) => $row['hour'] === $hour)->count(),
        ]);

        return view('pages.kepala-sekolah.fingerprint', compact('date', 'rows', 'summary', 'hours'));
    }
}
