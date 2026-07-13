<?php

namespace App\Http\Controllers;

use App\Models\StudentRegistration;
use Illuminate\Http\Request;

class PublicStudentRegistrationController extends Controller
{
    public function create()
    {
        return view('pages.public.student-registration');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        unset($validated['website'], $validated['consent']);

        $duplicate = StudentRegistration::whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($validated) {
                if (!empty($validated['nisn'])) {
                    $query->where('nisn', $validated['nisn']);
                } else {
                    $query->where('nama_lengkap', $validated['nama_lengkap'])
                        ->whereDate('tanggal_lahir', $validated['tanggal_lahir']);
                }
            })->exists();

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'nama_lengkap' => 'Pendaftaran dengan identitas yang sama masih dalam proses verifikasi.',
            ]);
        }

        $registration = StudentRegistration::create($validated + [
            'source' => 'public',
            'status' => 'pending',
        ]);

        return redirect()->route('student-registration.status', $registration->public_token);
    }

    public function status(string $token)
    {
        $registration = StudentRegistration::where('public_token', $token)->firstOrFail();

        return view('pages.public.student-registration-status', compact('registration'));
    }

    private function rules(): array
    {
        return [
            'website' => 'nullable|max:0',
            'consent' => 'required|accepted',
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'nullable|digits:10',
            'nik' => 'nullable|digits:16',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string|max:1000',
            'nomor_hp' => 'required|string|max:25',
            'email' => 'nullable|email|max:255',
            'sekolah_asal' => 'nullable|string|max:255',
            'nama_orang_tua' => 'nullable|string|max:255',
            'nomor_hp_orang_tua' => 'nullable|string|max:25',
        ];
    }
}
