<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\TranscriptSubject;
use Illuminate\Http\Request;

class TranscriptSubjectController extends Controller
{
    public function index(Request $request)
    {
        $groups = TranscriptSubject::groups();
        $subjects = TranscriptSubject::query()
            ->when($request->filled('group'), fn ($query) => $query->where('group', $request->group))
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->search . '%'))
            ->orderBy('group')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('pages.operator.transcript.subjects', compact('subjects', 'groups'));
    }

    public function store(Request $request)
    {
        TranscriptSubject::create($this->validated($request) + ['is_active' => $request->boolean('is_active', true)]);
        toast('Mata pelajaran transkrip berhasil ditambahkan.', 'success');

        return back();
    }

    public function update(Request $request, TranscriptSubject $subject)
    {
        $subject->update($this->validated($request) + ['is_active' => $request->boolean('is_active')]);
        toast('Mata pelajaran transkrip berhasil diperbarui.', 'success');

        return back();
    }

    public function destroy(TranscriptSubject $subject)
    {
        $subject->delete();
        toast('Mata pelajaran transkrip berhasil dihapus.', 'success');

        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer|min:1',
            'group' => 'required|in:umum,muatan_lokal,kejuruan',
        ]);
    }
}
