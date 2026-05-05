<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:1024'],
        ]);

        if ($request->user()->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($request->user()->avatar);
        }
        
        $path = $request->file('avatar')->store('avatars', 'public');
        $request->user()->update(['avatar' => $path]);

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Update the user's face ID photo.
     */
    public function updateFaceId(Request $request): RedirectResponse
    {
        $request->validate([
            'face_image' => ['required', 'string'],
        ]);

        try {
            $imageData = $request->input('face_image');
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $imageData = base64_decode($imageData);

            if (!$imageData) {
                return Redirect::back()->withErrors(['face_image' => 'Format gambar tidak valid.']);
            }

            if ($request->user()->face_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($request->user()->face_photo);
            }
            
            $path = 'faces/' . uniqid('face_') . '.jpg';
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $imageData);
            
            $request->user()->update(['face_photo' => $path]);

            return Redirect::route('profile.edit')->with('status', 'face-id-updated');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Face ID save error: ' . $e->getMessage());
            return Redirect::back()->withErrors(['face_image' => 'Gagal menyimpan Face ID.']);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
