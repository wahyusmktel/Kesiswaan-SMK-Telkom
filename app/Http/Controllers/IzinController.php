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
            $user = Auth::user();
            $query = Perizinan::where('user_id', $user->id);

            // Logika Pencarian
            if ($request->has('search') && $request->search != '') {
                $query->where('keterangan', 'like', '%' . $request->search . '%');
            }

            $perizinan = $query->latest()->paginate(10);

            // Data Siswa, Kelas & Wali Kelas untuk header readonly form modal
            $siswa = $user->masterSiswa;
            $tahunAktif = TahunPelajaran::where('is_active', true)->first();
            $rombel = null;
            if ($siswa) {
                if ($tahunAktif) {
                    $rombel = $siswa->rombels()->where('tahun_pelajaran_id', $tahunAktif->id)->first();
                }
                if (!$rombel) {
                    $rombel = $siswa->rombels()->latest()->first();
                }
            }
            $waliKelas = $rombel?->waliKelas;

            return view('pages.izin.index', compact('perizinan', 'siswa', 'rombel', 'waliKelas'));
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
        $kategori = $request->input('kategori_izin');
        $durasiSakit = $request->input('durasi_sakit');

        $rules = [
            'tanggal_izin' => 'required|date',
            'kategori_izin' => 'required|string',
        ];

        $messages = [
            'tanggal_izin.required' => 'Tanggal izin wajib diisi.',
            'kategori_izin.required' => 'Silakan pilih kategori perizinan.',
            'durasi_sakit.required' => 'Pilih durasi atau kondisi izin sakit.',
            'dokumen_pendukung.required' => match ($durasiSakit) {
                'rawat_inap' => 'Wajib melampirkan surat keterangan rawat inap resmi dari rumah sakit.',
                'lebih_dari_1_hari' => 'Wajib melampirkan surat keterangan sakit resmi dari faskes (Puskesmas / RS / Klinik).',
                default => 'Wajib melampirkan foto obat atau surat keterangan dokter/kesehatan.',
            },
            'dokumen_pendukung.mimes' => 'Format file dokumen tidak didukung (gunakan JPG, PNG, atau PDF).',
            'dokumen_pendukung.max' => 'Ukuran file dokumen maksimal 5MB.',
        ];

        if ($kategori === 'sakit') {
            $rules['durasi_sakit'] = 'required|in:1_hari,lebih_dari_1_hari,rawat_inap';
            if ($durasiSakit === '1_hari') {
                $rules['dokumen_pendukung'] = 'required|file|mimes:jpg,jpeg,png|max:5120';
            } else {
                $rules['dokumen_pendukung'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:5120';
            }
        } else {
            $rules['dokumen_pendukung'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            toast($validator->errors()->first(), 'error');
            return redirect()->back()->withErrors($validator)->withInput()->with('open_modal', 'ajukan-izin-modal');
        }

        try {
            $dokumenPath = null;
            if ($request->hasFile('dokumen_pendukung')) {
                $dokumenPath = $request->file('dokumen_pendukung')->store('dokumen_izin', 'public');
            }

            // Tentukan jenis_izin untuk kolom database enum ('sakit', 'izin', 'dispen')
            $jenisIzin = $kategori === 'sakit' ? 'sakit' : 'izin';

            // Susun teks keterangan yang deskriptif
            $kategoriLabel = match ($kategori) {
                'sakit' => match ($durasiSakit) {
                    'rawat_inap' => 'Izin Sakit Berat / Rawat Inap (Opname di Rumah Sakit)',
                    'lebih_dari_1_hari' => 'Izin Sakit (Lebih dari 1 Hari - Maks. 3 Hari)',
                    default => 'Izin Sakit (1 Hari)',
                },
                'acara_keluarga' => 'Izin - Acara / Urusan Keluarga Inti (1 Hari)',
                'duka_cita' => 'Izin - Takziah / Duka Cita Keluarga (1 Hari)',
                'kepentingan_mendesak' => 'Izin - Kepentingan Pribadi Mendesak (1 Hari)',
                default => 'Izin - Keperluan Lainnya (1 Hari)',
            };

            $catatan = trim((string) $request->input('catatan'));
            $keteranganLengkap = $kategoriLabel . ($catatan !== '' ? ' — ' . $catatan : '');

            $perizinan = Perizinan::create([
                'user_id' => Auth::id(),
                'tanggal_izin' => $request->tanggal_izin,
                'jenis_izin' => $jenisIzin,
                'keterangan' => $keteranganLengkap,
                'dokumen_pendukung' => $dokumenPath,
                'status' => 'diajukan',
            ]);

            $masterSiswa = Auth::user()->masterSiswa;
            $tahunAktif = TahunPelajaran::where('is_active', true)->first();
            
            if ($tahunAktif && $masterSiswa) {
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

        $kategori = $request->input('kategori_izin');
        $durasiSakit = $request->input('durasi_sakit');

        $rules = [
            'tanggal_izin' => 'required|date',
            'dokumen_pendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];

        if ($kategori) {
            $rules['kategori_izin'] = 'required|string';
            if ($kategori === 'sakit') {
                $rules['durasi_sakit'] = 'required|in:1_hari,lebih_dari_1_hari,rawat_inap';
            }
        } else {
            $rules['keterangan'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            toast($validator->errors()->first(), 'error');
            return redirect()->back()->withErrors($validator)->withInput()->with('open_modal', 'edit-izin-modal-'.$perizinan->id);
        }

        try {
            $dokumenPath = $perizinan->dokumen_pendukung;
            if ($request->hasFile('dokumen_pendukung')) {
                // Hapus file lama jika ada
                if ($dokumenPath) {
                    $cleanOld = preg_replace('#^public/#', '', $dokumenPath);
                    Storage::disk('public')->delete($cleanOld);
                    Storage::delete($dokumenPath);
                }
                $dokumenPath = $request->file('dokumen_pendukung')->store('dokumen_izin', 'public');
            }

            $updateData = [
                'tanggal_izin' => $request->tanggal_izin,
                'dokumen_pendukung' => $dokumenPath,
            ];

            if ($kategori) {
                $updateData['jenis_izin'] = $kategori === 'sakit' ? 'sakit' : 'izin';
                $kategoriLabel = match ($kategori) {
                    'sakit' => match ($durasiSakit) {
                        'rawat_inap' => 'Izin Sakit Berat / Rawat Inap (Opname di Rumah Sakit)',
                        'lebih_dari_1_hari' => 'Izin Sakit (Lebih dari 1 Hari - Maks. 3 Hari)',
                        default => 'Izin Sakit (1 Hari)',
                    },
                    'acara_keluarga' => 'Izin - Acara / Urusan Keluarga Inti (1 Hari)',
                    'duka_cita' => 'Izin - Takziah / Duka Cita Keluarga (1 Hari)',
                    'kepentingan_mendesak' => 'Izin - Kepentingan Pribadi Mendesak (1 Hari)',
                    default => 'Izin - Keperluan Lainnya (1 Hari)',
                };
                $catatan = trim((string) $request->input('catatan'));
                $updateData['keterangan'] = $kategoriLabel . ($catatan !== '' ? ' — ' . $catatan : '');
            } else {
                $updateData['keterangan'] = $request->keterangan;
            }

            $perizinan->update($updateData);

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
                $cleanOld = preg_replace('#^public/#', '', $perizinan->dokumen_pendukung);
                Storage::disk('public')->delete($cleanOld);
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

    /**
     * Menampilkan dokumen lampiran perizinan secara aman.
     */
    public function showDokumen(Perizinan $perizinan)
    {
        if (! $perizinan->dokumen_pendukung) {
            abort(404, 'Dokumen lampiran tidak ditemukan.');
        }

        $rawPath = $perizinan->dokumen_pendukung;
        $cleanPath = preg_replace('#^public/#', '', $rawPath);

        $candidatePaths = [
            storage_path('app/public/' . $cleanPath),
            storage_path('app/public/' . $rawPath),
            storage_path('app/private/' . $rawPath),
            storage_path('app/private/' . $cleanPath),
            storage_path('app/private/public/' . $cleanPath),
            storage_path('app/' . $rawPath),
            storage_path('app/' . $cleanPath),
            public_path('storage/' . $cleanPath),
            public_path('storage/' . $rawPath),
        ];

        foreach ($candidatePaths as $fullPath) {
            if (file_exists($fullPath) && is_file($fullPath)) {
                $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
                return response()->file($fullPath, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
                ]);
            }
        }

        return response('
            <div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f8fafc; margin: 0; padding: 20px; box-sizing: border-box;">
                <div style="background: white; border-radius: 12px; padding: 32px; max-width: 480px; width: 100%; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
                    <div style="width: 56px; height: 56px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <svg style="width: 28px; height: 28px; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h2 style="color: #0f172a; margin: 0 0 8px 0; font-size: 20px; font-weight: 700;">Berkas Tidak Ditemukan</h2>
                    <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin: 0 0 20px 0;">File fisik dokumen lampiran perizinan ini tidak ditemukan pada penyimpanan server (<code>' . htmlspecialchars($cleanPath) . '</code>).</p>
                    <button onclick="window.close()" style="background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s;">Tutup Jendela</button>
                </div>
            </div>', 404, ['Content-Type' => 'text/html']);
    }
}