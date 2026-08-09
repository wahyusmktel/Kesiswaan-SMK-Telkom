<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\SpmbFee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SpmbFeeController extends Controller
{
    public function index(): View
    {
        $fees = SpmbFee::query()->orderBy('sort_order')->orderBy('id')->get();
        $setting = AppSetting::first() ?? new AppSetting(['spmb_academic_year' => '2027/2028']);

        return view('pages.super-admin.spmb-fees.index', compact('fees', 'setting'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'spmb_academic_year' => ['required', 'regex:/^\\d{4}\/\\d{4}$/'],
        ], [
            'spmb_academic_year.regex' => 'Tahun pelajaran harus menggunakan format 2027/2028.',
        ]);

        $setting = AppSetting::first() ?? new AppSetting;
        $setting->fill($validated)->save();
        $this->forgetPublicCache();

        return back()->with('success', 'Tahun pelajaran SPMB berhasil diperbarui.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = $request->user()->id;

        SpmbFee::create($validated);
        $this->forgetPublicCache();

        return back()->with('success', 'Rincian biaya berhasil ditambahkan.');
    }

    public function update(Request $request, SpmbFee $spmbFee): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active');

        $spmbFee->update($validated);
        $this->forgetPublicCache();

        return back()->with('success', 'Rincian biaya berhasil diperbarui.');
    }

    public function destroy(SpmbFee $spmbFee): RedirectResponse
    {
        $spmbFee->delete();
        $this->forgetPublicCache();

        return back()->with('success', 'Rincian biaya berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }

    private function forgetPublicCache(): void
    {
        Cache::forget('api.spmb.fees');
    }
}
