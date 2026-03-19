<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShortUrlController extends Controller
{
    public function index()
    {
        $shortUrls = \App\Models\ShortUrl::where('user_id', \Illuminate\Support\Facades\Auth::id())->latest()->paginate(10);
        return view('pages.shared.shortener.index', compact('shortUrls'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
            'custom_code' => 'nullable|string|max:100|alpha_dash|unique:short_urls,short_code',
        ], [
            'custom_code.unique' => 'Alias / URL Pendek ini sudah digunakan orang lain, silakan pilih yang lain.',
            'custom_code.alpha_dash' => 'Alias hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).'
        ]);

        $shortCode = $request->custom_code;

        if (!$shortCode) {
            do {
                $shortCode = \Illuminate\Support\Str::random(5);
            } while (\App\Models\ShortUrl::where('short_code', $shortCode)->exists());
        }

        \App\Models\ShortUrl::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'original_url' => $request->original_url,
            'short_code' => $shortCode,
            'clicks' => 0,
        ]);

        toast('URL berhasil diperpendek!', 'success');
        return redirect()->back();
    }

    public function destroy(\App\Models\ShortUrl $shortUrl)
    {
        if ($shortUrl->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $shortUrl->delete();

        toast('URL pendek berhasil dihapus.', 'success');
        return redirect()->back();
    }

    public function redirect($code)
    {
        $shortUrl = \App\Models\ShortUrl::where('short_code', $code)->firstOrFail();
        
        $shortUrl->increment('clicks');

        return redirect()->away($shortUrl->original_url);
    }
}
