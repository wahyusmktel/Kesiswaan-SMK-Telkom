<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicShowcaseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $studentsQuery = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Siswa', 'siswa']);
        })->where(function($q) {
            $q->has('siswaSkills')->orHas('siswaProjects');
        })->with(['siswaSkills' => function($q) {
            $q->orderBy('percentage', 'desc');
        }, 'siswaProjects' => function($q) {
            $q->latest();
        }, 'masterSiswa.rombels.kelas']);

        if ($search) {
            $studentsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('siswaSkills', function($qSkill) use ($search) {
                      $qSkill->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $students = $studentsQuery->paginate(12)->withQueryString();
        
        // Return JSON for Vue API calls
        if ($request->wantsJson()) {
            // Format the items slightly to make them easier for Vue to consume
            $formattedStudents = $students->through(function($student) {
                $latestRombel = $student->masterSiswa?->rombels->last();
                $jurusan = $latestRombel?->kelas?->jurusan ?? 'Siswa SMK Telkom';
                
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'avatar' => $student->avatar ? \Storage::url($student->avatar) : null,
                    'jurusan' => $jurusan,
                    'skills' => $student->siswaSkills->take(3)->map(fn($s) => ['name' => $s->name, 'percentage' => $s->percentage]),
                    'skills_count' => $student->siswaSkills->count(),
                    'projects_count' => $student->siswaProjects->count(),
                    'url' => route('public.showcase.show', $student->id)
                ];
            });
            return response()->json([
                'data' => $formattedStudents->items(),
                'links' => $formattedStudents->linkCollection(),
                'current_page' => $formattedStudents->currentPage(),
                'last_page' => $formattedStudents->lastPage(),
            ]);
        }

        // Generate the payload for initial render
        $payload = [
            'routes' => [
                'home' => url('/'),
                'api_search' => route('public.showcase.index'),
            ]
        ];

        return view('public.showcase.index', compact('payload'));
    }

    public function show($id)
    {
        $student = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Siswa', 'siswa']);
        })->with(['siswaSkills' => function($q) {
            $q->orderBy('percentage', 'desc');
        }, 'siswaProjects' => function($q) {
            $q->latest();
        }, 'masterSiswa.rombels.kelas'])->findOrFail($id);

        return view('public.showcase.show', compact('student'));
    }
}
