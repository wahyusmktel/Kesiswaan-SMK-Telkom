<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\DapodikGuruSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DapodikController extends Controller
{
    private function getGuru()
    {
        return Auth::user()->masterGuru;
    }

    public function index()
    {
        $guru = $this->getGuru();

        if (!$guru) {
            return redirect()->route('guru-kelas.dashboard.index')->with('error', 'Data guru tidak ditemukan. Hubungi operator.');
        }

        $guru->load('dapodikGuru');

        $pendingSubmission  = $guru->pendingDapodikSubmission()->first();
        $latestRejected     = $guru->dapodikSubmissions()
            ->where('status', 'rejected')
            ->latest()
            ->first();

        if ($pendingSubmission) {
            $latestRejected = null;
        }

        return view('pages.guru.dapodik.index', compact('guru', 'pendingSubmission', 'latestRejected'));
    }

    public function edit()
    {
        $guru = $this->getGuru();

        if (!$guru) {
            return redirect()->route('guru-kelas.dashboard.index')->with('error', 'Data guru tidak ditemukan.');
        }

        if ($guru->pendingDapodikSubmission()->exists()) {
            return redirect()->route('guru.dapodik.index')
                ->with('warning', 'Anda masih memiliki pengajuan yang belum diproses oleh operator.');
        }

        $guru->load('dapodikGuru');

        return view('pages.guru.dapodik.edit', compact('guru'));
    }

    public function storeSubmission(Request $request)
    {
        $guru = $this->getGuru();

        if (!$guru) {
            return redirect()->route('guru-kelas.dashboard.index')->with('error', 'Data guru tidak ditemukan.');
        }

        if ($guru->pendingDapodikSubmission()->exists()) {
            return redirect()->route('guru.dapodik.index')
                ->with('error', 'Anda sudah memiliki pengajuan yang sedang menunggu persetujuan.');
        }

        $validated = $request->validate([
            'nik'               => 'nullable|string|max:20',
            'nuptk'             => 'nullable|string|max:20',
            'jenis_kelamin'     => 'nullable|in:L,P',
            'tempat_lahir'      => 'nullable|string|max:255',
            'tanggal_lahir'     => 'nullable|date',
            'agama'             => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|string|max:50',
            'nama_pasangan'     => 'nullable|string|max:255',
            'nip_pasangan'      => 'nullable|string|max:30',
            'pekerjaan_pasangan'=> 'nullable|string|max:255',
            'no_kk'             => 'nullable|string|max:30',
            'nama_ibu_kandung'  => 'nullable|string|max:255',
            'telepon'           => 'nullable|string|max:20',
            'hp'                => 'nullable|string|max:20',
            'email_dapodik'     => 'nullable|email|max:255',
            'alamat_jalan'      => 'nullable|string|max:500',
            'rt'                => 'nullable|string|max:5',
            'rw'                => 'nullable|string|max:5',
            'nama_dusun'        => 'nullable|string|max:255',
            'desa_kelurahan'    => 'nullable|string|max:255',
            'kecamatan'         => 'nullable|string|max:255',
            'kode_pos'          => 'nullable|string|max:10',
            'npwp'              => 'nullable|string|max:30',
            'nama_wajib_pajak'  => 'nullable|string|max:255',
            'bank'              => 'nullable|string|max:100',
            'no_rekening'       => 'nullable|string|max:50',
            'rekening_atas_nama'=> 'nullable|string|max:255',
            'karpeg'            => 'nullable|string|max:50',
        ]);

        $dapodik = $guru->dapodikGuru;

        // Build old_data and new_data from changed fields only
        $oldData = [];
        $newData = [];

        foreach ($validated as $field => $newVal) {
            $oldVal = $dapodik?->$field;

            if ($oldVal instanceof \Carbon\Carbon) {
                $oldVal = $oldVal->format('Y-m-d');
            }

            $normOld = ($oldVal === '' || $oldVal === null) ? null : $oldVal;
            $normNew = ($newVal === '' || $newVal === null) ? null : $newVal;

            if ($normNew !== $normOld) {
                $oldData[$field] = $normOld;
                $newData[$field] = $normNew;
            }
        }

        if (empty($newData)) {
            return back()->with('error', 'Tidak ada perubahan data yang diajukan.');
        }

        DapodikGuruSubmission::create([
            'master_guru_id' => $guru->id,
            'old_data'       => $oldData,
            'new_data'       => $newData,
            'status'         => 'pending',
            'submitted_at'   => now(),
        ]);

        return redirect()->route('guru.dapodik.index')
            ->with('success', 'Pengajuan perubahan data berhasil dikirim dan menunggu verifikasi Operator.');
    }

    public function history()
    {
        $guru = $this->getGuru();

        if (!$guru) {
            return redirect()->route('guru-kelas.dashboard.index')->with('error', 'Data guru tidak ditemukan.');
        }

        $submissions = $guru->dapodikSubmissions()->latest()->paginate(10);

        return view('pages.guru.dapodik.history', compact('submissions'));
    }
}
