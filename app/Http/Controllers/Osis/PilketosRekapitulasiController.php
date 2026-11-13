<?php

namespace App\Http\Controllers\Osis;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\PilketosDpt;
use App\Models\PilketosKandidat;
use App\Models\PilketosPeriode;
use App\Models\PilketosVote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PilketosRekapitulasiController extends Controller
{
    public function index(): View
    {
        $appSetting = AppSetting::first();
        $periode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        $totalDpt = 0;
        $totalVotes = 0;
        $turnout = 0.0;
        $kandidats = collect();
        $classBreakdown = collect();
        $bilikBreakdown = collect();
        $recentVotes = collect();

        if ($periode) {
            $totalDpt = $periode->total_dpt;
            $totalVotes = $periode->total_votes;
            $turnout = $periode->turnout_percentage;

            $kandidats = $periode->kandidats()->withCount('votes')->get()->map(function ($k) use ($totalVotes) {
                $k->percentage = $totalVotes > 0 ? round(($k->votes_count / $totalVotes) * 100, 1) : 0;
                return $k;
            });

            // Breakdown partisipasi per kelas
            $classBreakdown = PilketosDpt::where('pilketos_periode_id', $periode->id)
                ->select('kelas',
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when status_memilih = "sudah_memilih" then 1 else 0 end) as voted')
                )
                ->groupBy('kelas')
                ->orderBy('kelas')
                ->get()
                ->map(function ($c) {
                    $c->percentage = $c->total > 0 ? round(($c->voted / $c->total) * 100, 1) : 0;
                    return $c;
                });

            // Breakdown per bilik suara
            $bilikBreakdown = PilketosVote::where('pilketos_periode_id', $periode->id)
                ->select('bilik_id', DB::raw('count(*) as count'))
                ->groupBy('bilik_id')
                ->get();

            $recentVotes = PilketosVote::with('kandidat')
                ->where('pilketos_periode_id', $periode->id)
                ->latest('voted_at')
                ->take(10)
                ->get();
        }

        return view('pages.osis.pilketos.rekapitulasi', compact(
            'appSetting',
            'periode',
            'totalDpt',
            'totalVotes',
            'turnout',
            'kandidats',
            'classBreakdown',
            'bilikBreakdown',
            'recentVotes'
        ));
    }

    /**
     * Cetak Berita Acara Rekapitulasi Pemilihan
     */
    public function printBeritaAcara(): View
    {
        $appSetting = AppSetting::first();
        $periode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        $totalDpt = $periode ? $periode->total_dpt : 0;
        $totalVotes = $periode ? $periode->total_votes : 0;
        $turnout = $periode ? $periode->turnout_percentage : 0;

        $kandidats = $periode ? $periode->kandidats()->withCount('votes')->orderBy('nomor_urut')->get() : collect();

        // Pemenang suara terbanyak
        $winner = $kandidats->sortByDesc('votes_count')->first();

        return view('pages.osis.pilketos.berita-acara', compact(
            'appSetting',
            'periode',
            'totalDpt',
            'totalVotes',
            'turnout',
            'kandidats',
            'winner'
        ));
    }

    /**
     * Reset Semua Suara Masuk (Fitur Reset Pemilihan)
     */
    public function resetVotes(Request $request): RedirectResponse
    {
        $periode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();
        if (!$periode) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }

        DB::transaction(function () use ($periode) {
            PilketosVote::where('pilketos_periode_id', $periode->id)->delete();
            PilketosDpt::where('pilketos_periode_id', $periode->id)->update([
                'status_memilih' => 'belum_memilih',
                'waktu_memilih' => null,
                'bilik_id' => null,
            ]);
        });

        return back()->with('success', 'Seluruh suara masuk dan status DPT telah berhasil di-reset untuk pemilihan ulang!');
    }
}
