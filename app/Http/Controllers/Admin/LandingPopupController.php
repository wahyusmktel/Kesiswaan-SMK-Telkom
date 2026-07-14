<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingPopupController extends Controller
{
    public function edit(): View
    {
        $setting = AppSetting::first() ?? new AppSetting();

        return view('pages.super-admin.landing-popup', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'landing_popup_enabled' => ['nullable', 'boolean'],
            'landing_popup_type' => ['required', 'in:registration,mood'],
            'landing_popup_title' => ['required_if:landing_popup_type,registration', 'nullable', 'string', 'max:120'],
            'landing_popup_description' => ['required_if:landing_popup_type,registration', 'nullable', 'string', 'max:500'],
            'landing_popup_cta_text' => ['required_if:landing_popup_type,registration', 'nullable', 'string', 'max:50'],
            'landing_popup_cta_url' => [
                'required_if:landing_popup_type,registration',
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $scheme = $value ? parse_url($value, PHP_URL_SCHEME) : null;
                    $isWebUrl = filter_var($value, FILTER_VALIDATE_URL) && in_array($scheme, ['http', 'https'], true);

                    if ($value && ! str_starts_with($value, '/') && ! $isWebUrl) {
                        $fail('Tautan CTA harus berupa path internal yang diawali / atau URL lengkap.');
                    }
                },
            ],
            'landing_popup_frequency' => ['required', 'in:always,session,daily'],
        ]);

        $validated['landing_popup_enabled'] = $request->boolean('landing_popup_enabled');

        $setting = AppSetting::first() ?? new AppSetting();
        $setting->fill($validated)->save();

        toast('Pengaturan popup halaman utama berhasil diperbarui.', 'success');

        return redirect()->route('super-admin.landing-popup.edit');
    }
}
