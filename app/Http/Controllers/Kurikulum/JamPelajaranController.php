<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\JamPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JamPelajaranController extends Controller
{
    public function index()
    {
        $jamPelajaran = JamPelajaran::orderBy('jam_ke')->orderBy('hari')->get();
        return view('pages.kurikulum.jam-pelajaran.index', compact('jamPelajaran'));
    }

    public function create()
    {
        return view('pages.kurikulum.jam-pelajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jam_ke' => 'required|integer',
            'hari' => 'nullable|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tipe_kegiatan' => 'nullable|string|in:istirahat,sholawat_pagi,upacara,ishoma,kegiatan_4r',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Custom validation: check if (jam_ke, hari) combination already exists
        $exists = JamPelajaran::where('jam_ke', $request->jam_ke)
            ->where('hari', $request->hari)
            ->exists();

        if ($exists) {
            toast('Kombinasi Jam Ke-' . $request->jam_ke . ' untuk hari ' . ($request->hari ?? 'Umum') . ' sudah ada.', 'error');
            return back()->withInput();
        }

        try {
            JamPelajaran::create($request->all());
            toast('Jam pelajaran berhasil ditambahkan.', 'success');
            return redirect()->route('kurikulum.jam-pelajaran.index');
        } catch (\Exception $e) {
            Log::error('Error storing time slot: ' . $e->getMessage());
            toast('Gagal menambahkan jam pelajaran.', 'error');
            return back()->withInput();
        }
    }

    public function edit(JamPelajaran $jamPelajaran)
    {
        return view('pages.kurikulum.jam-pelajaran.edit', compact('jamPelajaran'));
    }

    public function update(Request $request, JamPelajaran $jamPelajaran)
    {
        $request->validate([
            'jam_ke' => 'required|integer',
            'hari' => 'nullable|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tipe_kegiatan' => 'nullable|string|in:istirahat,sholawat_pagi,upacara,ishoma,kegiatan_4r',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Custom validation for update
        $exists = JamPelajaran::where('jam_ke', $request->jam_ke)
            ->where('hari', $request->hari)
            ->where('id', '!=', $jamPelajaran->id)
            ->exists();

        if ($exists) {
            toast('Kombinasi Jam Ke-' . $request->jam_ke . ' untuk hari ' . ($request->hari ?? 'Umum') . ' sudah ada.', 'error');
            return back()->withInput();
        }

        try {
            $jamPelajaran->update($request->all());
            toast('Jam pelajaran berhasil diperbarui.', 'success');
            return redirect()->route('kurikulum.jam-pelajaran.index');
        } catch (\Exception $e) {
            Log::error('Error updating time slot: ' . $e->getMessage());
            toast('Gagal memperbarui jam pelajaran.', 'error');
            return back()->withInput();
        }
    }

    public function destroy(JamPelajaran $jamPelajaran)
    {
        try {
            $jamPelajaran->delete();
            toast('Jam pelajaran berhasil dihapus.', 'success');
        } catch (\Exception $e) {
            Log::error('Error deleting time slot: ' . $e->getMessage());
            toast('Gagal menghapus jam pelajaran. Mungkin masih digunakan.', 'error');
        }
        return redirect()->route('kurikulum.jam-pelajaran.index');
    }
}
