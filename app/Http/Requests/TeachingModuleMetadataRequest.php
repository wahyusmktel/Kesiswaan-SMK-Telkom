<?php

namespace App\Http\Requests;

use App\Models\TeachingModule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeachingModuleMetadataRequest extends FormRequest
{
    public function authorize(): bool
    {
        $module = $this->route('teachingModule');

        return $this->user() !== null
            && (! $module instanceof TeachingModule || $module->teacher_id === $this->user()->id);
    }

    protected function prepareForValidation(): void
    {
        $allocation = strtoupper(trim((string) $this->input('alokasi_waktu')));
        $allocation = preg_replace('/^(\d+)\s*JP$/', '$1 JP', $allocation) ?? $allocation;

        $this->merge([
            'program_keahlian' => trim((string) $this->input('program_keahlian')),
            'fase' => strtoupper(trim((string) $this->input('fase'))),
            'nama_penyusun' => trim((string) $this->input('nama_penyusun')),
            'instansi' => trim((string) $this->input('instansi')),
            'nama_modul' => trim((string) $this->input('nama_modul')),
            'alokasi_waktu' => $allocation,
            'jenjang' => strtoupper(trim((string) $this->input('jenjang'))),
            'kelas' => strtoupper(trim((string) $this->input('kelas'))),
            'kode_modul' => strtoupper(trim((string) $this->input('kode_modul'))),
            'jumlah_murid' => trim((string) $this->input('jumlah_murid')),
            'lingkup_materi' => trim((string) $this->input('lingkup_materi')),
        ]);
    }

    public function rules(): array
    {
        $module = $this->route('teachingModule');
        $yearId = (int) $this->input('tahun_pelajaran_id');
        $studentCounts = array_merge(['Disesuaikan'], array_map('strval', range(1, 40)));

        $uniqueCode = Rule::unique('teaching_modules', 'kode_modul')
            ->where(fn ($query) => $query
                ->where('teacher_id', $this->user()->id)
                ->where('tahun_pelajaran_id', $yearId));

        if ($module instanceof TeachingModule) {
            $uniqueCode->ignore($module->id);
        }

        return [
            'program_keahlian' => ['required', 'string', 'max:150'],
            'mata_pelajaran_id' => ['required', 'integer', 'exists:mata_pelajarans,id'],
            'fase' => ['required', Rule::in(['A', 'B', 'C', 'D', 'E', 'F'])],
            'nama_penyusun' => ['required', 'string', 'max:255'],
            'instansi' => ['required', 'string', 'max:255'],
            'tahun_pelajaran_id' => ['required', 'integer', 'exists:tahun_pelajaran,id'],
            'nama_modul' => ['required', 'string', 'max:255'],
            'alokasi_waktu' => ['required', 'regex:/^(?:[1-9]|[1-9][0-9]|[1-9][0-9]{2}) JP$/'],
            'jenjang' => ['required', Rule::in(['SD', 'SMP', 'SMA', 'SMK', 'MA', 'MAK'])],
            'kelas' => ['required', 'string', 'max:50'],
            'kode_modul' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9]+(?:[-.\/][A-Z0-9]+)*$/',
                $uniqueCode,
            ],
            'jumlah_murid' => ['required', Rule::in($studentCounts)],
            'lingkup_materi' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
            'fase.in' => 'Fase harus dipilih dari A sampai F.',
            'alokasi_waktu.regex' => 'Alokasi waktu harus menggunakan format jumlah JP, misalnya 4 JP.',
            'kode_modul.regex' => 'Kode modul hanya boleh berisi huruf, angka, tanda titik, garis miring, atau tanda hubung.',
            'kode_modul.unique' => 'Kode modul sudah digunakan pada tahun pelajaran dan semester tersebut.',
            'jumlah_murid.in' => 'Jumlah murid harus 1 sampai 40 atau Disesuaikan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'program_keahlian' => 'Program Keahlian',
            'mata_pelajaran_id' => 'Mata Pelajaran',
            'fase' => 'Fase',
            'nama_penyusun' => 'Nama Penyusun',
            'instansi' => 'Instansi',
            'tahun_pelajaran_id' => 'Tahun Pelajaran',
            'nama_modul' => 'Nama Modul',
            'alokasi_waktu' => 'Alokasi Waktu',
            'jenjang' => 'Jenjang',
            'kelas' => 'Kelas',
            'kode_modul' => 'Kode Modul',
            'jumlah_murid' => 'Jumlah Murid',
            'lingkup_materi' => 'Lingkup Materi',
        ];
    }
}
