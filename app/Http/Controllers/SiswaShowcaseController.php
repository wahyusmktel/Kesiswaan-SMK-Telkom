<?php

namespace App\Http\Controllers;

use App\Models\SiswaSkill;
use App\Models\SiswaProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiswaShowcaseController extends Controller
{
    public function index()
    {
        $skills = SiswaSkill::where('user_id', Auth::id())->orderBy('percentage', 'desc')->get();
        $projects = SiswaProject::where('user_id', Auth::id())->latest()->get();

        return view('pages.siswa.showcase.index', compact('skills', 'projects'));
    }

    public function createSkill()
    {
        return view('pages.siswa.showcase.skill_form');
    }

    public function storeSkill(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:0|max:100',
        ]);

        SiswaSkill::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'percentage' => $request->percentage,
        ]);

        return redirect()->route('siswa.showcase.index')->with('success', 'Keahlian berhasil ditambahkan.');
    }

    public function editSkill(SiswaSkill $skill)
    {
        if ($skill->user_id !== Auth::id()) abort(403);

        return view('pages.siswa.showcase.skill_form', compact('skill'));
    }

    public function updateSkill(Request $request, SiswaSkill $skill)
    {
        if ($skill->user_id !== Auth::id()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:0|max:100',
        ]);

        $skill->update([
            'name' => $request->name,
            'percentage' => $request->percentage,
        ]);

        return redirect()->route('siswa.showcase.index')->with('success', 'Keahlian berhasil diperbarui.');
    }

    public function destroySkill(SiswaSkill $skill)
    {
        if ($skill->user_id !== Auth::id()) abort(403);

        $skill->delete();

        return redirect()->route('siswa.showcase.index')->with('success', 'Keahlian berhasil dihapus.');
    }

    public function createProject()
    {
        return view('pages.siswa.showcase.project_form');
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'github_url' => 'nullable|url|max:255',
            'project_url' => 'nullable|url|max:255',
        ]);

        $data = $request->except('image_path');
        $data['user_id'] = Auth::id();

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('siswa/projects', 'public');
        }

        SiswaProject::create($data);

        return redirect()->route('siswa.showcase.index')->with('success', 'Project portofolio berhasil diunggah.');
    }

    public function editProject(SiswaProject $project)
    {
        if ($project->user_id !== Auth::id()) abort(403);

        return view('pages.siswa.showcase.project_form', compact('project'));
    }

    public function updateProject(Request $request, SiswaProject $project)
    {
        if ($project->user_id !== Auth::id()) abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'github_url' => 'nullable|url|max:255',
            'project_url' => 'nullable|url|max:255',
        ]);

        $data = $request->except('image_path');

        if ($request->hasFile('image_path')) {
            if ($project->image_path) {
                Storage::disk('public')->delete($project->image_path);
            }
            $data['image_path'] = $request->file('image_path')->store('siswa/projects', 'public');
        }

        $project->update($data);

        return redirect()->route('siswa.showcase.index')->with('success', 'Data project berhasil diperbarui.');
    }

    public function destroyProject(SiswaProject $project)
    {
        if ($project->user_id !== Auth::id()) abort(403);

        if ($project->image_path) {
            Storage::disk('public')->delete($project->image_path);
        }

        $project->delete();

        return redirect()->route('siswa.showcase.index')->with('success', 'Project portofolio berhasil dihapus.');
    }
}
