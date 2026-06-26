<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\MasterGuru;
use App\Models\AppSetting;
use App\Models\PrakerinIndustri;
use App\Models\PrakerinPembimbing;
use Illuminate\Http\Request;

class PembimbingController extends Controller
{
    public function index(Request $request)
    {
        $pembimbings = PrakerinPembimbing::with(['guru', 'industri'])
            ->when($request->filled('tipe'), fn ($q) => $q->where('tipe', $request->tipe))
            ->when($request->filled('search'), fn ($q) => $q->where('nama', 'like', '%' . $request->search . '%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $guru = MasterGuru::orderBy('nama_lengkap')->get();
        $industri = PrakerinIndustri::orderBy('nama_industri')->get();
        $schoolName = AppSetting::first()?->school_name ?? config('app.name', 'Sekolah');

        return view('pages.prakerin.pembimbing.index', compact('pembimbings', 'guru', 'industri', 'schoolName'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($data['tipe'] === 'internal' && $request->filled('master_guru_id')) {
            $guru = MasterGuru::find($request->master_guru_id);
            $data['nama'] = $guru?->nama_lengkap ?? ($data['nama'] ?? 'Pembimbing Internal');
            $data['prakerin_industri_id'] = null;
        } else {
            $data['master_guru_id'] = null;
        }

        PrakerinPembimbing::create($data + ['is_active' => $request->boolean('is_active', true)]);
        toast('Data pembimbing berhasil disimpan.', 'success');

        return back();
    }

    public function update(Request $request, PrakerinPembimbing $pembimbing)
    {
        $data = $this->validated($request);

        if ($data['tipe'] === 'internal' && $request->filled('master_guru_id')) {
            $guru = MasterGuru::find($request->master_guru_id);
            $data['nama'] = $guru?->nama_lengkap ?? ($data['nama'] ?? 'Pembimbing Internal');
            $data['prakerin_industri_id'] = null;
        } else {
            $data['master_guru_id'] = null;
        }

        $pembimbing->update($data + ['is_active' => $request->boolean('is_active')]);
        toast('Data pembimbing berhasil diperbarui.', 'success');

        return back();
    }

    public function destroy(PrakerinPembimbing $pembimbing)
    {
        $pembimbing->delete();
        toast('Data pembimbing berhasil dihapus.', 'success');

        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tipe' => 'required|in:internal,external',
            'master_guru_id' => 'nullable|required_if:tipe,internal|exists:master_gurus,id',
            'prakerin_industri_id' => 'nullable|required_if:tipe,external|exists:prakerin_industris,id',
            'nama' => 'nullable|required_if:tipe,external|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
        ]);
    }
}
