<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicShowcaseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Retrieve users who have either skills or projects
        $studentsQuery = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Siswa', 'siswa']);
        })->where(function($q) {
            $q->has('siswaSkills')->orHas('siswaProjects');
        })->with(['siswaSkills' => function($q) {
            $q->orderBy('percentage', 'desc');
        }, 'siswaProjects']);

        if ($search) {
            $studentsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('siswaSkills', function($qSkill) use ($search) {
                      $qSkill->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $students = $studentsQuery->paginate(12)->withQueryString();

        return view('public.showcase.index', compact('students', 'search'));
    }

    public function show($id)
    {
        $student = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Siswa', 'siswa']);
        })->with(['siswaSkills' => function($q) {
            $q->orderBy('percentage', 'desc');
        }, 'siswaProjects' => function($q) {
            $q->latest();
        }])->findOrFail($id);

        return view('public.showcase.show', compact('student'));
    }
}
