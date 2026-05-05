<?php

namespace App\Http\Controllers;

use App\Models\KantinMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class KantinMenuController extends Controller
{
    public function index()
    {
        $menus = KantinMenu::where('user_id', Auth::id())->latest()->paginate(10);
        return view('pages.kantin.menu.index', compact('menus'));
    }

    public function create()
    {
        return view('pages.kantin.menu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:makanan,minuman,cemilan',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'max:5', // Max 5 images
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('kantin_menus', 'public');
                $imagePaths[] = $path;
            }
        }

        KantinMenu::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'is_available' => $request->has('is_available'),
            'images' => empty($imagePaths) ? null : $imagePaths,
        ]);

        return redirect()->route('kantin.menu.index')->with('success', 'Menu makanan berhasil ditambahkan.');
    }

    public function edit(KantinMenu $menu)
    {
        if ($menu->user_id !== Auth::id()) abort(403);
        return view('pages.kantin.menu.edit', compact('menu'));
    }

    public function update(Request $request, KantinMenu $menu)
    {
        if ($menu->user_id !== Auth::id()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:makanan,minuman,cemilan',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'max:5',
        ]);

        $imagePaths = $menu->images ?? [];

        // Hapus gambar lama jika ada yang dicentang untuk dihapus
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $key => $value) {
                if (isset($imagePaths[$key])) {
                    Storage::disk('public')->delete($imagePaths[$key]);
                    unset($imagePaths[$key]);
                }
            }
            $imagePaths = array_values($imagePaths); // Re-index array
        }

        // Tambahkan gambar baru (gabungan lama & baru maksimal 5)
        if ($request->hasFile('images')) {
            $newImages = $request->file('images');
            $allowedNewImages = 5 - count($imagePaths);
            $newImages = array_slice($newImages, 0, $allowedNewImages);

            foreach ($newImages as $image) {
                $path = $image->store('kantin_menus', 'public');
                $imagePaths[] = $path;
            }
        }

        $menu->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'is_available' => $request->has('is_available'),
            'images' => empty($imagePaths) ? null : $imagePaths,
        ]);

        return redirect()->route('kantin.menu.index')->with('success', 'Menu makanan berhasil diperbarui.');
    }

    public function destroy(KantinMenu $menu)
    {
        if ($menu->user_id !== Auth::id()) abort(403);

        if ($menu->images) {
            foreach ($menu->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $menu->delete();

        return redirect()->route('kantin.menu.index')->with('success', 'Menu makanan berhasil dihapus.');
    }
}
