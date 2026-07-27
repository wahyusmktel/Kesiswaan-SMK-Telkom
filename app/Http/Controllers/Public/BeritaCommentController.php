<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\BeritaComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class BeritaCommentController extends Controller
{
    public function store(Request $request, Berita $berita): RedirectResponse
    {
        abort_unless(
            $berita->status === 'published'
                && $berita->published_at
                && $berita->published_at->lte(now()),
            404
        );

        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:berita_comments,id'],
            'name' => [$request->user() ? 'nullable' : 'required', 'nullable', 'string', 'max:120'],
            'email' => [$request->user() ? 'nullable' : 'required', 'nullable', 'email', 'max:255'],
            'content' => ['required', 'string', 'min:3', 'max:2000'],
            'captcha_token' => ['required', 'string', 'max:100'],
            'captcha_answer' => ['required', 'integer'],
            'website' => ['nullable', 'size:0'],
        ], [
            'captcha_answer.required' => 'Jawaban CAPTCHA wajib diisi.',
            'captcha_answer.integer' => 'Jawaban CAPTCHA harus berupa angka.',
            'website.size' => 'Komentar tidak dapat diproses.',
        ]);

        $this->validateCaptcha($request, $berita, $validated['captcha_token'], (int) $validated['captcha_answer']);

        $parent = null;
        if (! empty($validated['parent_id'])) {
            $parent = BeritaComment::approved()
                ->where('berita_id', $berita->id)
                ->whereNull('parent_id')
                ->find($validated['parent_id']);

            if (! $parent) {
                return back()->withErrors(['parent_id' => 'Komentar yang dibalas tidak tersedia.'])->withInput();
            }
        }

        $user = $request->user();
        BeritaComment::create([
            'berita_id' => $berita->id,
            'parent_id' => $parent?->id,
            'user_id' => $user?->id,
            'name' => $user?->name ?: trim((string) $validated['name']),
            'email' => $user?->email ?: strtolower(trim((string) $validated['email'])),
            'content' => trim(strip_tags($validated['content'])),
            'status' => 'pending',
            'ip_hash' => $request->ip() ? hash('sha256', $request->ip().config('app.key')) : null,
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
        ]);

        return redirect()
            ->route('berita.show', $berita->slug)
            ->with('comment_success', 'Komentar berhasil dikirim dan menunggu moderasi admin.');
    }

    private function validateCaptcha(Request $request, Berita $berita, string $token, int $answer): void
    {
        $challenges = $request->session()->get('news_comment_captchas', []);
        $challenge = $challenges[$token] ?? null;
        unset($challenges[$token]);
        $request->session()->put('news_comment_captchas', $challenges);

        if (
            ! is_array($challenge)
            || (int) ($challenge['berita_id'] ?? 0) !== $berita->id
            || (int) ($challenge['expires_at'] ?? 0) < now()->timestamp
            || ! Hash::check((string) $answer, (string) ($challenge['answer_hash'] ?? ''))
        ) {
            throw ValidationException::withMessages([
                'captcha_answer' => 'Jawaban CAPTCHA salah atau sudah kedaluwarsa.',
            ]);
        }
    }
}
