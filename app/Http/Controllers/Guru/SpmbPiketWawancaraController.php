<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\SpmbFee;
use App\Models\SpmbPesertaSeleksi;
use App\Models\SpmbWawancaraJawaban;
use App\Models\SpmbWawancaraPertanyaan;
use App\Models\SpmbWawancaraSesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SpmbPiketWawancaraController extends Controller
{
    /**
     * Show Candidate (Student) Interview Form.
     */
    public function formSiswa(SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $pesertaSeleksi->loadMissing(['tanggal.kelompok', 'user']);

        $sesi = SpmbWawancaraSesi::firstOrCreate(
            ['spmb_peserta_seleksi_id' => $pesertaSeleksi->id],
            [
                'spmb_piket_tanggal_id' => $pesertaSeleksi->spmb_piket_tanggal_id,
                'tahun_ajaran' => $pesertaSeleksi->tahun_ajaran,
                'status_wawancara_siswa' => 'Belum',
                'status_wawancara_ortu' => 'Belum',
            ]
        );

        $pertanyaans = SpmbWawancaraPertanyaan::where('kategori', 'siswa')
            ->where('is_active', true)
            ->orderBy('nomor_urut', 'asc')
            ->get();

        $jawabans = $sesi->jawabans()
            ->where('kategori', 'siswa')
            ->get()
            ->keyBy('spmb_wawancara_pertanyaan_id');

        $user = Auth::user();

        return view('pages.guru.spmb-piket.wawancara-siswa', compact(
            'pesertaSeleksi',
            'sesi',
            'pertanyaans',
            'jawabans',
            'user'
        ));
    }

    /**
     * Store Candidate (Student) Interview Responses.
     */
    public function storeSiswa(Request $request, SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $sesi = SpmbWawancaraSesi::firstOrCreate(
            ['spmb_peserta_seleksi_id' => $pesertaSeleksi->id],
            [
                'spmb_piket_tanggal_id' => $pesertaSeleksi->spmb_piket_tanggal_id,
                'tahun_ajaran' => $pesertaSeleksi->tahun_ajaran,
            ]
        );

        $pertanyaans = SpmbWawancaraPertanyaan::where('kategori', 'siswa')
            ->where('is_active', true)
            ->get();

        $rawJawaban = $request->input('jawaban', []);
        $rawOther = $request->input('jawaban_other', []);

        DB::beginTransaction();
        try {
            $totalPointsEarned = 0;
            $maxPossiblePoints = 0;

            foreach ($pertanyaans as $p) {
                $ansVal = $rawJawaban[$p->id] ?? null;
                $otherVal = $rawOther[$p->id] ?? null;
                $skorPerItem = 0;

                // Sync basic candidate profile fields if edited
                if ($p->field_key === 'no_wa_siswa' && ! empty($ansVal)) {
                    $pesertaSeleksi->update(['no_wa_siswa' => $ansVal]);
                } elseif ($p->field_key === 'asal_sekolah' && ! empty($ansVal)) {
                    $pesertaSeleksi->update(['asal_sekolah' => $ansVal]);
                } elseif ($p->field_key === 'jurusan_pilihan' && is_array($ansVal)) {
                    $pesertaSeleksi->update([
                        'jurusan_pilihan' => $ansVal[0] ?? $pesertaSeleksi->jurusan_pilihan,
                        'jurusan_pilihan_2' => $ansVal[1] ?? $pesertaSeleksi->jurusan_pilihan_2,
                    ]);
                }

                // Calculate item score
                if ($p->skor_opsi && is_array($p->skor_opsi)) {
                    $maxItem = max(array_values($p->skor_opsi));
                    $maxPossiblePoints += $maxItem;

                    if (is_string($ansVal) && isset($p->skor_opsi[$ansVal])) {
                        $skorPerItem = (float) $p->skor_opsi[$ansVal];
                        $totalPointsEarned += $skorPerItem;
                    }
                } elseif ($p->tipe_input === 'scale_1_5') {
                    $maxPossiblePoints += 20;
                    if (is_numeric($ansVal)) {
                        $skorPerItem = ((int) $ansVal) * 4; // 1->4, 5->20
                        $totalPointsEarned += $skorPerItem;
                    }
                } elseif ($p->bobot > 0) {
                    $maxPossiblePoints += $p->bobot;
                    if (! empty($ansVal)) {
                        $skorPerItem = $p->bobot;
                        $totalPointsEarned += $skorPerItem;
                    }
                }

                // Prepare storage
                $jawabanTeks = null;
                $jawabanPilihan = null;
                $jawabanSkala = null;

                if (is_array($ansVal)) {
                    $jawabanPilihan = array_values($ansVal);
                } elseif ($p->tipe_input === 'scale_1_5') {
                    $jawabanSkala = ! empty($ansVal) ? (int) $ansVal : null;
                } elseif ($p->tipe_input !== 'file_upload') {
                    $jawabanTeks = is_string($ansVal) ? $ansVal : null;
                }

                SpmbWawancaraJawaban::updateOrCreate(
                    [
                        'spmb_wawancara_sesi_id' => $sesi->id,
                        'spmb_wawancara_pertanyaan_id' => $p->id,
                    ],
                    [
                        'kategori' => 'siswa',
                        'jawaban_teks' => $jawabanTeks,
                        'jawaban_pilihan' => $jawabanPilihan,
                        'jawaban_other' => $otherVal,
                        'jawaban_skala' => $jawabanSkala,
                        'skor_diperoleh' => $skorPerItem,
                    ]
                );
            }

            // Handle Photo Upload
            if ($request->hasFile('foto_siswa')) {
                if ($sesi->foto_siswa && Storage::disk('public')->exists($sesi->foto_siswa)) {
                    Storage::disk('public')->delete($sesi->foto_siswa);
                }
                $sesi->foto_siswa = $request->file('foto_siswa')->store('spmb-wawancara/siswa', 'public');
            }

            // Calculate percentage score (0 - 100)
            $skorAkhir = $maxPossiblePoints > 0 ? round(($totalPointsEarned / $maxPossiblePoints) * 100, 2) : 75;

            // Automated Grade Recommendation
            if ($skorAkhir >= 85) {
                $hasilAutomated = 'Sangat Baik';
            } elseif ($skorAkhir >= 70) {
                $hasilAutomated = 'Baik';
            } elseif ($skorAkhir >= 55) {
                $hasilAutomated = 'Cukup';
            } else {
                $hasilAutomated = 'Kurang Baik';
            }

            // If interviewer manually chose in final question, prioritize that or automated
            $hasilFinal = $request->input('hasil_wawancara_override') ?: ($request->input('hasil_wawancara') ?: $hasilAutomated);

            $sesi->update([
                'status_wawancara_siswa' => 'Selesai',
                'pewawancara_siswa_user_id' => Auth::id(),
                'pewawancara_siswa_nama' => Auth::user()->name,
                'waktu_wawancara_siswa' => now(),
                'skor_siswa' => $skorAkhir,
                'hasil_wawancara_siswa' => $hasilFinal,
                'catatan_pewawancara_siswa' => $request->input('catatan_pewawancara_siswa'),
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Penilaian wawancara calon siswa {$pesertaSeleksi->nama_lengkap} berhasil disimpan dengan predikat: {$hasilFinal} (Skor: {$skorAkhir}).");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan hasil wawancara siswa: ' . $e->getMessage());
        }
    }

    /**
     * Show Candidate Parent/Guardian Interview Form.
     */
    public function formOrtu(SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $pesertaSeleksi->loadMissing(['tanggal.kelompok', 'user']);

        $sesi = SpmbWawancaraSesi::firstOrCreate(
            ['spmb_peserta_seleksi_id' => $pesertaSeleksi->id],
            [
                'spmb_piket_tanggal_id' => $pesertaSeleksi->spmb_piket_tanggal_id,
                'tahun_ajaran' => $pesertaSeleksi->tahun_ajaran,
                'status_wawancara_siswa' => 'Belum',
                'status_wawancara_ortu' => 'Belum',
            ]
        );

        $pertanyaans = SpmbWawancaraPertanyaan::where('kategori', 'orang_tua')
            ->where('is_active', true)
            ->orderBy('nomor_urut', 'asc')
            ->get();

        $jawabans = $sesi->jawabans()
            ->where('kategori', 'orang_tua')
            ->get()
            ->keyBy('spmb_wawancara_pertanyaan_id');

        // Load SPMB Fees
        $spmbFees = SpmbFee::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $totalBiaya = $spmbFees->sum('amount');

        $user = Auth::user();

        return view('pages.guru.spmb-piket.wawancara-ortu', compact(
            'pesertaSeleksi',
            'sesi',
            'pertanyaans',
            'jawabans',
            'spmbFees',
            'totalBiaya',
            'user'
        ));
    }

    /**
     * Store Candidate Parent/Guardian Interview Responses.
     */
    public function storeOrtu(Request $request, SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $sesi = SpmbWawancaraSesi::firstOrCreate(
            ['spmb_peserta_seleksi_id' => $pesertaSeleksi->id],
            [
                'spmb_piket_tanggal_id' => $pesertaSeleksi->spmb_piket_tanggal_id,
                'tahun_ajaran' => $pesertaSeleksi->tahun_ajaran,
            ]
        );

        $pertanyaans = SpmbWawancaraPertanyaan::where('kategori', 'orang_tua')
            ->where('is_active', true)
            ->get();

        $rawJawaban = $request->input('jawaban', []);
        $rawOther = $request->input('jawaban_other', []);

        DB::beginTransaction();
        try {
            $totalPointsEarned = 0;
            $maxPossiblePoints = 0;

            foreach ($pertanyaans as $p) {
                $ansVal = $rawJawaban[$p->id] ?? null;
                $otherVal = $rawOther[$p->id] ?? null;
                $skorPerItem = 0;

                // Sync parent profile fields if edited
                if ($p->field_key === 'nama_orang_tua' && ! empty($ansVal)) {
                    $pesertaSeleksi->update(['nama_orang_tua' => $ansVal]);
                } elseif ($p->field_key === 'no_wa_orang_tua' && ! empty($ansVal)) {
                    $pesertaSeleksi->update(['no_wa_orang_tua' => $ansVal]);
                }

                // Calculate item score
                if ($p->skor_opsi && is_array($p->skor_opsi)) {
                    $maxItem = max(array_values($p->skor_opsi));
                    $maxPossiblePoints += $maxItem;

                    if (is_string($ansVal) && isset($p->skor_opsi[$ansVal])) {
                        $skorPerItem = (float) $p->skor_opsi[$ansVal];
                        $totalPointsEarned += $skorPerItem;
                    }
                } elseif ($p->tipe_input === 'scale_1_5') {
                    $maxPossiblePoints += 20;
                    if (is_numeric($ansVal)) {
                        $skorPerItem = ((int) $ansVal) * 4;
                        $totalPointsEarned += $skorPerItem;
                    }
                } elseif ($p->bobot > 0) {
                    $maxPossiblePoints += $p->bobot;
                    if (! empty($ansVal)) {
                        $skorPerItem = $p->bobot;
                        $totalPointsEarned += $skorPerItem;
                    }
                }

                // Prepare storage
                $jawabanTeks = null;
                $jawabanPilihan = null;
                $jawabanSkala = null;

                if (is_array($ansVal)) {
                    $jawabanPilihan = array_values($ansVal);
                } elseif ($p->tipe_input === 'scale_1_5') {
                    $jawabanSkala = ! empty($ansVal) ? (int) $ansVal : null;
                } elseif ($p->tipe_input !== 'file_upload') {
                    $jawabanTeks = is_string($ansVal) ? $ansVal : null;
                }

                SpmbWawancaraJawaban::updateOrCreate(
                    [
                        'spmb_wawancara_sesi_id' => $sesi->id,
                        'spmb_wawancara_pertanyaan_id' => $p->id,
                    ],
                    [
                        'kategori' => 'orang_tua',
                        'jawaban_teks' => $jawabanTeks,
                        'jawaban_pilihan' => $jawabanPilihan,
                        'jawaban_other' => $otherVal,
                        'jawaban_skala' => $jawabanSkala,
                        'skor_diperoleh' => $skorPerItem,
                    ]
                );
            }

            // Handle Photo Upload
            if ($request->hasFile('foto_ortu')) {
                if ($sesi->foto_ortu && Storage::disk('public')->exists($sesi->foto_ortu)) {
                    Storage::disk('public')->delete($sesi->foto_ortu);
                }
                $sesi->foto_ortu = $request->file('foto_ortu')->store('spmb-wawancara/ortu', 'public');
            }

            // Calculate percentage score (0 - 100)
            $skorAkhir = $maxPossiblePoints > 0 ? round(($totalPointsEarned / $maxPossiblePoints) * 100, 2) : 80;

            // Automated Grade Recommendation
            if ($skorAkhir >= 85) {
                $hasilAutomated = 'Sangat Baik';
            } elseif ($skorAkhir >= 70) {
                $hasilAutomated = 'Baik';
            } elseif ($skorAkhir >= 55) {
                $hasilAutomated = 'Cukup';
            } else {
                $hasilAutomated = 'Kurang Baik';
            }

            $pewawancaraNama = $request->input('nama_pewawancara') ?: Auth::user()->name;

            $sesi->update([
                'status_wawancara_ortu' => 'Selesai',
                'pewawancara_ortu_user_id' => Auth::id(),
                'pewawancara_ortu_nama' => $pewawancaraNama,
                'waktu_wawancara_ortu' => now(),
                'skor_ortu' => $skorAkhir,
                'hasil_wawancara_ortu' => $request->input('hasil_wawancara_override') ?: $hasilAutomated,
                'catatan_pewawancara_ortu' => $request->input('catatan_pewawancara_ortu'),
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Wawancara orang tua/wali calon siswa {$pesertaSeleksi->nama_lengkap} berhasil disimpan dengan predikat: {$sesi->hasil_wawancara_ortu} (Skor: {$skorAkhir}).");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan hasil wawancara orang tua: ' . $e->getMessage());
        }
    }
}
