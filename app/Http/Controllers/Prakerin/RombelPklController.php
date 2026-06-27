<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\PrakerinIndustri;
use App\Models\PrakerinPembimbing;
use App\Models\PrakerinPenempatan;
use App\Models\PrakerinRombel;
use App\Models\PrakerinSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RombelPklController extends Controller
{
    public function index()
    {
        $rombels = PrakerinRombel::with(['industri', 'pembimbingInternal', 'pembimbingExternal'])
            ->withCount('penempatans')
            ->latest()
            ->paginate(12);
        $industri = PrakerinIndustri::orderBy('nama_industri')->get();
        $internal = PrakerinPembimbing::where('tipe', 'internal')->where('is_active', true)->orderBy('nama')->get();
        $external = PrakerinPembimbing::with(['industri', 'guru'])->where('tipe', 'external')->where('is_active', true)->orderBy('nama')->get();

        return view('pages.prakerin.rombel.index', compact('rombels', 'industri', 'internal', 'external'));
    }

    public function store(Request $request)
    {
        PrakerinRombel::create($this->validated($request));
        toast('Rombel PKL berhasil dibuat.', 'success');

        return back();
    }

    public function update(Request $request, PrakerinRombel $rombel)
    {
        $rombel->update($this->validated($request));
        toast('Rombel PKL berhasil diperbarui.', 'success');

        return back();
    }

    public function destroy(PrakerinRombel $rombel)
    {
        $rombel->delete();
        toast('Rombel PKL berhasil dihapus.', 'success');

        return back();
    }

    public function mapping(Request $request, PrakerinRombel $rombel)
    {
        $rombel->load(['industri', 'pembimbingInternal.guru', 'pembimbingExternal.industri', 'penempatans.siswa.rombels.kelas']);

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mappedIds = PrakerinPenempatan::where('status', 'aktif')->pluck('master_siswa_id');

        $siswa = MasterSiswa::with('rombels.kelas')
            ->whereNotIn('id', $mappedIds)
            ->when($request->filled('kelas_id'), function ($query) use ($request) {
                $query->whereHas('rombels', fn ($q) => $q->where('kelas_id', $request->kelas_id));
            })
            ->when($request->filled('search'), fn ($query) => $query->where(function ($search) use ($request) {
                $search->where('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('nis', 'like', '%' . $request->search . '%');
            }))
            ->orderBy('nama_lengkap')
            ->paginate(20)
            ->withQueryString();

        return view('pages.prakerin.rombel.mapping', compact('rombel', 'kelas', 'siswa'));
    }

    public function storeMapping(Request $request, PrakerinRombel $rombel)
    {
        $data = $request->validate([
            'master_siswa_ids' => 'required|array',
            'master_siswa_ids.*' => 'exists:master_siswa,id',
        ]);

        DB::transaction(function () use ($data, $rombel) {
            foreach ($data['master_siswa_ids'] as $siswaId) {
                PrakerinPenempatan::updateOrCreate(
                    ['master_siswa_id' => $siswaId],
                    [
                        'prakerin_rombel_id' => $rombel->id,
                        'prakerin_industri_id' => $rombel->prakerin_industri_id,
                        'master_guru_id' => $rombel->pembimbingInternal?->master_guru_id,
                        'nama_pembimbing_industri' => $rombel->pembimbingExternal?->nama ?? '-',
                        'tanggal_mulai' => $this->effectiveTanggalMulai($rombel),
                        'tanggal_selesai' => $this->effectiveTanggalSelesai($rombel),
                        'status' => 'aktif',
                    ]
                );
            }
        });

        toast('Siswa berhasil dimapping ke rombel PKL.', 'success');

        return back();
    }

    public function removeMapping(PrakerinRombel $rombel, PrakerinPenempatan $penempatan)
    {
        abort_unless($penempatan->prakerin_rombel_id === $rombel->id, 404);
        $penempatan->delete();
        toast('Mapping siswa berhasil dilepas.', 'success');

        return back();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nama_rombel' => 'required|string|max:255',
            'prakerin_industri_id' => 'required|exists:prakerin_industris,id',
            'pembimbing_internal_id' => [
                'required',
                Rule::exists('prakerin_pembimbings', 'id')->where('tipe', 'internal')->where('is_active', true),
            ],
            'pembimbing_external_id' => [
                'required',
                Rule::exists('prakerin_pembimbings', 'id')
                    ->where('tipe', 'external')
                    ->where('is_active', true)
                    ->where('prakerin_industri_id', $request->input('prakerin_industri_id')),
            ],
            'gunakan_periode_kustom' => 'nullable|boolean',
            'tanggal_mulai' => 'required_if:gunakan_periode_kustom,1|nullable|date',
            'tanggal_selesai' => 'required_if:gunakan_periode_kustom,1|nullable|date|after_or_equal:tanggal_mulai',
            'gunakan_waktu_absensi_kustom' => 'nullable|boolean',
            'jam_check_in_mulai' => 'required_if:gunakan_waktu_absensi_kustom,1|nullable|date_format:H:i',
            'jam_check_in_selesai' => 'required_if:gunakan_waktu_absensi_kustom,1|nullable|date_format:H:i',
            'jam_check_out_mulai' => 'required_if:gunakan_waktu_absensi_kustom,1|nullable|date_format:H:i',
            'jam_check_out_selesai' => 'required_if:gunakan_waktu_absensi_kustom,1|nullable|date_format:H:i',
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        $data['gunakan_periode_kustom'] = $request->boolean('gunakan_periode_kustom');
        $data['gunakan_waktu_absensi_kustom'] = $request->boolean('gunakan_waktu_absensi_kustom');

        if (! $data['gunakan_periode_kustom']) {
            $data['tanggal_mulai'] = null;
            $data['tanggal_selesai'] = null;
        }

        if (! $data['gunakan_waktu_absensi_kustom']) {
            $data['jam_check_in_mulai'] = null;
            $data['jam_check_in_selesai'] = null;
            $data['jam_check_out_mulai'] = null;
            $data['jam_check_out_selesai'] = null;
        }

        return $data;
    }

    private function effectiveTanggalMulai(PrakerinRombel $rombel): string
    {
        if ($rombel->gunakan_periode_kustom && $rombel->tanggal_mulai) {
            return $rombel->tanggal_mulai->toDateString();
        }

        return PrakerinSetting::first()?->tanggal_mulai?->toDateString() ?? now()->toDateString();
    }

    private function effectiveTanggalSelesai(PrakerinRombel $rombel): string
    {
        if ($rombel->gunakan_periode_kustom && $rombel->tanggal_selesai) {
            return $rombel->tanggal_selesai->toDateString();
        }

        return PrakerinSetting::first()?->tanggal_selesai?->toDateString() ?? now()->addMonths(3)->toDateString();
    }
}
