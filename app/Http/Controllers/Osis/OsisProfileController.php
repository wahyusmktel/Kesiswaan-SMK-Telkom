<?php

namespace App\Http\Controllers\Osis;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\OsisSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OsisProfileController extends Controller
{
    public function edit(): View
    {
        $appSetting = AppSetting::first();
        $setting = OsisSetting::current();

        return view('pages.osis.profile', compact('appSetting', 'setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = OsisSetting::current();

        $validated = $request->validate([
            'nama_organisasi' => 'required|string|max:255',
            'semboyan' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'periode_aktif' => 'required|string|max:50',
            'nama_ketua_existing' => 'nullable|string|max:255',
            'nama_wakil_existing' => 'nullable|string|max:255',
            'visi_misi_ketua_existing' => 'nullable|string',
            'pesan_ketua_existing' => 'nullable|string',
            'instagram' => 'nullable|string|max:100',
            'tiktok' => 'nullable|string|max:100',
            'youtube' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
            'logo' => 'nullable|image|max:2048',
            'foto_struktur' => 'nullable|image|max:3072',
            'foto_ketua_existing' => 'nullable|image|max:2048',
            'foto_wakil_existing' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                Storage::delete($setting->logo);
            }
            $validated['logo'] = $request->file('logo')->store('osis/branding', 'public');
        }

        if ($request->hasFile('foto_struktur')) {
            if ($setting->foto_struktur) {
                Storage::delete($setting->foto_struktur);
            }
            $validated['foto_struktur'] = $request->file('foto_struktur')->store('osis/branding', 'public');
        }

        if ($request->hasFile('foto_ketua_existing')) {
            if ($setting->foto_ketua_existing) {
                Storage::delete($setting->foto_ketua_existing);
            }
            $validated['foto_ketua_existing'] = $request->file('foto_ketua_existing')->store('osis/pengurus', 'public');
        }

        if ($request->hasFile('foto_wakil_existing')) {
            if ($setting->foto_wakil_existing) {
                Storage::delete($setting->foto_wakil_existing);
            }
            $validated['foto_wakil_existing'] = $request->file('foto_wakil_existing')->store('osis/pengurus', 'public');
        }

        $setting->update($validated);

        return redirect()->route('osis.profile.edit')->with('success', 'Profil dan informasi OSIS berhasil diperbarui!');
    }
}
