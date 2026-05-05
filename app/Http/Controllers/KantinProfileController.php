<?php

namespace App\Http\Controllers;

use App\Models\KantinProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class KantinProfileController extends Controller
{
    public function edit()
    {
        // Get or create a blank profile for the currently logged-in Kantin user
        $profile = KantinProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name . ' Kantin', 'is_open' => true]
        );

        return view('pages.kantin.settings.index', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = KantinProfile::firstOrCreate(['user_id' => Auth::id()]);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'phone_number' => $request->phone_number,
            'is_open' => $request->has('is_open'),
        ];

        if ($request->hasFile('banner_image')) {
            // Delete old banner if exists
            if ($profile->banner_image) {
                Storage::disk('public')->delete($profile->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('kantin_banners', 'public');
        }

        $profile->update($data);

        return redirect()->route('kantin.settings.index')->with('success', 'Pengaturan kantin berhasil disimpan.');
    }
}
