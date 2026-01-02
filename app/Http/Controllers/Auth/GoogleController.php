<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Create a redirect method to google api.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a callback method to handle login.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists by email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update google_id if not set
                if (empty($user->google_id)) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                }

                Auth::login($user);

                return redirect()->intended('dashboard');
            } else {
                return redirect()->route('login')->with('error', 'Email anda tidak terdaftar di sistem. Silakan hubungi admin.');
            }

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat login via Google: ' . $e->getMessage());
        }
    }
}
