<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SsoUserInfoController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user('api');

        abort_unless($user?->tokenCan('profile:read'), 403, 'Token tidak memiliki scope profile:read.');
        $sisfoUser = User::findOrFail($user->getAuthIdentifier());

        return response()->json([
            'sub' => (string) $sisfoUser->getAuthIdentifier(),
            'name' => $sisfoUser->name,
            'email' => $sisfoUser->email,
            'email_verified' => ! is_null($sisfoUser->email_verified_at),
            'picture' => $sisfoUser->avatar,
            'roles' => $sisfoUser->getRoleNames()->values(),
        ]);
    }
}
