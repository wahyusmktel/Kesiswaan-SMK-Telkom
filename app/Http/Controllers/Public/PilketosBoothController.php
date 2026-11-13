<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\PilketosDpt;
use App\Models\PilketosKandidat;
use App\Models\PilketosPeriode;
use App\Models\PilketosVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PilketosBoothController extends Controller
{
    /**
     * Tampilan Layar Bilik Suara Elektronik (E-Voting Booth)
     */
    public function index(Request $request): View
    {
        $appSetting = AppSetting::first();
        $pilketos = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        $bilikId = $request->query('bilik', 'Bilik 1');

        $kandidats = collect();
        $kandidatsJson = [];
        if ($pilketos) {
            $kandidats = $pilketos->kandidats;
            $kandidatsJson = $kandidats->map(function ($k) {
                return [
                    'id' => $k->id,
                    'nomor_urut' => $k->nomor_urut,
                    'nama_ketua' => $k->nama_ketua,
                    'nama_wakil' => $k->nama_wakil,
                    'kelas_ketua' => $k->kelas_ketua,
                    'kelas_wakil' => $k->kelas_wakil,
                    'tagline' => $k->tagline,
                    'visi' => $k->visi,
                    'misi' => $k->misi,
                    'program_unggulan' => $k->program_unggulan,
                    'foto_paslon' => $k->foto_paslon_url,
                ];
            })->values()->toArray();
        }

        return view('public.osis.booth', compact(
            'appSetting',
            'pilketos',
            'bilikId',
            'kandidats',
            'kandidatsJson'
        ));
    }

    /**
     * Verifikasi Scan Barcode Fisik Pemilih di Bilik Suara
     */
    public function scanBarcode(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string|max:100',
            'bilik_id' => 'nullable|string|max:50',
        ]);

        $barcodeInput = trim($request->barcode);
        $bilikId = $request->bilik_id ?: 'Bilik 1';

        $pilketos = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();
        if (!$pilketos) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada periode pemilihan yang sedang aktif.',
            ], 422);
        }

        if ($pilketos->status !== 'voting_berlangsung') {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi pemungutan suara saat ini berstatus: "' . strtoupper(str_replace('_', ' ', $pilketos->status)) . '". Bilik belum dibuka.',
            ], 422);
        }

        // Cari berdasarkan barcode_token atau NIS (fleksibel jika panitia darurat input NIS)
        $dpt = PilketosDpt::where('pilketos_periode_id', $pilketos->id)
            ->where(function ($q) use ($barcodeInput) {
                $q->where('barcode_token', $barcodeInput)
                  ->orWhere('nis', $barcodeInput);
            })
            ->first();

        if (!$dpt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Barcode kartu pemilih tidak terdaftar dalam Daftar Pemilih Tetap (DPT)!',
            ], 404);
        }

        // Cek apakah sudah pernah memilih (Anti-Kecurangan / Single Use Token)
        if ($dpt->status_memilih === 'sudah_memilih') {
            $waktu = $dpt->waktu_memilih ? $dpt->waktu_memilih->format('H:i:s, d M Y') : 'sebelumnya';
            $bilik = $dpt->bilik_id ?: 'Bilik Suara';
            return response()->json([
                'status' => 'already_voted',
                'voter_name' => $dpt->nama_pemilih,
                'voter_nis' => $dpt->nis,
                'voter_kelas' => $dpt->kelas,
                'message' => "PERINGATAN: Pemilih {$dpt->nama_pemilih} ({$dpt->nis} - {$dpt->kelas}) SUDAH MENGGUNAKAN HAK SUARANYA pada {$waktu} di {$bilik}! Akses ditolak.",
            ], 403);
        }

        // Buat Single-use Token untuk sesi pemilihan ini
        $sessionToken = Str::random(48);
        session([
            'pilketos_voter_id' => $dpt->id,
            'pilketos_session_token' => $sessionToken,
            'pilketos_bilik_id' => $bilikId,
        ]);

        // Siapkan data paslon
        $kandidats = $pilketos->kandidats->map(function ($k) {
            return [
                'id' => $k->id,
                'nomor_urut' => $k->nomor_urut,
                'nama_ketua' => $k->nama_ketua,
                'nama_wakil' => $k->nama_wakil,
                'kelas_ketua' => $k->kelas_ketua,
                'kelas_wakil' => $k->kelas_wakil,
                'tagline' => $k->tagline,
                'visi' => $k->visi,
                'misi' => $k->misi,
                'program_unggulan' => $k->program_unggulan,
                'foto_paslon' => $k->foto_paslon_url,
                'foto_ketua' => $k->foto_ketua_url,
                'foto_wakil' => $k->foto_wakil_url,
            ];
        });

        // Tandai status sementara
        $dpt->update([
            'status_memilih' => 'sedang_memilih',
            'bilik_id' => $bilikId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Barcode valid. Identitas pemilih berhasil diverifikasi.',
            'session_token' => $sessionToken,
            'voter' => [
                'id' => $dpt->id,
                'nama' => $dpt->nama_pemilih,
                'nis' => $dpt->nis,
                'nisn' => $dpt->nisn,
                'kelas' => $dpt->kelas,
                'foto' => $dpt->masterSiswa?->foto_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($dpt->nama_pemilih) . '&background=0284c7&color=ffffff',
            ],
            'kandidats' => $kandidats,
        ]);
    }

    /**
     * Submit Surat Suara Digital dari Bilik
     */
    public function submitBallot(Request $request): JsonResponse
    {
        $request->validate([
            'session_token' => 'required|string',
            'voter_id' => 'required|integer',
            'kandidat_id' => 'required|integer',
            'bilik_id' => 'nullable|string|max:50',
        ]);

        $sessionToken = session('pilketos_session_token');
        $sessionVoterId = session('pilketos_voter_id');

        if (!$sessionToken || $sessionToken !== $request->session_token || $sessionVoterId != $request->voter_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi pemilihan telah kedaluwarsa atau tidak valid. Silakan scan ulang barcode Anda.',
            ], 403);
        }

        $pilketos = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();
        if (!$pilketos || $pilketos->status !== 'voting_berlangsung') {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi pemilihan sedang tidak aktif.',
            ], 422);
        }

        $kandidat = PilketosKandidat::where('pilketos_periode_id', $pilketos->id)
            ->where('id', $request->kandidat_id)
            ->first();

        if (!$kandidat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kandidat yang dipilih tidak valid!',
            ], 404);
        }

        $bilikId = $request->bilik_id ?: (session('pilketos_bilik_id') ?: 'Bilik 1');

        return DB::transaction(function () use ($request, $pilketos, $kandidat, $bilikId) {
            $dpt = PilketosDpt::where('id', $request->voter_id)
                ->lockForUpdate()
                ->first();

            if (!$dpt || $dpt->status_memilih === 'sudah_memilih') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Suara untuk pemilih ini sudah tercatat sebelumnya. Upaya kecurangan dicegah.',
                ], 403);
            }

            // Catat suara secara anonim (LUBER JURDIL)
            PilketosVote::create([
                'pilketos_periode_id' => $pilketos->id,
                'pilketos_kandidat_id' => $kandidat->id,
                'bilik_id' => $bilikId,
                'voted_at' => now(),
            ]);

            // Hanguskan token barcode pemilih (Single Use)
            $dpt->update([
                'status_memilih' => 'sudah_memilih',
                'waktu_memilih' => now(),
                'bilik_id' => $bilikId,
            ]);

            // Bersihkan sesi bilik
            session()->forget(['pilketos_voter_id', 'pilketos_session_token', 'pilketos_bilik_id']);

            return response()->json([
                'status' => 'success',
                'message' => 'Hak suara Anda telah berhasil digunakan secara sah dan rahasia! Terima kasih telah berpartisipasi dalam Pilketos SMK Telkom Lampung.',
                'chosen_candidate_number' => $kandidat->nomor_urut,
            ]);
        });
    }

    /**
     * Batalkan Sesi Bilik (misal jika pemilih batal di bilik sebelum klik pilih)
     */
    public function cancelSession(Request $request): JsonResponse
    {
        $voterId = session('pilketos_voter_id');
        if ($voterId) {
            $dpt = PilketosDpt::find($voterId);
            if ($dpt && $dpt->status_memilih === 'sedang_memilih') {
                $dpt->update(['status_memilih' => 'belum_memilih']);
            }
        }

        session()->forget(['pilketos_voter_id', 'pilketos_session_token', 'pilketos_bilik_id']);

        return response()->json([
            'status' => 'success',
            'message' => 'Sesi bilik telah di-reset.',
        ]);
    }
}
