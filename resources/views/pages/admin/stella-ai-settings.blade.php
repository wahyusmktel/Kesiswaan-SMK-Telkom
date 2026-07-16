<x-app-layout>
    <x-slot name="header">
        <h2 class="flex items-center gap-2 text-xl font-bold leading-tight text-gray-800">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.663 17h4.673M12 3v1m6.364 1.636-.707.707M21 12h-1M4 12H3m3.343-5.657-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547Z" />
            </svg>
            Konfigurasi Stella AI
        </h2>
    </x-slot>

    <div class="w-full py-6">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            <form action="{{ route('super-admin.stella-ai.settings.update') }}" method="POST"
                x-data="stellaAiSettings()" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                @csrf

                <div class="flex flex-col gap-3 border-b border-gray-100 bg-gray-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800">Koneksi Layanan AI</h3>
                        <p class="mt-1 text-xs text-gray-500">Gunakan endpoint yang kompatibel dengan format OpenAI.</p>
                    </div>

                    @if($isReady)
                        <span class="inline-flex w-fit items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                            Aktif dan siap
                        </span>
                    @else
                        <span class="inline-flex w-fit items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600">
                            <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                            Belum siap
                        </span>
                    @endif
                </div>

                <div class="space-y-6 p-5 sm:p-6">
                    <div>
                        <label for="stella_ai_base_url" class="block text-sm font-semibold text-gray-700">Base URL API</label>
                        <input type="url" name="stella_ai_base_url" id="stella_ai_base_url"
                            x-ref="baseUrl"
                            value="{{ old('stella_ai_base_url', $setting->stella_ai_base_url) }}"
                            placeholder="https://api.openai.com/v1"
                            class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm @error('stella_ai_base_url') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('stella_ai_base_url')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stella_ai_api_key" class="block text-sm font-semibold text-gray-700">API Key</label>
                        <div class="relative mt-1">
                            <input :type="showApiKey ? 'text' : 'password'" name="stella_ai_api_key" id="stella_ai_api_key"
                                x-ref="apiKey"
                                value=""
                                autocomplete="new-password"
                                placeholder="{{ $setting->stella_ai_api_key ? 'Tersimpan - kosongkan jika tidak ingin mengganti' : 'Masukkan API key' }}"
                                class="block w-full rounded-lg pr-24 shadow-sm sm:text-sm @error('stella_ai_api_key') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            <button type="button" @click="showApiKey = !showApiKey"
                                class="absolute inset-y-0 right-0 px-3 text-xs font-semibold text-gray-500 hover:text-gray-800"
                                x-text="showApiKey ? 'Sembunyikan' : 'Tampilkan'">
                            </button>
                        </div>
                        @error('stella_ai_api_key')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="stella_ai_chat_model" class="block text-sm font-semibold text-gray-700">Model Chat</label>
                            <input type="text" name="stella_ai_chat_model" id="stella_ai_chat_model"
                                value="{{ old('stella_ai_chat_model', $setting->stella_ai_chat_model) }}"
                                placeholder="gpt-4o-mini"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                            @error('stella_ai_chat_model')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stella_ai_image_model" class="block text-sm font-semibold text-gray-700">Model Gambar</label>
                            <input type="text" name="stella_ai_image_model" id="stella_ai_image_model"
                                value="{{ old('stella_ai_image_model', $setting->stella_ai_image_model) }}"
                                placeholder="dall-e-3"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                            @error('stella_ai_image_model')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <label class="inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="stella_ai_enabled" value="1" class="peer sr-only"
                                {{ old('stella_ai_enabled', $setting->stella_ai_enabled) ? 'checked' : '' }}>
                            <span class="relative h-6 w-11 rounded-full bg-gray-200 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-transform peer-checked:bg-red-600 peer-checked:after:translate-x-5"></span>
                            <span class="ml-3 text-sm font-semibold text-gray-700">Aktifkan Stella AI untuk semua pengguna</span>
                        </label>
                        <p class="mt-2 text-xs text-gray-500">Menu pengguna hanya muncul setelah Base URL, API key, dan model chat terisi.</p>
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <button type="button" @click="testConnection()" :disabled="testing"
                            class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-200 disabled:cursor-wait disabled:opacity-60">
                            <svg x-show="!testing" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m13 10 7-7-3 8h4l-10 10 3-8h-4l3-3Z" />
                            </svg>
                            <svg x-show="testing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z"></path>
                            </svg>
                            <span x-text="testing ? 'Menguji koneksi...' : 'Tes Koneksi'"></span>
                        </button>

                        <div x-cloak x-show="testResult" x-transition
                            :class="testResult?.success ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'"
                            class="mt-3 rounded-lg border px-4 py-3 text-sm font-medium"
                            x-text="testResult?.message">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-5 py-4 sm:px-6">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-red-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7" />
                        </svg>
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function stellaAiSettings() {
                return {
                    showApiKey: false,
                    testing: false,
                    testResult: null,

                    async testConnection() {
                        this.testing = true;
                        this.testResult = null;

                        try {
                            const response = await fetch("{{ route('super-admin.stella-ai.test') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    stella_ai_base_url: this.$refs.baseUrl.value,
                                    stella_ai_api_key: this.$refs.apiKey.value,
                                }),
                            });

                            const data = await response.json();
                            this.testResult = response.ok
                                ? data
                                : { success: false, message: data.message || 'Konfigurasi belum valid.' };
                        } catch (error) {
                            this.testResult = {
                                success: false,
                                message: 'Gagal menghubungi server aplikasi.',
                            };
                        } finally {
                            this.testing = false;
                        }
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
