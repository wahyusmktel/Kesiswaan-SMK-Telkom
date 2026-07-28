<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CctvCamera;
use App\Models\User;
use App\Services\CctvPlaybackToken;
use Illuminate\Http\Request;

class CctvGatewayAuthController extends Controller
{
    public function __invoke(Request $request, CctvPlaybackToken $tokens)
    {
        $configuredKey = (string) config('services.cctv.gateway_auth_key');
        $suppliedKey = (string) $request->query('key');

        abort_if($configuredKey === '' || ! hash_equals($configuredKey, $suppliedKey), 403);
        abort_unless($request->input('action') === 'read' && $request->input('protocol') === 'hls', 403);

        $payload = $tokens->verify($request->input('token'));
        abort_unless($payload, 401);
        abort_unless(hash_equals((string) ($payload['path'] ?? ''), (string) $request->input('path')), 403);

        $camera = CctvCamera::active()
            ->whereKey($payload['cid'] ?? null)
            ->where('stream_path', $payload['path'])
            ->first();
        $user = User::find($payload['uid'] ?? null);

        abort_unless($camera && $user && $user->canViewCctv($camera), 403);

        return response()->noContent();
    }
}
