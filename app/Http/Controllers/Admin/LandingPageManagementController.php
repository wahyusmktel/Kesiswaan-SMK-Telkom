<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingHeroSlide;
use App\Models\LandingTicker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingPageManagementController extends Controller
{
    public function index()
    {
        $slides = LandingHeroSlide::orderBy('sort_order')->orderBy('id')->get();
        $tickers = LandingTicker::orderBy('sort_order')->orderBy('id')->get();

        return view('pages.super-admin.landing-page.index', compact('slides', 'tickers'));
    }

    public function storeSlide(Request $request)
    {
        $validated = $request->validate($this->slideRules(true));
        $validated['image_path'] = $request->file('image')->store('landing/hero', 'public');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = $request->user()->id;

        LandingHeroSlide::create($validated);

        return back()->with('success', 'Hero slide berhasil ditambahkan.');
    }

    public function updateSlide(Request $request, LandingHeroSlide $slide)
    {
        $validated = $request->validate($this->slideRules(false));
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('landing/hero', 'public');
            Storage::disk('public')->delete($slide->image_path);
            $validated['image_path'] = $newImagePath;
        }

        $slide->update($validated);

        return back()->with('success', 'Hero slide berhasil diperbarui.');
    }

    public function destroySlide(LandingHeroSlide $slide)
    {
        Storage::disk('public')->delete($slide->image_path);
        $slide->delete();

        return back()->with('success', 'Hero slide berhasil dihapus.');
    }

    public function storeTicker(Request $request)
    {
        $validated = $request->validate($this->tickerRules());
        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = $request->user()->id;

        LandingTicker::create($validated);

        return back()->with('success', 'Info terkini berhasil ditambahkan.');
    }

    public function updateTicker(Request $request, LandingTicker $ticker)
    {
        $validated = $request->validate($this->tickerRules());
        $validated['is_active'] = $request->boolean('is_active');
        $ticker->update($validated);

        return back()->with('success', 'Info terkini berhasil diperbarui.');
    }

    public function destroyTicker(LandingTicker $ticker)
    {
        $ticker->delete();

        return back()->with('success', 'Info terkini berhasil dihapus.');
    }

    private function slideRules(bool $imageRequired): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
            'cta_label' => ['nullable', 'string', 'max:60', 'required_with:cta_url'],
            'cta_url' => ['nullable', 'string', 'max:500', 'required_with:cta_label'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }

    private function tickerRules(): array
    {
        return [
            'text' => ['required', 'string', 'max:500'],
            'url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }
}
