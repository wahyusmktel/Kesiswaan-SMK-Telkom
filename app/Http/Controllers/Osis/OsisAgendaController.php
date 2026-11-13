<?php

namespace App\Http\Controllers\Osis;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\OsisAgenda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OsisAgendaController extends Controller
{
    public function index(): View
    {
        $appSetting = AppSetting::first();
        $agendas = OsisAgenda::latest('tanggal_mulai')->paginate(15);

        return view('pages.osis.agenda.index', compact('appSetting', 'agendas'));
    }

    public function create(): View
    {
        $appSetting = AppSetting::first();
        return view('pages.osis.agenda.create', compact('appSetting'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'nullable|string|max:255',
            'kategori' => 'required|string|max:100',
            'status' => 'required|in:akan_datang,berlangsung,selesai',
            'is_published' => 'nullable|boolean',
            'foto' => 'nullable|image|max:2048',
        ]);

        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('osis/agenda', 'public');
        }

        OsisAgenda::create($validated);

        return redirect()->route('osis.agenda.index')->with('success', 'Agenda OSIS berhasil ditambahkan!');
    }

    public function edit(OsisAgenda $agenda): View
    {
        $appSetting = AppSetting::first();
        return view('pages.osis.agenda.edit', compact('appSetting', 'agenda'));
    }

    public function update(Request $request, OsisAgenda $agenda): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'nullable|string|max:255',
            'kategori' => 'required|string|max:100',
            'status' => 'required|in:akan_datang,berlangsung,selesai',
            'is_published' => 'nullable|boolean',
            'foto' => 'nullable|image|max:2048',
        ]);

        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('foto')) {
            if ($agenda->foto) {
                Storage::delete($agenda->foto);
            }
            $validated['foto'] = $request->file('foto')->store('osis/agenda', 'public');
        }

        $agenda->update($validated);

        return redirect()->route('osis.agenda.index')->with('success', 'Agenda OSIS berhasil diperbarui!');
    }

    public function destroy(OsisAgenda $agenda): RedirectResponse
    {
        if ($agenda->foto) {
            Storage::delete($agenda->foto);
        }
        $agenda->delete();

        return redirect()->route('osis.agenda.index')->with('success', 'Agenda OSIS berhasil dihapus!');
    }
}
