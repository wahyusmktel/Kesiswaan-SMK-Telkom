<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            Stella AI
        </h2>
    </x-slot>

    <div class="py-4 w-full h-[calc(100vh-10rem)]" x-data="stellaAiChat()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden h-full flex">

                <!-- Sidebar Conversations -->
                <div class="w-72 border-r border-gray-200 flex flex-col bg-gray-50/50 flex-shrink-0 hidden md:flex">
                    <div class="p-4 border-b border-gray-100">
                        <button @click="newConversation()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-500 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Chat Baru
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-2 space-y-1">
                        <template x-for="conv in conversations" :key="conv.id">
                            <div
                                :class="activeConversation == conv.id ? 'bg-red-50 border-red-200 text-red-700' : 'hover:bg-gray-100 text-gray-700 border-transparent'"
                                class="group flex w-full items-center rounded-lg border text-sm font-medium transition-colors">
                                <button type="button" @click="selectConversation(conv.id)"
                                    class="min-w-0 flex-1 px-3 py-2.5 text-left">
                                    <span class="block truncate" x-text="conv.title"></span>
                                    <span class="mt-0.5 block truncate font-mono text-[10px] font-normal opacity-60"
                                        x-text="conv.model || defaultModel"></span>
                                </button>
                                <button type="button" @click="deleteConversation(conv.id)"
                                    class="mr-2 rounded-md p-1 text-gray-400 opacity-100 transition-colors hover:bg-white hover:text-red-500 md:opacity-0 md:group-hover:opacity-100"
                                    title="Hapus percakapan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <div x-show="conversations.length === 0" class="text-center py-8 text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-xs font-semibold">Belum ada percakapan</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="flex-1 flex flex-col min-w-0">
                    <div class="flex items-center gap-2 border-b border-gray-200 bg-gray-50 p-3 md:hidden">
                        <select :value="activeConversation ?? ''" @change="selectConversation(Number($event.target.value))"
                            class="min-w-0 flex-1 rounded-lg border-gray-300 py-2 text-sm focus:border-red-500 focus:ring-red-500">
                            <option value="" disabled>Daftar percakapan</option>
                            <template x-for="conv in conversations" :key="conv.id">
                                <option :value="conv.id" x-text="conv.title"></option>
                            </template>
                        </select>
                        <button type="button" @click="newConversation()"
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-red-600 text-white hover:bg-red-500"
                            title="Chat baru">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-col gap-2 border-b border-gray-200 bg-white px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-700">Model AI</p>
                            <p class="text-[10px] text-gray-400">Model disimpan untuk setiap percakapan.</p>
                        </div>
                        <div class="relative w-full sm:w-72">
                            <input type="text" list="stella-ai-model-options"
                                x-model="selectedModel"
                                @change="changeModel()"
                                :disabled="loading"
                                placeholder="Cari atau pilih model"
                                class="block w-full rounded-lg border-gray-300 py-2 pl-3 pr-9 font-mono text-xs shadow-sm focus:border-red-500 focus:ring-red-500 disabled:bg-gray-100">
                            <svg class="pointer-events-none absolute right-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 9 4-4 4 4m0 6-4 4-4-4" />
                            </svg>
                            <datalist id="stella-ai-model-options">
                                @foreach($availableModels as $model)
                                    <option value="{{ $model }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4" id="chat-messages" x-ref="chatMessages">
                        <!-- Welcome screen -->
                        <div x-show="messages.length === 0 && !loading" class="h-full flex items-center justify-center">
                            <div class="text-center max-w-md">
                                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-red-100">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-900">Halo! Saya Stella AI</h3>
                                <p class="text-sm text-gray-500 mt-2">Asisten cerdas SMK Telkom. Saya bisa membantu menjawab pertanyaan, dan juga bisa generate gambar!</p>
                                <div class="mt-6 flex flex-wrap gap-2 justify-center">
                                    <button @click="sendQuickPrompt('Jelaskan tentang jurusan RPL')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-colors">Jelaskan jurusan RPL</button>
                                    <button @click="sendQuickPrompt('Buatkan puisi tentang sekolah')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-colors">Buat puisi sekolah</button>
                                    <button x-show="imageEnabled" @click="sendImagePrompt('Gambar maskot sekolah SMK futuristik')" class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 rounded-lg text-xs font-semibold text-purple-600 transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Generate gambar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <template x-for="msg in messages" :key="msg.id">
                            <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="msg.role === 'user' ? 'bg-red-600 text-white rounded-2xl rounded-br-md max-w-[75%]' : 'bg-gray-100 text-gray-800 rounded-2xl rounded-bl-md max-w-[85%]'" class="px-4 py-3 shadow-sm">
                                    <div x-show="msg.type === 'image_request'" class="flex items-center gap-1 mb-1">
                                        <svg class="w-3.5 h-3.5" :class="msg.role === 'user' ? 'text-red-200' : 'text-purple-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider" :class="msg.role === 'user' ? 'text-red-200' : 'text-purple-500'">Generate Gambar</span>
                                    </div>
                                    <p class="text-sm leading-relaxed whitespace-pre-wrap" x-text="msg.content"></p>
                                    <template x-if="msg.image_path">
                                        <div class="mt-3">
                                            <img :src="msg.image_path.startsWith('http') ? msg.image_path : '/storage/' + msg.image_path" alt="Generated image" class="rounded-xl max-w-full shadow-md cursor-pointer hover:opacity-90 transition-opacity" @click="window.open(msg.image_path.startsWith('http') ? msg.image_path : '/storage/' + msg.image_path, '_blank')">
                                        </div>
                                    </template>
                                    <p class="text-[10px] mt-1 opacity-60" x-text="new Date(msg.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})"></p>
                                </div>
                            </div>
                        </template>

                        <!-- Loading -->
                        <div x-show="loading" class="flex justify-start">
                            <div class="bg-gray-100 rounded-2xl rounded-bl-md px-4 py-3 shadow-sm">
                                <div class="flex items-center gap-2">
                                    <div class="flex gap-1">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 font-medium" x-text="isImageMode ? 'Membuat gambar...' : 'Stella sedang mengetik...'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="border-t border-gray-200 p-4 bg-white">
                        <div x-show="imageEnabled" class="flex items-center gap-2 mb-2">
                            <button @click="isImageMode = !isImageMode"
                                :class="isImageMode ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200'"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span x-text="isImageMode ? 'Mode Gambar: ON' : 'Mode Gambar'"></span>
                            </button>
                            <span x-show="isImageMode" class="text-[10px] text-purple-500 font-semibold">Deskripsikan gambar yang ingin dibuat</span>
                        </div>
                        <form @submit.prevent="sendMessage()" class="flex items-end gap-3">
                            <div class="flex-1 relative">
                                <textarea x-model="messageInput" @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                                    :placeholder="isImageMode ? 'Deskripsikan gambar yang ingin dibuat...' : 'Ketik pesan...'"
                                    rows="1"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm resize-none pr-4 py-3"
                                    :class="isImageMode ? 'border-purple-300 focus:border-purple-500 focus:ring-purple-500' : ''"
                                    style="max-height: 120px; overflow-y: auto;"
                                    @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px'"
                                    :disabled="loading"
                                ></textarea>
                            </div>
                            <button type="submit" :disabled="loading || !messageInput.trim()"
                                :class="isImageMode ? 'bg-purple-600 hover:bg-purple-500 shadow-purple-100' : 'bg-red-600 hover:bg-red-500 shadow-red-100'"
                                class="flex-shrink-0 p-3 text-white rounded-xl transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function stellaAiChat() {
            return {
                conversations: @json($conversations),
                activeConversation: null,
                messages: [],
                messageInput: '',
                loading: false,
                isImageMode: false,
                imageEnabled: @json($imageEnabled),
                availableModels: @json($availableModels),
                defaultModel: @json($defaultModel),
                selectedModel: @json($defaultModel),

                init() {
                    if (this.conversations.length > 0) {
                        this.selectConversation(this.conversations[0].id);
                    }
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const el = this.$refs.chatMessages;
                        if (el) el.scrollTop = el.scrollHeight;
                    });
                },

                async newConversation() {
                    try {
                        const res = await fetch("{{ route('stella-ai.conversations.create') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                model: this.validSelectedModel(),
                            }),
                        });
                        if (!res.ok) {
                            throw new Error('Gagal membuat percakapan.');
                        }

                        const data = await res.json();
                        this.conversations.unshift(data);
                        await this.selectConversation(data.id);
                        return true;
                    } catch (e) {
                        this.showError('Percakapan baru tidak dapat dibuat.');
                        return false;
                    }
                },

                async selectConversation(id) {
                    this.activeConversation = id;
                    const conversation = this.conversations.find(item => item.id === id);
                    this.selectedModel = conversation?.model || this.defaultModel;
                    this.messages = [];
                    try {
                        const res = await fetch(`/stella-ai/conversations/${id}/messages`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!res.ok) {
                            throw new Error('Gagal memuat percakapan.');
                        }

                        this.messages = await res.json();
                        this.scrollToBottom();
                    } catch (e) {
                        this.showError('Riwayat percakapan tidak dapat dimuat.');
                    }
                },

                validSelectedModel() {
                    return this.availableModels.includes(this.selectedModel)
                        ? this.selectedModel
                        : this.defaultModel;
                },

                async changeModel() {
                    const model = this.validSelectedModel();
                    this.selectedModel = model;

                    if (!this.activeConversation) return;

                    try {
                        const response = await fetch(`/stella-ai/conversations/${this.activeConversation}/model`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ model }),
                        });

                        if (!response.ok) {
                            throw new Error('Gagal mengganti model.');
                        }

                        const conversation = this.conversations.find(item => item.id === this.activeConversation);
                        if (conversation) {
                            conversation.model = model;
                        }
                    } catch (error) {
                        this.showError('Model percakapan tidak dapat diubah.');
                    }
                },

                sendQuickPrompt(text) {
                    this.isImageMode = false;
                    this.messageInput = text;
                    this.sendMessage();
                },

                sendImagePrompt(text) {
                    this.isImageMode = true;
                    this.messageInput = text;
                    this.sendMessage();
                },

                async sendMessage() {
                    const msg = this.messageInput.trim();
                    if (!msg || this.loading) return;

                    if (!this.activeConversation) {
                        const created = await this.newConversation();
                        if (!created) return;
                    }

                    const type = this.isImageMode ? 'image_request' : 'text';

                    this.messages.push({
                        id: 'temp-' + Date.now(),
                        role: 'user',
                        content: msg,
                        type: type,
                        image_path: null,
                        created_at: new Date().toISOString(),
                    });

                    this.messageInput = '';
                    this.loading = true;
                    this.scrollToBottom();

                    try {
                        const res = await fetch("{{ route('stella-ai.send') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                conversation_id: this.activeConversation,
                                message: msg,
                                type: type,
                            })
                        });

                        const data = await res.json();
                        if (data.message) {
                            this.messages.push(typeof data.message === 'string' ? {
                                id: 'err-' + Date.now(),
                                role: 'assistant',
                                content: data.message,
                                type: 'text',
                                image_path: null,
                                created_at: new Date().toISOString(),
                            } : data.message);

                            // Update conversation title
                            const conv = this.conversations.find(c => c.id === this.activeConversation);
                            if (conv && conv.title === 'Percakapan Baru') {
                                conv.title = msg.substring(0, 50);
                            }
                        } else if (!res.ok) {
                            throw new Error('Permintaan gagal diproses.');
                        }
                    } catch (e) {
                        this.showError('Maaf, terjadi kesalahan koneksi.');
                    } finally {
                        this.loading = false;
                        this.scrollToBottom();
                    }
                },

                async deleteConversation(id) {
                    if (!confirm('Hapus percakapan ini?')) return;
                    try {
                        const response = await fetch(`/stella-ai/conversations/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            }
                        });
                        if (!response.ok) {
                            throw new Error('Gagal menghapus percakapan.');
                        }

                        if (this.activeConversation === id) {
                            this.activeConversation = null;
                            this.messages = [];
                        }

                        this.conversations = this.conversations.filter(c => c.id !== id);
                    } catch (e) {
                        this.showError('Percakapan tidak dapat dihapus.');
                    }
                },

                showError(message) {
                    this.messages.push({
                        id: 'err-' + Date.now(),
                        role: 'assistant',
                        content: message,
                        type: 'text',
                        image_path: null,
                        created_at: new Date().toISOString(),
                    });
                    this.scrollToBottom();
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
