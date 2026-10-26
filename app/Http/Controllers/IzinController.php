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
                $dokumenPath = $request->file('dokumen_pendukung')->store('public/dokumen_izin');
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
                    Storage::delete($dokumenPath);
                }
                $dokumenPath = $request->file('dokumen_pendukung')->store('public/dokumen_izin');
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