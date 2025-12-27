<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Get detailed profile data for the authenticated user.
     */
    public function getProfile(Request $request)
    {
        $user = $request->user()->load(['masterSiswa', 'masterGuru']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first(),
                ],
                'siswa_data' => $user->masterSiswa,
                'guru_data' => $user->masterGuru,
            ],
        ]);
    }

    /**
     * Update user's basic profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $user,
        ]);
    }

    /**
     * Update user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui',
        ]);
    }

    /**
     * Update profile avatar (placeholder logic).
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Logic to store avatar can be added here
        // $path = $request->file('avatar')->store('avatars', 'public');
        // $user->update(['avatar_path' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui (Simulasi)',
        ]);
    }
}
