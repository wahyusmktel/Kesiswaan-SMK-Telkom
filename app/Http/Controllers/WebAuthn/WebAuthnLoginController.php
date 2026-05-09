<?php

namespace App\Http\Controllers\WebAuthn;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;

use function response;

class WebAuthnLoginController
{
    /**
     * Returns the challenge to assertion.
     */
    public function options(AssertionRequest $request): Responsable
    {
        return $request->toVerify($request->validate(['email' => 'sometimes|email|string']));
    }

    /**
     * Log the user in and return a JSON response with role-based redirect URL.
     */
    public function login(AssertedRequest $request): JsonResponse|Response
    {
        if (!$request->login()) {
            return response()->noContent(422);
        }

        /** @var User $user */
        $user = Auth::user();

        return response()->json([
            'redirect' => $this->getDashboardRoute($user),
        ]);
    }

    private function getDashboardRoute(User $user): string
    {
        $roleRoutes = [
            'Siswa'          => 'siswa.dashboard.index',
            'Guru Kelas'     => 'guru-kelas.dashboard.index',
            'Wali Kelas'     => 'wali-kelas.dashboard.index',
            'Guru BK'        => 'bk.dashboard.index',
            'Guru Piket'     => 'piket.dashboard.index',
            'Waka Kesiswaan' => 'kesiswaan.dashboard.index',
            'Kurikulum'      => 'kurikulum.dashboard.index',
            'Operator'       => 'operator.dashboard.index',
            'Security'       => 'security.dashboard.index',
            'KAUR SDM'       => 'sdm.dashboard.index',
            'Super Admin'    => 'super-admin.dashboard.index',
            'Kantin'         => 'kantin.dashboard.index',
        ];

        foreach ($roleRoutes as $role => $routeName) {
            if ($user->hasRole($role)) {
                return route($routeName, absolute: false);
            }
        }

        return route('dashboard', absolute: false);
    }
}
