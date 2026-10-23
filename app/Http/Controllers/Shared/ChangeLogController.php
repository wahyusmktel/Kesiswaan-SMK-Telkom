<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ChangeLogController extends Controller
{
    public function index(Request $request)
    {
        $changelogPath = base_path('CHANGELOG.md');
        $rawContent = File::exists($changelogPath) 
            ? File::get($changelogPath) 
            : "# Changelog Belum Ditemukan\nSilakan buat file CHANGELOG.md pada root aplikasi.";

        if ($request->has('download') && File::exists($changelogPath)) {
            return response()->download($changelogPath, 'CHANGELOG-SISFO-STELLA.md', [
                'Content-Type' => 'text/markdown',
            ]);
        }

        $htmlContent = Str::markdown($rawContent);

        // Extract version tags & dates
        preg_match_all('/## \[([0-9\.]+)\] - ([0-9\-]+)/', $rawContent, $matches, PREG_SET_ORDER);
        $releases = [];
        foreach ($matches as $match) {
            $releases[] = [
                'version' => $match[1],
                'date' => $match[2],
            ];
        }

        $latestVersion = $releases[0]['version'] ?? config('app.version', '2.4.0');

        return view('pages.shared.changelog.index', compact(
            'rawContent',
            'htmlContent',
            'releases',
            'latestVersion'
        ));
    }
}
