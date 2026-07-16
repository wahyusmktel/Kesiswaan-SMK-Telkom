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
            'imageEnabled' => $this->imageSettingIsReady($setting),
            'availableModels' => $this->availableModels($setting),
            'defaultModel' => $setting->stella_ai_chat_model,
        ]);
    }

    public function createConversation(Request $request)
    {
        $setting = $this->enabledSetting();
        $validated = $request->validate([
            'model' => 'nullable|string|max:255',
        ]);
        $model = $this->resolveAllowedModel($setting, $validated['model'] ?? null);

        $conversation = StellaAiConversation::create([
            'user_id' => Auth::id(),
            'title' => 'Percakapan Baru',
            'model' => $model,
        ]);

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->title,
            'model' => $conversation->model,
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

    public function updateConversationModel(Request $request, $conversationId)
    {
        $setting = $this->enabledSetting();
        $validated = $request->validate([
            'model' => 'required|string|max:255',
        ]);

        $conversation = StellaAiConversation::where('user_id', Auth::id())
            ->findOrFail($conversationId);
        $model = $this->resolveAllowedModel($setting, $validated['model']);

        $conversation->update(['model' => $model]);

        return response()->json([
            'success' => true,
            'model' => $model,
        ]);
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
        $model = $this->resolveAllowedModel($setting, $conversation->model);

        if ($conversation->model !== $model) {
            $conversation->update(['model' => $model]);
        }

        $messageType = $request->input('type', 'text');
        $isImageRequest = $messageType === 'image_request';

        if ($isImageRequest && !$this->imageSettingIsReady($setting)) {
            return response()->json([
                'message' => 'Provider gambar belum dikonfigurasi oleh administrator.',
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
                $response = $this->chatCompletion($setting, $conversation, $model);
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
                'content' => $isImageRequest
                    ? 'Generate gambar gagal. Periksa endpoint, API key, model gambar, serta kredit provider pada konfigurasi Stella AI.'
                    : 'Maaf, Stella AI belum dapat memproses permintaan ini. Silakan coba kembali.',
                'type' => 'text',
            ]);

            return response()->json([
                'message' => $errorMessage,
                'error' => true,
            ], 500);
        }
    }

    private function chatCompletion(
        AppSetting $setting,
        StellaAiConversation $conversation,
        string $model
    ): array
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

        $response = $this->aiClient($setting->stella_ai_api_key)
            ->timeout(120)
            ->post(rtrim($setting->stella_ai_base_url, '/') . '/chat/completions', [
                'model' => $model,
                'messages' => $history,
                'stream' => false,
            ]);

        if ($response->failed()) {
            Log::warning('Stella AI provider rejected chat request.', [
                'user_id' => Auth::id(),
                'provider_host' => parse_url($setting->stella_ai_base_url, PHP_URL_HOST),
                'model' => $model,
                'status' => $response->status(),
                'response' => Str::limit($response->body(), 2000),
            ]);

            throw new \RuntimeException(
                'Chat API returned HTTP '.$response->status().': '.$this->providerErrorMessage($response)
            );
        }

        $data = $response->json();
        $assistantContent = data_get($data, 'choices.0.message.content')
            ?: data_get($data, 'choices.0.message.reasoning_content');

        if (!is_string($assistantContent) || trim($assistantContent) === '') {
            Log::warning('Stella AI provider returned an unsupported response.', [
                'user_id' => Auth::id(),
                'provider_host' => parse_url($setting->stella_ai_base_url, PHP_URL_HOST),
                'model' => $model,
                'response_keys' => is_array($data) ? array_keys($data) : [],
            ]);

            throw new \RuntimeException('Chat API returned no readable assistant content.');
        }

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

        $response = $this->aiClient($setting->stella_ai_image_api_key)
            ->timeout(180)
            ->post($setting->stella_ai_image_endpoint, [
                'model' => $imageModel,
                'prompt' => $prompt,
                'n' => 1,
            ]);

        if ($response->failed()) {
            Log::warning('Stella AI provider rejected image request.', [
                'user_id' => Auth::id(),
                'provider_host' => parse_url($setting->stella_ai_image_endpoint, PHP_URL_HOST),
                'model' => $imageModel,
                'status' => $response->status(),
                'response' => Str::limit($response->body(), 2000),
            ]);

            throw new \RuntimeException(
                'Image API returned HTTP '.$response->status().': '.$this->providerErrorMessage($response)
            );
        }

        $data = $response->json();

        $imagePath = null;
        $content = 'Gambar berhasil dibuat.';

        $base64Image = data_get($data, 'data.0.b64_json')
            ?: data_get($data, 'data.0.b64_ephemeral');
        $remoteUrl = data_get($data, 'data.0.url');

        if (is_string($base64Image) && $base64Image !== '') {
            $imageData = base64_decode($base64Image, true);
            if ($imageData === false) {
                throw new \RuntimeException('Image API returned invalid base64 data.');
            }

            $filename = 'stella-ai/' . Str::uuid() . '.png';
            Storage::disk('public')->put($filename, $imageData);
            $imagePath = $filename;
        } elseif (is_string($remoteUrl) && filter_var($remoteUrl, FILTER_VALIDATE_URL)) {
            $imagePath = $remoteUrl;
        } else {
            Log::warning('Stella AI image provider returned an unsupported response.', [
                'user_id' => Auth::id(),
                'provider_host' => parse_url($setting->stella_ai_image_endpoint, PHP_URL_HOST),
                'model' => $imageModel,
                'response_keys' => is_array($data) ? array_keys($data) : [],
            ]);

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
            'availableModels' => $this->availableModels($setting),
        ]);
    }

    public function updateAiSettings(Request $request)
    {
        $request->validate([
            'stella_ai_base_url' => 'nullable|required_if:stella_ai_enabled,1|url|max:500',
            'stella_ai_api_key' => 'nullable|string|max:500',
            'stella_ai_chat_model' => 'nullable|required_if:stella_ai_enabled,1|string|max:255',
            'stella_ai_models_json' => 'nullable|string|max:20000',
            'stella_ai_image_model' => 'nullable|string|max:255',
            'stella_ai_image_endpoint' => 'nullable|url|max:500',
            'stella_ai_image_api_key' => 'nullable|string|max:500',
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

        $models = $this->decodeModels($request->input('stella_ai_models_json'));
        $defaultModel = trim((string) $request->input('stella_ai_chat_model'));

        if ($defaultModel !== '' && !in_array($defaultModel, $models, true)) {
            $models[] = $defaultModel;
        }

        $settings = [
            'stella_ai_base_url' => $request->input('stella_ai_base_url'),
            'stella_ai_chat_model' => $defaultModel ?: null,
            'stella_ai_models' => array_values(array_unique($models)),
            'stella_ai_image_model' => $request->input('stella_ai_image_model'),
            'stella_ai_image_endpoint' => $request->input('stella_ai_image_endpoint'),
            'stella_ai_enabled' => $request->has('stella_ai_enabled'),
        ];

        if ($request->filled('stella_ai_api_key')) {
            $settings['stella_ai_api_key'] = $request->input('stella_ai_api_key');
        }

        if ($request->filled('stella_ai_image_api_key')) {
            $settings['stella_ai_image_api_key'] = $request->input('stella_ai_image_api_key');
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
            'stella_ai_chat_model' => 'nullable|string|max:255',
        ]);

        $baseUrl = $validated['stella_ai_base_url'] ?? $setting?->stella_ai_base_url;
        $apiKey = $validated['stella_ai_api_key'] ?? $setting?->stella_ai_api_key;
        $chatModel = $validated['stella_ai_chat_model'] ?? $setting?->stella_ai_chat_model;

        if (!$baseUrl || !$apiKey || !$chatModel) {
            return response()->json([
                'success' => false,
                'message' => 'Base URL, API Key, dan Model Chat harus diisi terlebih dahulu.',
            ]);
        }

        try {
            $response = $this->aiClient($apiKey)
                ->timeout(60)
                ->post(rtrim($baseUrl, '/') . '/chat/completions', [
                    'model' => $chatModel,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'Balas tepat dengan kata: TERHUBUNG',
                        ],
                    ],
                    'stream' => false,
                ]);

            if ($response->successful()) {
                $content = data_get($response->json(), 'choices.0.message.content')
                    ?: data_get($response->json(), 'choices.0.message.reasoning_content');

                if (!is_string($content) || trim($content) === '') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Provider merespons, tetapi format jawaban model tidak dikenali.',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Koneksi dan model berhasil diuji.',
                ]);
            }

            Log::warning('Stella AI connection test was rejected by provider.', [
                'user_id' => Auth::id(),
                'provider_host' => parse_url($baseUrl, PHP_URL_HOST),
                'model' => $chatModel,
                'status' => $response->status(),
                'response' => Str::limit($response->body(), 2000),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Provider menolak request (HTTP '.$response->status().'): '
                    .$this->providerErrorMessage($response),
            ]);
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

    public function discoverModels(Request $request)
    {
        $setting = AppSetting::first();
        $validated = $request->validate([
            'stella_ai_base_url' => 'nullable|url|max:500',
            'stella_ai_api_key' => 'nullable|string|max:500',
        ]);

        $baseUrl = $validated['stella_ai_base_url'] ?? $setting?->stella_ai_base_url;
        $apiKey = $validated['stella_ai_api_key'] ?? $setting?->stella_ai_api_key;

        if (!$baseUrl || !$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Base URL dan API Key harus diisi terlebih dahulu.',
            ], 422);
        }

        try {
            $response = $this->aiClient($apiKey)
                ->timeout(30)
                ->get(rtrim($baseUrl, '/') . '/models');

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider tidak menyediakan daftar model (HTTP '.$response->status().'). Gunakan input manual.',
                ]);
            }

            $models = collect(data_get($response->json(), 'data', []))
                ->map(fn ($model) => is_array($model) ? ($model['id'] ?? null) : null)
                ->filter(fn ($model) => is_string($model) && trim($model) !== '')
                ->map(fn ($model) => trim($model))
                ->unique()
                ->sort()
                ->values()
                ->all();

            if ($models === []) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider merespons, tetapi daftar model tidak ditemukan. Gunakan input manual.',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => count($models).' model ditemukan.',
                'models' => $models,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Stella AI model discovery failed.', [
                'user_id' => Auth::id(),
                'provider_host' => parse_url($baseUrl, PHP_URL_HOST),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Daftar model tidak dapat diambil. Gunakan input manual dari tombol Copy di provider.',
            ]);
        }
    }

    public function testImageConnection(Request $request)
    {
        $setting = AppSetting::first();
        $validated = $request->validate([
            'stella_ai_image_endpoint' => 'nullable|url|max:500',
            'stella_ai_image_api_key' => 'nullable|string|max:500',
            'stella_ai_image_model' => 'nullable|string|max:255',
        ]);

        $endpoint = $validated['stella_ai_image_endpoint'] ?? $setting?->stella_ai_image_endpoint;
        $apiKey = $validated['stella_ai_image_api_key'] ?? $setting?->stella_ai_image_api_key;
        $model = $validated['stella_ai_image_model'] ?? $setting?->stella_ai_image_model;

        if (!$endpoint || !$apiKey || !$model) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint, API Key, dan Model Gambar harus diisi terlebih dahulu.',
            ]);
        }

        try {
            $response = $this->aiClient($apiKey)
                ->timeout(180)
                ->post($endpoint, [
                    'model' => $model,
                    'prompt' => 'A simple red circle on a clean white background.',
                    'n' => 1,
                ]);

            if ($response->failed()) {
                Log::warning('Stella AI image connection test was rejected.', [
                    'user_id' => Auth::id(),
                    'provider_host' => parse_url($endpoint, PHP_URL_HOST),
                    'model' => $model,
                    'status' => $response->status(),
                    'response' => Str::limit($response->body(), 2000),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Provider gambar menolak request (HTTP '.$response->status().'): '
                        .$this->providerErrorMessage($response),
                ]);
            }

            $hasImage = filled(data_get($response->json(), 'data.0.url'))
                || filled(data_get($response->json(), 'data.0.b64_json'))
                || filled(data_get($response->json(), 'data.0.b64_ephemeral'));

            return response()->json([
                'success' => $hasImage,
                'message' => $hasImage
                    ? 'Endpoint dan model gambar berhasil diuji.'
                    : 'Provider merespons, tetapi hasil gambar tidak ditemukan dalam format yang didukung.',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Stella AI image connection test failed.', [
                'user_id' => Auth::id(),
                'provider_host' => parse_url($endpoint, PHP_URL_HOST),
                'model' => $model,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Koneksi ke provider gambar gagal. Periksa endpoint dan koneksi server.',
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

    private function imageSettingIsReady(?AppSetting $setting): bool
    {
        return (bool) (
            $setting?->stella_ai_image_model
            && $setting->stella_ai_image_endpoint
            && $setting->stella_ai_image_api_key
        );
    }

    private function availableModels(AppSetting $setting): array
    {
        $models = collect($setting->stella_ai_models ?? [])
            ->filter(fn ($model) => is_string($model) && trim($model) !== '')
            ->map(fn ($model) => trim($model))
            ->push($setting->stella_ai_chat_model)
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $models;
    }

    private function resolveAllowedModel(AppSetting $setting, ?string $requestedModel): string
    {
        $models = $this->availableModels($setting);
        $requestedModel = trim((string) $requestedModel);

        if ($requestedModel !== '' && in_array($requestedModel, $models, true)) {
            return $requestedModel;
        }

        return $setting->stella_ai_chat_model;
    }

    private function decodeModels(?string $json): array
    {
        if (!$json) {
            return [];
        }

        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            throw ValidationException::withMessages([
                'stella_ai_models_json' => 'Daftar model tidak valid.',
            ]);
        }

        return collect($decoded)
            ->filter(fn ($model) => is_string($model) && trim($model) !== '')
            ->map(fn ($model) => trim($model))
            ->unique()
            ->take(200)
            ->values()
            ->all();
    }

    private function aiClient(string $apiKey)
    {
        return Http::acceptJson()
            ->asJson()
            ->withToken($apiKey);
    }

    private function providerErrorMessage($response): string
    {
        $message = data_get($response->json(), 'error.message')
            ?? data_get($response->json(), 'message')
            ?? data_get($response->json(), 'detail');

        if (!is_string($message) || trim($message) === '') {
            return 'Tidak ada detail error dari provider.';
        }

        return Str::limit(strip_tags($message), 300);
    }
}
