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
            'hari' => 'nullable|array',
            'hari.*' => 'nullable|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tipe_kegiatan' => 'nullable|string|in:istirahat,sholawat_pagi,upacara,ishoma,kegiatan_4r',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $days = $request->hari ?? [null];
        
        // Filter out empty strings if any (though nullable|array should handle it, Alpine might send empty string)
        $days = array_map(fn($d) => $d === "" ? null : $d, $days);
        $days = array_unique($days);

        $successCount = 0;
        $failCount = 0;
        $errors = [];

        foreach ($days as $hari) {
            // Custom validation: check if (jam_ke, hari) combination already exists
            $exists = JamPelajaran::where('jam_ke', $request->jam_ke)
                ->where('hari', $hari)
                ->exists();

            if ($exists) {
                $errors[] = 'Kombinasi Jam Ke-' . $request->jam_ke . ' untuk hari ' . ($hari ?? 'Umum') . ' sudah ada.';
                $failCount++;
                continue;
            }

            try {
                JamPelajaran::create([
                    'jam_ke' => $request->jam_ke,
                    'hari' => $hari,
                    'jam_mulai' => $request->jam_mulai,
                    'jam_selesai' => $request->jam_selesai,
                    'tipe_kegiatan' => $request->tipe_kegiatan,
                    'keterangan' => $request->keterangan,
                ]);
                $successCount++;
            } catch (\Exception $e) {
                Log::error('Error storing time slot for day ' . ($hari ?? 'Umum') . ': ' . $e->getMessage());
                $failCount++;
            }
        }

        if ($successCount > 0) {
            toast($successCount . ' Jam pelajaran berhasil ditambahkan.', 'success');
        }

        if ($failCount > 0) {
            foreach ($errors as $error) {
                toast($error, 'error');
            }
            if (empty($errors)) {
                toast('Gagal menambahkan beberapa jam pelajaran.', 'error');
            }
        }

        return redirect()->route('kurikulum.jam-pelajaran.index');
    }

    public function edit(JamPelajaran $jamPelajaran)
    {
        return view('pages.kurikulum.jam-pelajaran.edit', compact('jamPelajaran'));
    }

    public function update(Request $request, JamPelajaran $jamPelajaran)
    {
        $request->validate([
            'jam_ke' => 'required|integer',
            'hari' => 'nullable|array',
            'hari.*' => 'nullable|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tipe_kegiatan' => 'nullable|string|in:istirahat,sholawat_pagi,upacara,ishoma,kegiatan_4r',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Get single day for update (usually edit only affects one record)
        $hari = $request->hari[0] ?? null;
        if ($hari === "") $hari = null;

        // Custom validation for update
        $exists = JamPelajaran::where('jam_ke', $request->jam_ke)
            ->where('hari', $hari)
            ->where('id', '!=', $jamPelajaran->id)
            ->exists();

        if ($exists) {
            toast('Kombinasi Jam Ke-' . $request->jam_ke . ' untuk hari ' . ($hari ?? 'Umum') . ' sudah ada.', 'error');
            return back()->withInput();
        }

        try {
            $jamPelajaran->update([
                'jam_ke' => $request->jam_ke,
                'hari' => $hari,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'tipe_kegiatan' => $request->tipe_kegiatan,
                'keterangan' => $request->keterangan,
            ]);
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
