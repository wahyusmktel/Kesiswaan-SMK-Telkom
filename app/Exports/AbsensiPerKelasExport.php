<?php

namespace App\Exports;

use App\Models\AbsensiGuru;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class AbsensiPerKelasExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $startDate;
    protected $endDate;
    protected $rombelId;

    public function __construct($startDate, $endDate, $rombelId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->rombelId = $rombelId;
    }

    public function query()
    {
        $query = AbsensiGuru::query()
            ->with(['jadwalPelajaran.guru', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.rombel.kelas', 'pencatat'])
            ->whereBetween('tanggal', [$this->startDate, $this->endDate]);

        if ($this->rombelId) {
            $query->whereHas('jadwalPelajaran', function($q) {
                $q->where('rombel_id', $this->rombelId);
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jam',
            'Nama Guru',
            'Mata Pelajaran',
            'Kelas',
            'Status',
            'Waktu Absen',
            'Dicatat Oleh',
            'Keterangan',
        ];
    }

    public function map($absensi): array
    {
        return [
            $absensi->tanggal->format('d/m/Y'),
            $absensi->jadwalPelajaran->jam_mulai . ' - ' . $absensi->jadwalPelajaran->jam_selesai,
            $absensi->jadwalPelajaran->guru->nama_lengkap ?? 'Guru Tidak Tersedia',
            $absensi->jadwalPelajaran->mataPelajaran->nama_mapel,
            $absensi->jadwalPelajaran->rombel->kelas->nama_kelas ?? $absensi->jadwalPelajaran->rombel->nama_rombel,
            ucfirst(str_replace('_', ' ', $absensi->status)),
            $absensi->waktu_absen ? $absensi->waktu_absen->format('H:i:s') : '-',
            $absensi->pencatat->name ?? '-',
            $absensi->keterangan,
        ];
    }
}
