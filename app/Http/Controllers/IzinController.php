<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PengajuanIzinMasuk;

class IzinController extends Controller
{
    /**
     * Menampilkan halaman riwayat perizinan dengan pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        try {
            $query = Perizinan::where('user_id', Auth::id());

            // Logika Pencarian
            if ($request->has('search') && $request->search != '') {
                $query->where('keterangan', 'like', '%' . $request->search . '%');
            }

            $perizinan = $query->latest()->paginate(10);

            return view('pages.izin.index', compact('perizinan'));
        } catch (\Exception $e) {
            Log::error('Error fetching permission history: ' . $e->getMessage());
            toast('Gagal memuat riwayat perizinan.', 'error');
            return redirect()->back();
        }
    }

    /**
     * Menyimpan pengajuan izin baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_izin' => 'required|date',
            'keterangan' => 'required|string|min:10',
            'dokumen_pendukung' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            toast($validator->errors()->first(), 'error');
            return redirect()->back()->withErrors($validator)->withInput()->with('open_modal', 'ajukan-izin-modal');
        }

        try {
            $dokumenPath = null;
            if ($request->hasFile('dokumen_pendukung')) {
                $dokumenPath = $request->file('dokumen_pendukung')->store('public/dokumen_izin');
            }

            $perizinan = Perizinan::create([
                'user_id' => Auth::id(),
                'tanggal_izin' => $request->tanggal_izin,
                'jenis_izin' => 'izin',
                'keterangan' => $request->keterangan,
                'dokumen_pendukung' => $dokumenPath,
                'status' => 'diajukan',
            ]);

            $masterSiswa = Auth::user()->masterSiswa;
            $tahunAktif = TahunPelajaran::where('is_active', true)->first();
            
            if ($tahunAktif) {
                $rombelAktif = $masterSiswa->rombels()
                    ->where('tahun_pelajaran_id', $tahunAktif->id)
                    ->first();
                    
                if ($rombelAktif && $rombelAktif->waliKelas) {
                    $waliKelas = $rombelAktif->waliKelas;
                    $waliKelas->notify(new PengajuanIzinMasuk($perizinan));
                }
            }

            toast('Pengajuan izin berhasil dikirim!', 'success');
            return redirect()->route('izin.index');
        } catch (\Exception $e) {
            Log::error('Error creating permission: ' . $e->getMessage());
            toast('Terjadi kesalahan saat mengirim pengajuan.', 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Mengupdate pengajuan izin yang ada.
     */
    public function update(Request $request, Perizinan $perizinan)
    {
        // Otorisasi: Pastikan user adalah pemilik dan status masih 'diajukan'
        if ($perizinan->user_id !== Auth::id() || $perizinan->status !== 'diajukan') {
            toast('Anda tidak memiliki izin untuk mengubah pengajuan ini.', 'error');
            return redirect()->route('izin.index');
        }

        $validator = Validator::make($request->all(), [
            'tanggal_izin' => 'required|date',
            'keterangan' => 'required|string|min:10',
            'dokumen_pendukung' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            toast($validator->errors()->first(), 'error');
            return redirect()->back()->withErrors($validator)->withInput()->with('open_modal', 'edit-izin-modal-'.$perizinan->id);
        }

        try {
            $dokumenPath = $perizinan->dokumen_pendukung;
            if ($request->hasFile('dokumen_pendukung')) {
                // Hapus file lama jika ada
                if ($dokumenPath) {
                    Storage::delete($dokumenPath);
                }
                $dokumenPath = $request->file('dokumen_pendukung')->store('public/dokumen_izin');
            }

            $perizinan->update([
                'tanggal_izin' => $request->tanggal_izin,
                'keterangan' => $request->keterangan,
                'dokumen_pendukung' => $dokumenPath,
            ]);

            toast('Pengajuan izin berhasil diperbarui!', 'success');
            return redirect()->route('izin.index');
        } catch (\Exception $e) {
            Log::error('Error updating permission: ' . $e->getMessage());
            toast('Terjadi kesalahan saat memperbarui pengajuan.', 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Membatalkan (menghapus) pengajuan izin.
     */
    public function destroy(Perizinan $perizinan)
    {
        // Otorisasi: Pastikan user adalah pemilik dan status masih 'diajukan'
        if ($perizinan->user_id !== Auth::id() || $perizinan->status !== 'diajukan') {
            toast('Anda tidak memiliki izin untuk membatalkan pengajuan ini.', 'error');
            return redirect()->route('izin.index');
        }

        try {
            // Hapus file terkait jika ada
            if ($perizinan->dokumen_pendukung) {
                Storage::delete($perizinan->dokumen_pendukung);
            }
            $perizinan->delete();
            toast('Pengajuan izin berhasil dibatalkan.', 'success');
            return redirect()->route('izin.index');
        } catch (\Exception $e) {
            Log::error('Error deleting permission: ' . $e->getMessage());
            toast('Gagal membatalkan pengajuan.', 'error');
            return redirect()->route('izin.index');
        }
    }
}