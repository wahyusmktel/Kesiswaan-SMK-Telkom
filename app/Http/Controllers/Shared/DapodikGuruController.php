<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Imports\DapodikGuruImport;
use App\Models\DapodikGuru;
use App\Models\MasterGuru;
use App\Support\EmploymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class DapodikGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = DapodikGuru::with('masterGuru.user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhere('nuptk', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'linked') {
                $query->whereNotNull('master_guru_id');
            } elseif ($request->status === 'unlinked') {
                $query->whereNull('master_guru_id');
            }
        }

        if ($request->filled('jenis_ptk')) {
            $query->where('jenis_ptk', $request->jenis_ptk);
        }

        $dapodikGurus = $query->orderBy('nama')->paginate(20)->withQueryString();

        $totalDapodik  = DapodikGuru::count();
        $totalLinked   = DapodikGuru::whereNotNull('master_guru_id')->count();
        $totalUnlinked = DapodikGuru::whereNull('master_guru_id')->count();
        $jenisPtkList  = DapodikGuru::select('jenis_ptk')->distinct()->whereNotNull('jenis_ptk')->orderBy('jenis_ptk')->pluck('jenis_ptk');

        $employees = MasterGuru::with('user')
            ->orderBy('nama_lengkap')
            ->get();

        return view('pages.shared.dapodik-guru.index', compact(
            'dapodikGurus', 'totalDapodik', 'totalLinked', 'totalUnlinked', 'jenisPtkList', 'employees'
        ));
    }

    public function show(DapodikGuru $dapodikGuru)
    {
        $dapodikGuru->load('masterGuru.user.roles');
        return view('pages.shared.dapodik-guru.show', compact('dapodikGuru'));
    }

    public function create()
    {
        $dapodikGuru = new DapodikGuru();
        $isCreate = true;

        return view('pages.shared.dapodik-guru.edit', compact('dapodikGuru', 'isCreate'));
    }

    public function edit(DapodikGuru $dapodikGuru)
    {
        $dapodikGuru->load('masterGuru.user');
        return view('pages.shared.dapodik-guru.edit', compact('dapodikGuru'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        try {
            $masterGuru = null;
            if ($request->filled('nik')) {
                $masterGuru = MasterGuru::where('nik', $request->nik)->first();
            }

            $dapodikGuru = DapodikGuru::create(array_merge(
                $data,
                ['master_guru_id' => $masterGuru?->id]
            ));

            toast('Data dapodik guru berhasil ditambahkan.', 'success');

            return redirect()->route('dapodik-guru.show', $dapodikGuru);
        } catch (\Exception $e) {
            Log::error('DapodikGuru store error: ' . $e->getMessage());
            toast('Gagal menambahkan: ' . $e->getMessage(), 'error');

            return back()->withInput();
        }
    }

    public function update(Request $request, DapodikGuru $dapodikGuru)
    {
        $data = $this->validatedData($request, $dapodikGuru);

        try {
            // Re-link master_guru if NIK changed
            $masterGuru = null;
            if ($request->filled('nik')) {
                $masterGuru = MasterGuru::where('nik', $request->nik)->first();
            }

            $dapodikGuru->update(array_merge(
                $data,
                ['master_guru_id' => $masterGuru?->id ?? $dapodikGuru->master_guru_id]
            ));

            toast('Data dapodik berhasil diperbarui.', 'success');
        } catch (\Exception $e) {
            Log::error('DapodikGuru update error: ' . $e->getMessage());
            toast('Gagal menyimpan: ' . $e->getMessage(), 'error');
        }

        return redirect()->route('dapodik-guru.show', $dapodikGuru);
    }

    private function validatedData(Request $request, ?DapodikGuru $dapodikGuru = null): array
    {
        return $request->validate([
            'nama'                    => 'required|string|max:255',
            'nik'                     => ['nullable', 'string', 'max:20', Rule::unique('dapodik_gurus', 'nik')->ignore($dapodikGuru?->id)],
            'nuptk'                   => 'nullable|string|max:20',
            'jenis_kelamin'           => 'nullable|in:L,P',
            'tempat_lahir'            => 'nullable|string|max:255',
            'tanggal_lahir'           => 'nullable|date',
            'agama'                   => 'nullable|string|max:255',
            'kewarganegaraan'         => 'nullable|string|max:5',
            'no_kk'                   => 'nullable|string|max:20',
            'nama_ibu_kandung'        => 'nullable|string|max:255',
            'nip'                     => 'nullable|string|max:30',
            'status_kepegawaian'      => ['nullable', Rule::in(EmploymentStatus::options())],
            'jenis_ptk'               => 'nullable|string|max:255',
            'tugas_tambahan'          => 'nullable|string|max:255',
            'pangkat_golongan'        => 'nullable|string|max:255',
            'sumber_gaji'             => 'nullable|string|max:255',
            'lembaga_pengangkatan'    => 'nullable|string|max:255',
            'sk_pengangkatan'         => 'nullable|string|max:255',
            'tmt_pengangkatan'        => 'nullable|date',
            'sk_cpns'                 => 'nullable|string|max:255',
            'tanggal_cpns'            => 'nullable|date',
            'tmt_pns'                 => 'nullable|date',
            'nuks'                    => 'nullable|string|max:30',
            'karpeg'                  => 'nullable|string|max:20',
            'karis_karsu'             => 'nullable|string|max:20',
            'alamat_jalan'            => 'nullable|string|max:255',
            'rt'                      => 'nullable|string|max:5',
            'rw'                      => 'nullable|string|max:5',
            'nama_dusun'              => 'nullable|string|max:255',
            'desa_kelurahan'          => 'nullable|string|max:255',
            'kecamatan'               => 'nullable|string|max:255',
            'kode_pos'                => 'nullable|string|max:10',
            'telepon'                 => 'nullable|string|max:20',
            'hp'                      => 'nullable|string|max:20',
            'email_dapodik'           => 'nullable|email',
            'lintang'                 => 'nullable|string|max:255',
            'bujur'                   => 'nullable|string|max:255',
            'status_perkawinan'       => 'nullable|string|max:255',
            'nama_pasangan'           => 'nullable|string|max:255',
            'nip_pasangan'            => 'nullable|string|max:30',
            'pekerjaan_pasangan'      => 'nullable|string|max:255',
            'npwp'                    => 'nullable|string|max:30',
            'nama_wajib_pajak'        => 'nullable|string|max:255',
            'bank'                    => 'nullable|string|max:255',
            'no_rekening'             => 'nullable|string|max:30',
            'rekening_atas_nama'      => 'nullable|string|max:255',
            'lisensi_kepala_sekolah'  => 'nullable|in:Ya,Tidak',
            'diklat_kepengawasan'     => 'nullable|in:Ya,Tidak',
            'keahlian_braille'        => 'nullable|in:Ya,Tidak',
            'keahlian_bahasa_isyarat' => 'nullable|in:Ya,Tidak',
        ]);
    }

    public function updateMapping(Request $request, DapodikGuru $dapodikGuru)
    {
        $data = $request->validate([
            'master_guru_id' => ['nullable', 'exists:master_gurus,id'],
        ]);

        DB::transaction(function () use ($dapodikGuru, $data) {
            $masterGuruId = $data['master_guru_id'] ?? null;

            if ($masterGuruId) {
                DapodikGuru::where('master_guru_id', $masterGuruId)
                    ->whereKeyNot($dapodikGuru->id)
                    ->update(['master_guru_id' => null]);
            }

            $dapodikGuru->update(['master_guru_id' => $masterGuruId]);
        });

        return back()->with('success', 'Mapping Dapodik Guru ke data pegawai berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $import = new DapodikGuruImport();
            Excel::import($import, $request->file('file_import'));

            $msg = "Import selesai: {$import->created} data baru, {$import->updated} diperbarui";
            if ($import->skipped > 0) $msg .= ", {$import->skipped} dilewati";
            toast($msg . '.', 'success');

            if (!empty($import->errors)) {
                session()->flash('dapodik_import_errors', $import->errors);
            }
        } catch (\Exception $e) {
            Log::error('DapodikGuru import error: ' . $e->getMessage());
            toast('Gagal memproses file: ' . $e->getMessage(), 'error');
        }

        return back();
    }
}
