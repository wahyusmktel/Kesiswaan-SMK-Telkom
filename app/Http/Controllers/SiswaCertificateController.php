<?php

namespace App\Http\Controllers;

use App\Models\SiswaCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiswaCertificateController extends Controller
{
    public function index()
    {
        $certificates = SiswaCertificate::where('user_id', Auth::id())->latest()->paginate(9);
        return view('pages.siswa.certificates.index', compact('certificates'));
    }

    public function create()
    {
        return view('pages.siswa.certificates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiration_date' => 'nullable|date|after_or_equal:issue_date',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:255',
            'file_path' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'description' => 'nullable|string',
        ]);

        $data = $request->except('file_path');
        $data['user_id'] = Auth::id();

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('siswa/certificates', 'public');
        }

        SiswaCertificate::create($data);

        return redirect()->route('siswa.certificates.index')->with('success', 'Sertifikat berhasil ditambahkan ke portofolio Anda.');
    }

    public function edit(SiswaCertificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) abort(403);
        
        return view('pages.siswa.certificates.edit', compact('certificate'));
    }

    public function update(Request $request, SiswaCertificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiration_date' => 'nullable|date|after_or_equal:issue_date',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:255',
            'file_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'description' => 'nullable|string',
        ]);

        $data = $request->except('file_path');

        if ($request->hasFile('file_path')) {
            if ($certificate->file_path) {
                Storage::disk('public')->delete($certificate->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('siswa/certificates', 'public');
        }

        $certificate->update($data);

        return redirect()->route('siswa.certificates.index')->with('success', 'Data sertifikat berhasil diperbarui.');
    }

    public function destroy(SiswaCertificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) abort(403);

        if ($certificate->file_path) {
            Storage::disk('public')->delete($certificate->file_path);
        }
        
        $certificate->delete();

        return redirect()->route('siswa.certificates.index')->with('success', 'Sertifikat berhasil dihapus dari portofolio.');
    }
}
