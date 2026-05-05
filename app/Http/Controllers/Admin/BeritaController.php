<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BeritaController extends Controller
{
    /**
     * Tampilkan daftar semua berita (admin).
     */
    public function index(Request $request)
    {
        $query = Berita::with('author')->latest();

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $beritas = $query->paginate(10);

        return view('pages.admin.berita.index', compact('beritas'));
    }

    /**
     * Form tambah berita baru.
     */
    public function create()
    {
        return view('pages.admin.berita.create');
    }

    /**
     * Simpan berita baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'kategori' => 'required|in:Akademik,Kesiswaan,Kegiatan,Prestasi,Pengumuman,Lainnya',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->except('gambar');
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->judul) . '-' . Str::random(5);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        if ($request->status === 'published') {
            $data['published_at'] = now();
        }

        Berita::create($data);

        toast('Berita berhasil ditambahkan!', 'success');
        return redirect()->route('super-admin.berita.index');
    }

    /**
     * Form edit berita.
     */
    public function edit(Berita $berita)
    {
        return view('pages.admin.berita.edit', compact('berita'));
    }

    /**
     * Perbarui berita.
     */
    public function update(Request $request, Berita $berita)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'kategori' => 'required|in:Akademik,Kesiswaan,Kegiatan,Prestasi,Pengumuman,Lainnya',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        // Set published_at if status changed to published
        if ($request->status === 'published' && !$berita->published_at) {
            $data['published_at'] = now();
        } elseif ($request->status === 'draft') {
            $data['published_at'] = null;
        }

        $berita->update($data);

        toast('Berita berhasil diperbarui!', 'success');
        return redirect()->route('super-admin.berita.index');
    }

    /**
     * Hapus berita.
     */
    public function destroy(Berita $berita)
    {
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->delete();

        toast('Berita berhasil dihapus!', 'success');
        return redirect()->route('super-admin.berita.index');
    }

    /**
     * Halaman detail berita (public).
     */
    public function show($slug)
    {
        $berita = Berita::with('author')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->firstOrFail();

        $relatedNews = Berita::published()
            ->where('id', '!=', $berita->id)
            ->where('kategori', $berita->kategori)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('pages.berita.show', compact('berita', 'relatedNews'));
    }

    /**
     * API endpoint to get latest published news for welcome page.
     */
    public function latestApi()
    {
        $beritas = Berita::published()
            ->latest('published_at')
            ->take(6)
            ->get(['id', 'judul', 'slug', 'ringkasan', 'gambar', 'kategori', 'published_at']);

        return response()->json($beritas->map(function ($b) {
            return [
                'id' => $b->id,
                'judul' => $b->judul,
                'slug' => $b->slug,
                'ringkasan' => $b->ringkasan,
                'gambar_url' => $b->gambar_url,
                'kategori' => $b->kategori,
                'published_at' => $b->published_at->diffForHumans(),
                'url' => route('berita.show', $b->slug),
            ];
        }));
    }
}
