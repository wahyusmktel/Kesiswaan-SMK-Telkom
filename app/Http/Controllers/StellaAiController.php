<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\StellaAiConversation;
use App\Models\StellaAiMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StellaAiController extends Controller
{
    public function index()
    {
        $setting = $this->enabledSetting();

        $conversations = StellaAiConversation::where('user_id', Auth::id())
            ->orderByDesc('updated_at')
            ->get();

        return view('pages.stella-ai.index', [
            'conversations' => $conversations,
            'imageEnabled' => filled($setting->stella_ai_image_model),
        ]);
    }

    public function createConversation(Request $request)
    {
        $this->enabledSetting();

        $conversation = StellaAiConversation::create([
            'user_id' => Auth::id(),
            'title' => 'Percakapan Baru',
        ]);

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->title,
        ]);
    }

    public function getMessages($conversationId)
    {
        $this->enabledSetting();

        $conversation = StellaAiConversation::where('user_id', Auth::id())
            ->findOrFail($conversationId);

        $messages = $conversation->messages()->orderBy('created_at')->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:stella_ai_conversations,id',
            'message' => 'required|string|max:4000',
            'type' => 'nullable|in:text,image_request',
        ]);

        $conversation = StellaAiConversation::where('user_id', Auth::id())
            ->findOrFail($request->conversation_id);

        $setting = $this->enabledSetting();

        $messageType = $request->input('type', 'text');
        $isImageRequest = $messageType === 'image_request';

        if ($isImageRequest && !$setting->stella_ai_image_model) {
            return response()->json([
                'message' => 'Fitur pembuatan gambar belum dikonfigurasi oleh administrator.',
            ], 422);
        }

        StellaAiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $request->message,
            'type' => $isImageRequest ? 'image_request' : 'text',
        ]);

        // Update conversation title from first message
        if ($conversation->messages()->count() <= 1) {
            $conversation->update([
                'title' => Str::limit($request->message, 50),
            ]);
        } else {
            $conversation->touch();
        }

        try {
            if ($isImageRequest) {
                $response = $this->generateImage($setting, $conversation, $request->message);
            } else {
                $response = $this->chatCompletion($setting, $conversation);
            }

            return response()->json($response);
        } catch (\Throwable $e) {
            Log::error('Stella AI request failed.', [
                'user_id' => Auth::id(),
                'conversation_id' => $conversation->id,
                'type' => $messageType,
                'exception' => $e,
            ]);

            $errorMessage = StellaAiMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => 'Maaf, Stella AI belum dapat memproses permintaan ini. Silakan coba kembali.',
                'type' => 'text',
            ]);

            return response()->json([
                'message' => $errorMessage,
                'error' => true,
            ], 500);
        }
    }

    private function chatCompletion(AppSetting $setting, StellaAiConversation $conversation): array
    {
        $history = $conversation->messages()
            ->where('type', '!=', 'image_response')
            ->latest('id')
            ->limit(30)
            ->get()
            ->reverse()
            ->values()
            ->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ];
            })
            ->toArray();

        // Add system prompt
        array_unshift($history, [
            'role' => 'system',
            'content' => 'Kamu adalah Stella AI, asisten cerdas di lingkungan SMK Telkom. Kamu ramah, membantu, dan menjawab dalam Bahasa Indonesia. Kamu bisa membantu dengan pertanyaan akademik, tugas sekolah, dan hal umum lainnya.',
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $setting->stella_ai_api_key,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post(rtrim($setting->stella_ai_base_url, '/') . '/chat/completions', [
            'model' => $setting->stella_ai_chat_model,
            'messages' => $history,
            'max_tokens' => 2048,
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Chat API returned HTTP '.$response->status().'.');
        }

        $data = $response->json();
        $assistantContent = $data['choices'][0]['message']['content'] ?? 'Maaf, tidak ada respon.';

        $assistantMessage = StellaAiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $assistantContent,
            'type' => 'text',
        ]);

        return ['message' => $assistantMessage];
    }

    private function generateImage(
        AppSetting $setting,
        StellaAiConversation $conversation,
        string $prompt
    ): array
    {
        $imageModel = $setting->stella_ai_image_model;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $setting->stella_ai_api_key,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post(rtrim($setting->stella_ai_base_url, '/') . '/images/generations', [
            'model' => $imageModel,
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1024x1024',
            'response_format' => 'b64_json',
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Image API returned HTTP '.$response->status().'.');
        }

        $data = $response->json();

        $imagePath = null;
        $content = 'Gambar berhasil dibuat.';

        if (isset($data['data'][0]['b64_json'])) {
            $imageData = base64_decode($data['data'][0]['b64_json'], true);
            if ($imageData === false) {
                throw new \RuntimeException('Image API returned invalid base64 data.');
            }

            $filename = 'stella-ai/' . Str::uuid() . '.png';
            Storage::disk('public')->put($filename, $imageData);
            $imagePath = $filename;
        } elseif (isset($data['data'][0]['url'])) {
            $imagePath = $data['data'][0]['url'];
        } else {
            throw new \RuntimeException('Image API returned no image.');
        }

        $assistantMessage = StellaAiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $content,
            'image_path' => $imagePath,
            'type' => 'image_response',
        ]);

        return ['message' => $assistantMessage];
    }

    public function deleteConversation($conversationId)
    {
        $this->enabledSetting();

        $conversation = StellaAiConversation::where('user_id', Auth::id())
            ->findOrFail($conversationId);

        $conversation->delete();

        return response()->json(['success' => true]);
    }

    public function aiSettings()
    {
        $setting = AppSetting::first() ?? new AppSetting();

        return view('pages.admin.stella-ai-settings', [
            'setting' => $setting,
            'isReady' => $this->settingIsReady($setting),
        ]);
    }

    public function updateAiSettings(Request $request)
    {
        $request->validate([
            'stella_ai_base_url' => 'nullable|required_if:stella_ai_enabled,1|url|max:500',
            'stella_ai_api_key' => 'nullable|string|max:500',
            'stella_ai_chat_model' => 'nullable|required_if:stella_ai_enabled,1|string|max:255',
            'stella_ai_image_model' => 'nullable|string|max:255',
            'stella_ai_enabled' => 'nullable|boolean',
        ]);

        $setting = AppSetting::first() ?? new AppSetting();

        if ($request->boolean('stella_ai_enabled')
            && !$request->filled('stella_ai_api_key')
            && !$setting->stella_ai_api_key) {
            throw ValidationException::withMessages([
                'stella_ai_api_key' => 'API Key wajib diisi saat Stella AI diaktifkan.',
            ]);
        }

        $settings = [
            'stella_ai_base_url' => $request->input('stella_ai_base_url'),
            'stella_ai_chat_model' => $request->input('stella_ai_chat_model'),
            'stella_ai_image_model' => $request->input('stella_ai_image_model'),
            'stella_ai_enabled' => $request->has('stella_ai_enabled'),
        ];

        if ($request->filled('stella_ai_api_key')) {
            $settings['stella_ai_api_key'] = $request->input('stella_ai_api_key');
        }

        $setting->fill($settings);
        $setting->save();

        toast('Konfigurasi Stella AI berhasil disimpan.', 'success');
        return redirect()->back();
    }

    public function testConnection(Request $request)
    {
        $setting = AppSetting::first();

        $validated = $request->validate([
            'stella_ai_base_url' => 'nullable|url|max:500',
            'stella_ai_api_key' => 'nullable|string|max:500',
        ]);

        $baseUrl = $validated['stella_ai_base_url'] ?? $setting?->stella_ai_base_url;
        $apiKey = $validated['stella_ai_api_key'] ?? $setting?->stella_ai_api_key;

        if (!$baseUrl || !$apiKey) {
            return response()->json(['success' => false, 'message' => 'Base URL dan API Key harus diisi terlebih dahulu.']);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->get(rtrim($baseUrl, '/') . '/models');

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Koneksi berhasil!', 'models' => $response->json()]);
            }

            return response()->json(['success' => false, 'message' => 'Gagal: ' . $response->status()]);
        } catch (\Throwable $e) {
            Log::warning('Stella AI connection test failed.', [
                'user_id' => Auth::id(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Koneksi ke layanan AI gagal. Periksa Base URL, API Key, dan koneksi server.',
            ]);
        }
    }

    private function enabledSetting(): AppSetting
    {
        $setting = AppSetting::first();

        abort_unless(
            $this->settingIsReady($setting),
            403,
            'Stella AI belum diaktifkan atau konfigurasinya belum lengkap.'
        );

        return $setting;
    }

    private function settingIsReady(?AppSetting $setting): bool
    {
        return (bool) (
            $setting?->stella_ai_enabled
            && $setting->stella_ai_base_url
            && $setting->stella_ai_api_key
            && $setting->stella_ai_chat_model
        );
    }
}
