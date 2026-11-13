<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\OsisAgenda;
use App\Models\OsisPengurus;
use App\Models\OsisSetting;
use App\Models\PilketosKandidat;
use App\Models\PilketosPeriode;
use App\Models\PilketosVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OsisPublicController extends Controller
{
    /**
     * Halaman Publik Utama OSIS SMK Telkom Lampung
     */
    public function index(): View
    {
        $appSetting = AppSetting::first();
        $setting = OsisSetting::current();

        // Pengurus aktif
        $penguruses = OsisPengurus::where('is_active', true)
            ->orderBy('urutan')
            ->get();

        // BPH & Sekbid groupings
        $bph = $penguruses->filter(fn ($p) => str_contains(strtolower($p->sekbid ?? ''), 'bph') || in_array(strtolower($p->jabatan), ['ketua umum', 'wakil ketua umum', 'sekretaris umum', 'bendahara umum', 'sekretaris', 'bendahara']));
        $sekbids = $penguruses->diff($bph)->groupBy('sekbid');

        // Agenda OSIS
        $agendas = OsisAgenda::where('is_published', true)
            ->orderBy('tanggal_mulai')
            ->get();

        // Periode Pilketos aktif
        $pilketos = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        $timelines = collect();
        $kandidats = collect();
        $totalDpt = 0;
        $totalVotes = 0;
        $turnout = 0.0;

        if ($pilketos) {
            $timelines = $pilketos->timelines;
            $kandidats = $pilketos->kandidats()->withCount('votes')->get();
            $totalDpt = $pilketos->total_dpt;
            $totalVotes = $pilketos->total_votes;
            $turnout = $pilketos->turnout_percentage;
        }

        return view('public.osis.index', compact(
            'appSetting',
            'setting',
            'penguruses',
            'bph',
            'sekbids',
            'agendas',
            'pilketos',
            'timelines',
            'kandidats',
            'totalDpt',
            'totalVotes',
            'turnout'
        ));
    }

    /**
     * Halaman Live Count Publik
     */
    public function liveCount(): View
    {
        $appSetting = AppSetting::first();
        $setting = OsisSetting::current();
        $pilketos = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        $kandidats = collect();
        $kandidatsJson = [];
        $totalDpt = 0;
        $totalVotes = 0;
        $turnout = 0.0;

        if ($pilketos) {
            $kandidats = $pilketos->kandidats()->withCount('votes')->get();
            $totalDpt = $pilketos->total_dpt;
            $totalVotes = $pilketos->total_votes;
            $turnout = $pilketos->turnout_percentage;

            $kandidatsJson = $kandidats->map(function ($k) use ($totalVotes) {
                return [
                    'id' => $k->id,
                    'nomor_urut' => $k->nomor_urut,
                    'nama_ketua' => $k->nama_ketua,
                    'nama_wakil' => $k->nama_wakil,
                    'kelas_ketua' => $k->kelas_ketua,
                    'kelas_wakil' => $k->kelas_wakil,
                    'tagline' => $k->tagline,
                    'foto_paslon' => $k->foto_paslon_url,
                    'votes_count' => $k->votes_count,
                    'percentage' => $totalVotes > 0 ? round(($k->votes_count / $totalVotes) * 100, 1) : 0,
                ];
            })->values()->toArray();
        }

        return view('public.osis.live-count', compact(
            'appSetting',
            'setting',
            'pilketos',
            'kandidats',
            'kandidatsJson',
            'totalDpt',
            'totalVotes',
            'turnout'
        ));
    }

    /**
     * Endpoint API JSON untuk Polling Realtime Live Count
     */
    public function liveCountData(): JsonResponse
    {
        $pilketos = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        if (!$pilketos) {
            return response()->json([
                'status' => 'inactive',
                'message' => 'Belum ada pemilihan yang aktif.',
                'total_dpt' => 0,
                'total_votes' => 0,
                'turnout_percentage' => 0,
                'kandidats' => [],
            ]);
        }

        $totalDpt = $pilketos->total_dpt;
        $totalVotes = $pilketos->total_votes;
        $turnout = $totalDpt > 0 ? round(($totalVotes / $totalDpt) * 100, 1) : 0;

        $kandidats = $pilketos->kandidats()->withCount('votes')->get()->map(function ($k) use ($totalVotes) {
            $pct = $totalVotes > 0 ? round(($k->votes_count / $totalVotes) * 100, 1) : 0;
            return [
                'id' => $k->id,
                'nomor_urut' => $k->nomor_urut,
                'nama_ketua' => $k->nama_ketua,
                'nama_wakil' => $k->nama_wakil,
                'kelas_ketua' => $k->kelas_ketua,
                'kelas_wakil' => $k->kelas_wakil,
                'tagline' => $k->tagline,
                'foto_paslon' => $k->foto_paslon_url,
                'votes_count' => $k->votes_count,
                'percentage' => $pct,
            ];
        });

        // Suara per bilik
        $bilikStats = PilketosVote::where('pilketos_periode_id', $pilketos->id)
            ->selectRaw('COALESCE(bilik_id, "Bilik Utama") as bilik, count(*) as count')
            ->groupBy('bilik')
            ->get();

        return response()->json([
            'status' => 'success',
            'periode_status' => $pilketos->status,
            'nama_periode' => $pilketos->nama_periode,
            'tahun_pelajaran' => $pilketos->tahun_pelajaran,
            'total_dpt' => $totalDpt,
            'total_votes' => $totalVotes,
            'turnout_percentage' => $turnout,
            'kandidats' => $kandidats,
            'bilik_stats' => $bilikStats,
            'last_updated' => now()->format('H:i:s'),
        ]);
    }
}
