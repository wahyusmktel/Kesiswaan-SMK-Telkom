<?php

namespace App\Http\Controllers;

use App\Models\CctvAccessLog;
use App\Models\CctvCamera;
use App\Services\CctvPlaybackToken;
use App\Services\MediaMtxService;
use Illuminate\Http\Request;

class CctvLiveController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = CctvCamera::active()->orderBy('sort_order')->orderBy('name');

        if (! $user->hasRole('Super Admin')) {
            $query->whereHas('users', fn ($relation) => $relation->whereKey($user->id));
        }

        $cameras = $query->get(['id', 'name', 'slug', 'location', 'description', 'stream_path']);

        abort_if(
            $cameras->isEmpty() && ! $user->hasRole('Super Admin'),
            403,
            'Anda belum memiliki akses ke kamera CCTV.'
        );

        $liveData = [
            'csrfToken' => csrf_token(),
            'cameras' => $cameras->map(fn (CctvCamera $camera) => [
                'id' => $camera->id,
                'name' => $camera->name,
                'location' => $camera->location,
                'description' => $camera->description,
                'tokenUrl' => route('cctv-live.token', $camera),
            ])->values(),
        ];

        return view('pages.cctv-live.index', compact('cameras', 'liveData'));
    }

    public function token(
        Request $request,
        CctvCamera $camera,
        CctvPlaybackToken $tokens,
        MediaMtxService $mediaMtx
    ) {
        abort_unless($camera->is_active && $request->user()->canViewCctv($camera), 403);

        $issued = $tokens->issue($request->user(), $camera);
        CctvAccessLog::create([
            'cctv_camera_id' => $camera->id,
            'user_id' => $request->user()->id,
            'action' => 'token_issued',
            'ip_hash' => hash('sha256', (string) $request->ip().'|'.config('app.key')),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
        ]);

        return response()->json([
            'token' => $issued['token'],
            'expires_at' => $issued['expires_at'],
            'manifest_url' => $mediaMtx->manifestUrl($camera),
        ]);
    }
}
