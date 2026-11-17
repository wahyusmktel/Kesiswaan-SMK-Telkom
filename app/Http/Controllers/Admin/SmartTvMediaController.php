<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfoTicker;
use App\Models\SmartTvMedia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class SmartTvMediaController extends Controller
{
    private const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function index()
    {
        $mediaList = SmartTvMedia::with('user')
            ->orderBy('mode_tayang', 'asc') // eksklusif dulu, lalu kombinasi
            ->orderBy('urutan', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $tickers = InfoTicker::with('creator')->latest()->get();

        $now = Carbon::now();
        $activeFullscreenTotalCount = $mediaList->filter(fn($m) => $m->mode_tayang === 'fullscreen_total' && $m->isScheduleActiveNow($now))->count();
        $activeExclusiveCount = $mediaList->filter(fn($m) => $m->mode_tayang === 'eksklusif' && $m->isScheduleActiveNow($now))->count();
        $activeKombinasiCount = $mediaList->filter(fn($m) => $m->mode_tayang === 'kombinasi' && $m->isScheduleActiveNow($now))->count();

        return view('pages.smart-tv.index', [
            'mediaList' => $mediaList,
            'tickers' => $tickers,
            'days' => self::DAYS,
            'now' => $now,
            'activeFullscreenTotalCount' => $activeFullscreenTotalCount,
            'activeExclusiveCount' => $activeExclusiveCount,
            'activeKombinasiCount' => $activeKombinasiCount,
        ]);
    }

    public function storeMedia(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'tipe' => 'required|in:qr_hadir,qr_pulang,mading,pengumuman,lainnya',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:25600', // max 25MB
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'hari' => 'nullable|array',
            'hari.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'mode_tayang' => 'required|in:eksklusif,kombinasi,fullscreen_total',
            'durasi_detik' => 'nullable|integer|min:5|max:300',
            'urutan' => 'nullable|integer|min:0|max:999',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'file.mimes' => 'Format file harus berupa PDF, JPG, JPEG, PNG, atau WEBP.',
            'file.max' => 'Ukuran file maksimal 25 MB.',
            'jam_mulai.required' => 'Jam mulai tayang harus diisi.',
            'jam_selesai.required' => 'Jam selesai tayang harus diisi.',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $fileType = ($ext === 'pdf') ? 'pdf' : 'image';

        $fileName = 'tv_' . time() . '_' . uniqid() . '.' . $ext;
        $filePath = $file->storeAs('smart-tv/media', $fileName, 'public');

        SmartTvMedia::create([
            'judul' => trim($validated['judul']),
            'tipe' => $validated['tipe'],
            'file_path' => $filePath,
            'file_type' => $fileType,
            'jam_mulai' => $validated['jam_mulai'] . ':00',
            'jam_selesai' => $validated['jam_selesai'] . ':00',
            'hari' => ! empty($validated['hari']) ? array_values($validated['hari']) : null,
            'mode_tayang' => $validated['mode_tayang'],
            'durasi_detik' => $validated['durasi_detik'] ?: 20,
            'urutan' => $validated['urutan'] ?? 0,
            'is_active' => true,
            'keterangan' => $validated['keterangan'] ? trim($validated['keterangan']) : null,
            'created_by' => Auth::id(),
        ]);

        toast('Media tayang Smart TV berhasil diunggah dan dijadwalkan.', 'success');
        return back();
    }

    public function updateMedia(Request $request, SmartTvMedia $media)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'tipe' => 'required|in:qr_hadir,qr_pulang,mading,pengumuman,lainnya',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:25600',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'hari' => 'nullable|array',
            'hari.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'mode_tayang' => 'required|in:eksklusif,kombinasi,fullscreen_total',
            'durasi_detik' => 'nullable|integer|min:5|max:300',
            'urutan' => 'nullable|integer|min:0|max:999',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $filePath = $media->file_path;
        $fileType = $media->file_type;

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }

            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileType = ($ext === 'pdf') ? 'pdf' : 'image';
            $fileName = 'tv_' . time() . '_' . uniqid() . '.' . $ext;
            $filePath = $file->storeAs('smart-tv/media', $fileName, 'public');
        }

        $media->update([
            'judul' => trim($validated['judul']),
            'tipe' => $validated['tipe'],
            'file_path' => $filePath,
            'file_type' => $fileType,
            'jam_mulai' => $validated['jam_mulai'] . ':00',
            'jam_selesai' => $validated['jam_selesai'] . ':00',
            'hari' => ! empty($validated['hari']) ? array_values($validated['hari']) : null,
            'mode_tayang' => $validated['mode_tayang'],
            'durasi_detik' => $validated['durasi_detik'] ?: 20,
            'urutan' => $validated['urutan'] ?? 0,
            'keterangan' => $validated['keterangan'] ? trim($validated['keterangan']) : null,
        ]);

        toast('Pengaturan media tayang berhasil diperbarui.', 'success');
        return back();
    }

    public function toggleMedia(SmartTvMedia $media)
    {
        $media->update(['is_active' => ! $media->is_active]);
        $statusStr = $media->is_active ? 'diaktifkan' : 'dinonaktifkan';
        toast("Media '{$media->judul}' berhasil {$statusStr}.", 'success');
        return back();
    }

    public function destroyMedia(SmartTvMedia $media)
    {
        if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();
        toast('Media tayang berhasil dihapus.', 'success');
        return back();
    }

    public function storeTicker(Request $request)
    {
        $request->validate(['konten' => 'required|string|max:500']);

        InfoTicker::create([
            'konten' => trim($request->konten),
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        toast('Running text berhasil ditambahkan.', 'success');
        return back();
    }

    public function toggleTicker(InfoTicker $ticker)
    {
        $ticker->update(['is_active' => ! $ticker->is_active]);
        return back();
    }

    public function destroyTicker(InfoTicker $ticker)
    {
        $ticker->delete();
        toast('Running text berhasil dihapus.', 'success');
        return back();
    }
}
