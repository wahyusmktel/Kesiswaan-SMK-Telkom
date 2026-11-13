<?php

namespace App\Http\Controllers\Osis;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\OsisAgenda;
use App\Models\OsisPengurus;
use App\Models\OsisSetting;
use App\Models\PilketosDpt;
use App\Models\PilketosKandidat;
use App\Models\PilketosPeriode;
use App\Models\PilketosVote;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OsisDashboardController extends Controller
{
    public function index(): View
    {
        $appSetting = AppSetting::first();
        $setting = OsisSetting::current();

        $totalPengurus = OsisPengurus::where('is_active', true)->count();
        $totalAgenda = OsisAgenda::where('is_published', true)->count();

        $activePeriode = PilketosPeriode::active() ?? PilketosPeriode::latest()->first();

        $totalDpt = 0;
        $totalVotes = 0;
        $turnout = 0.0;
        $kandidats = collect();

        if ($activePeriode) {
            $totalDpt = $activePeriode->total_dpt;
            $totalVotes = $activePeriode->total_votes;
            $turnout = $activePeriode->turnout_percentage;
            $kandidats = $activePeriode->kandidats()->withCount('votes')->get();
        }

        $recentAgendas = OsisAgenda::latest('tanggal_mulai')->take(5)->get();
        $recentVotes = $activePeriode ? PilketosVote::with('kandidat')->where('pilketos_periode_id', $activePeriode->id)->latest()->take(6)->get() : collect();

        return view('pages.osis.dashboard', compact(
            'appSetting',
            'setting',
            'totalPengurus',
            'totalAgenda',
            'activePeriode',
            'totalDpt',
            'totalVotes',
            'turnout',
            'kandidats',
            'recentAgendas',
            'recentVotes'
        ));
    }
}
