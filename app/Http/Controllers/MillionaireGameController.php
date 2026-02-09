<?php

namespace App\Http\Controllers;

use App\Models\MillionaireQuestion;
use App\Models\MillionaireSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MillionaireGameController extends Controller
{
    /**
     * Show the selection of question sets for players.
     */
    public function index()
    {
        $sets = MillionaireSet::where('is_active', true)->withCount('questions')->get();
        return view('millionaire.index', compact('sets'));
    }

    /**
     * Show the game interface for a specific set.
     */
    public function play(MillionaireSet $set)
    {
        return view('millionaire.play', compact('set'));
    }

    /**
     * Get questions for a set (API).
     */
    public function getQuestions(MillionaireSet $set)
    {
        $questions = $set->questions()->orderBy('level', 'asc')->get();
        return response()->json($questions);
    }

    /**
     * Show the management interface for Guru Kelas.
     */
    public function manage()
    {
        $sets = MillionaireSet::where('user_id', Auth::id())->with('questions')->get();
        return view('millionaire.manage', compact('sets'));
    }

    /**
     * Store a new question set.
     */
    public function storeSet(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        MillionaireSet::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'is_active' => true,
        ]);

        return back()->with('success', 'Set soal berhasil dibuat.');
    }

    /**
     * Update a question set.
     */
    public function updateSet(Request $request, MillionaireSet $set)
    {
        $this->authorizeOwner($set);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $set->update($request->only('name', 'description', 'is_active'));

        return back()->with('success', 'Set soal berhasil diperbarui.');
    }

    /**
     * Delete a question set.
     */
    public function destroySet(MillionaireSet $set)
    {
        $this->authorizeOwner($set);
        $set->delete();

        return back()->with('success', 'Set soal berhasil dihapus.');
    }

    /**
     * Store a new question.
     */
    public function storeQuestion(Request $request)
    {
        $request->validate([
            'set_id' => 'required|exists:millionaire_sets,id',
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'level' => 'required|integer|min:1|max:15',
        ]);

        $set = MillionaireSet::findOrFail($request->set_id);
        $this->authorizeOwner($set);

        MillionaireQuestion::create($request->all());

        return back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    /**
     * Update a question.
     */
    public function updateQuestion(Request $request, MillionaireQuestion $question)
    {
        $this->authorizeOwner($question->set);

        $request->validate([
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'level' => 'required|integer|min:1|max:15',
        ]);

        $question->update($request->all());

        return back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Delete a question.
     */
    public function destroyQuestion(MillionaireQuestion $question)
    {
        $this->authorizeOwner($question->set);
        $question->delete();

        return back()->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * Helper to authorize user.
     */
    private function authorizeOwner(MillionaireSet $set)
    {
        if ($set->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
