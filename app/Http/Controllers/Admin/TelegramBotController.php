<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramBot;
use App\Models\TelegramLog;
use App\Models\TelegramUserLink;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TelegramBotController extends Controller
{
    public function index()
    {
        $bots = TelegramBot::withCount('userLinks')->orderBy('purpose')->orderBy('name')->get();
        $links = TelegramUserLink::with(['bot', 'user'])->latest('linked_at')->paginate(25);
        $logs = TelegramLog::with('bot')->latest()->limit(50)->get();

        return view('pages.admin.telegram-bots.index', compact('bots', 'links', 'logs'));
    }

    public function store(Request $request, TelegramService $telegram)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'alpha_dash', 'max:60', 'unique:telegram_bots,slug'],
            'purpose' => ['required', Rule::in(['employment', 'student_affairs', 'general'])],
            'bot_token' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $bot = TelegramBot::create($data + [
            'webhook_secret' => Str::random(48),
            'is_active' => $request->boolean('is_active'),
        ]);
        $result = $telegram->verifyAndRegisterWebhook($bot);

        return back()->with($result['success'] ? 'success' : 'error', $result['success']
            ? 'Bot Telegram berhasil dibuat dan webhook sudah aktif.'
            : 'Bot tersimpan, tetapi verifikasi gagal: '.$result['message']);
    }

    public function update(Request $request, TelegramBot $telegramBot, TelegramService $telegram)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'alpha_dash', 'max:60', Rule::unique('telegram_bots', 'slug')->ignore($telegramBot)],
            'purpose' => ['required', Rule::in(['employment', 'student_affairs', 'general'])],
            'bot_token' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        if (blank($data['bot_token'] ?? null)) {
            unset($data['bot_token']);
        }
        $telegramBot->update($data + ['is_active' => $request->boolean('is_active')]);
        $result = $telegram->verifyAndRegisterWebhook($telegramBot);

        return back()->with($result['success'] ? 'success' : 'error', $result['success']
            ? 'Bot Telegram berhasil diperbarui dan diverifikasi.'
            : 'Konfigurasi tersimpan, tetapi verifikasi gagal: '.$result['message']);
    }

    public function verify(TelegramBot $telegramBot, TelegramService $telegram)
    {
        $result = $telegram->verifyAndRegisterWebhook($telegramBot);

        return back()->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Bot dan webhook Telegram terhubung.' : $result['message']);
    }

    public function destroy(TelegramBot $telegramBot)
    {
        $telegramBot->delete();

        return back()->with('success', 'Bot Telegram berhasil dihapus.');
    }

    public function unlink(TelegramUserLink $telegramUserLink, TelegramService $telegram)
    {
        $result = $telegram->prepareAccountForRelinking($telegramUserLink);
        $telegramUserLink->delete();

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function sendTest(TelegramUserLink $telegramUserLink, TelegramService $telegram)
    {
        $result = $telegram->sendTestNotification($telegramUserLink);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
