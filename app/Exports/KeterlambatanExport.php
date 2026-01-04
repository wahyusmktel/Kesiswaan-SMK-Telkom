<?php

namespace App\Exports;

use App\Models\Keterlambatan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class KeterlambatanExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Keterlambatan::with(['siswa.user', 'siswa.rombels.kelas', 'security', 'guruPiket']);

        if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
            $query->whereBetween('waktu_dicatat_security', [$this->filters['start_date'] . ' 00:00:00', $this->filters['end_date'] . ' 23:59:59']);
        }

        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['kelas_id']) && $this->filters['kelas_id']) {
            $kelasId = $this->filters['kelas_id'];
            $query->whereHas('siswa.rombels', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        // Apply same role-based scoping if necessary (passed via filters)
        if (isset($this->filters['wali_kelas_id'])) {
            $query->whereHas('siswa.rombels', function ($q) {
                $q->where('wali_kelas_id', $this->filters['wali_kelas_id']);
            });
        }

        return $query->latest('waktu_dicatat_security');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'NIS',
            'Kelas',
            'Waktu Datang',
            'Alasan',
            'Status',
            'Dicatat Oleh (Security)',
            'Diverifikasi Oleh (Piket)',
            'Waktu Verifikasi',
        ];
    }

    public function map($late): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $late->siswa->user->name,
            $late->siswa->nis,
            $late->siswa->rombels->first()?->kelas->nama_kelas ?? 'N/A',
            $late->waktu_dicatat_security->format('d/m/Y H:i'),
            $late->alasan_siswa,
            str_replace('_', ' ', strtoupper($late->status)),
            $late->security->name,
            $late->guruPiket->name ?? '-',
            $late->waktu_verifikasi_piket ? $late->waktu_verifikasi_piket->format('d/m/Y H:i') : '-',
        ];
    }
}
