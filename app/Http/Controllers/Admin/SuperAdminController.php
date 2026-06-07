<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;

class SuperAdminController extends Controller
{
    public function settings()
    {
        $setting = AppSetting::first() ?? new AppSetting();
        return view('pages.admin.settings', compact('setting'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'school_name'    => 'required|string|max:255',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'kop_surat_ukk'  => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'favicon'        => 'nullable|image|mimes:png,ico|max:1024',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'allow_registration' => 'nullable|boolean',
            'theme'          => 'nullable|string|in:default,light-red,tech-red,campus-flow,telkom-corporate',
        ]);

        $setting = AppSetting::first() ?? new AppSetting();
        $data = $request->except(['logo', 'kop_surat_ukk', 'favicon']);
        $data['allow_registration'] = $request->has('allow_registration');

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                Storage::disk('public')->delete($setting->logo);
            }
            $data['logo'] = $request->file('logo')->store('settings', 'public');
        }

        if ($request->hasFile('kop_surat_ukk')) {
            if ($setting->kop_surat_ukk) {
                Storage::disk('public')->delete($setting->kop_surat_ukk);
            }
            $data['kop_surat_ukk'] = $request->file('kop_surat_ukk')->store('settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                Storage::disk('public')->delete($setting->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        $setting->fill($data);
        $setting->save();

        toast('Konfigurasi aplikasi berhasil diperbarui.', 'success');
        return redirect()->back();
    }
}
