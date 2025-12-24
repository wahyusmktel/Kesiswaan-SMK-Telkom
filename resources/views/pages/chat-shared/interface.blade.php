<x-app-layout>
    <div class="py-6 h-[calc(100vh-64px)] overflow-hidden">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-full">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl h-full flex" 
                x-data="chatSystem()" 
                @keydown.window.escape="activeRoom = null">
                
                <!-- Sidebar Rooms -->
                <div class="w-full md:w-80 border-r border-gray-100 flex flex-col overflow-hidden" 
                    :class="activeRoom ? 'hidden md:flex' : 'flex'">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-xl font-black text-gray-900 leading-none">Pesan Konsultasi</h3>
                        <p class="text-xs text-gray-400 mt-2 font-medium">Bicarakan masalahmu secara privat</p>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        @role('Siswa|siswa')
                        <div class="p-4 bg-red-50/50 border-b border-red-50">
                            <label class="block text-[10px] font-black text-red-600 uppercase tracking-widest mb-2">Pilih Guru BK</label>
                            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                                @foreach($gurusBK as $guru)
                                <a href="{{ route('siswa.chat.start', $guru->id) }}" class="flex-shrink-0 group text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-white border-2 border-red-100 flex items-center justify-center text-red-600 font-bold group-hover:bg-red-600 group-hover:text-white transition-all shadow-sm">
                                        {{ substr($guru->name, 0, 1) }}
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-600 mt-1 block truncate w-12">{{ explode(' ', $guru->name)[0] }}</span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endrole

                        <div class="divide-y divide-gray-50">
                            @forelse($rooms as $room)
                            <button @click="openRoom({{ $room->id }})" 
                                class="w-full p-6 text-left hover:bg-gray-50 transition-colors flex items-center gap-4 group border-r-4 transition-all"
                                :class="activeRoomId == {{ $room->id }} ? 'bg-red-50/50 border-red-600' : 'border-transparent'">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center font-black text-gray-400 group-hover:scale-110 transition-transform shadow-sm">
                                    @if(Auth::user()->hasAnyRole(['Guru BK', 'guru bk']))
                                        {{ substr($room->siswa->name, 0, 1) }}
                                    @else
                                        {{ substr($room->guruBK->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline mb-1">
                                        <h4 class="text-sm font-bold text-gray-900 truncate">
                                            @if(Auth::user()->hasAnyRole(['Guru BK', 'guru bk']))
                                                {{ $room->siswa->name }}
                                            @else
                                                {{ $room->guruBK->name }}
                                            @endif
                                        </h4>
                                        <span class="text-[10px] text-gray-400 font-bold">
                                            {{ $room->last_message_at ? \Carbon\Carbon::parse($room->last_message_at)->format('H:i') : '' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate font-medium">
                                        {{ $room->messages->first()?->message ?? ($room->messages->first()?->type != 'text' ? '[Lampiran]' : 'Belum ada pesan') }}
                                    </p>
                                </div>
                            </button>
                            @empty
                            <div class="p-10 text-center">
                                <p class="text-xs font-bold text-gray-400">Belum ada percakapan</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="flex-1 flex flex-col bg-gray-50/30 overflow-hidden relative" :class="activeRoom ? 'flex' : 'hidden md:flex'">
                    <template x-if="activeRoom">
                        <div class="flex flex-col h-full">
                            <!-- Header -->
                            <div class="p-6 bg-white border-b border-gray-100 flex items-center justify-between shadow-sm">
                                <div class="flex items-center gap-4">
                                    <button @click="activeRoom = null; activeRoomId = null" class="md:hidden p-2 -ml-2 text-gray-400 hover:bg-gray-50 rounded-xl">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center text-white font-black text-sm shadow-lg shadow-red-100">
                                        <span x-text="getCounterpartName()[0]"></span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-gray-900" x-text="getCounterpartName()"></h4>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Online</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages -->
                            <div id="chatMessages" class="flex-1 overflow-y-auto p-6 space-y-6 scrollbar-thin scrollbar-thumb-gray-200">
                                <template x-for="msg in messages" :key="msg.id">
                                    <div class="flex flex-col" :class="msg.sender_id == currentUserId ? 'items-end' : 'items-start'">
                                        <div class="max-w-[85%] md:max-w-[70%] rounded-3xl overflow-hidden shadow-sm"
                                            :class="msg.sender_id == currentUserId ? 'bg-red-600 text-white rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none border border-gray-100'">
                                            
                                            <!-- Image Message -->
                                            <template x-if="msg.type == 'image'">
                                                <div class="p-1">
                                                    <img :src="msg.file_url" class="rounded-2xl max-h-64 w-full object-cover cursor-pointer hover:opacity-90 transition-opacity" 
                                                        @click="showLightbox = true; lightboxUrl = msg.file_url">
                                                </div>
                                            </template>

                                            <!-- Video Message -->
                                            <template x-if="msg.type == 'video'">
                                                <div class="p-1">
                                                    <video :src="msg.file_url" controls class="rounded-2xl max-h-64 w-full bg-black"></video>
                                                </div>
                                            </template>

                                            <div class="px-6 py-4" x-show="msg.message">
                                                <p class="text-sm font-medium leading-relaxed" x-text="msg.message"></p>
                                            </div>
                                        </div>
                                        <span class="text-[9px] font-black text-gray-400 mt-2 uppercase tracking-tighter" x-text="formatTime(msg.created_at)"></span>
                                    </div>
                                </template>
                                <div x-ref="bottom"></div>
                            </div>

                            <!-- Message Input & Preview -->
                            <div class="p-6 bg-white border-t border-gray-100" x-data="{ showEmoji: false }">
                                <!-- Image Preview Before Upload -->
                                <template x-if="previewUrl">
                                    <div class="mb-4 relative inline-block">
                                        <div class="relative rounded-2xl overflow-hidden border-4 border-gray-50 shadow-lg">
                                            <img :src="previewUrl" class="h-32 w-auto object-cover">
                                            <button @click="removeSelectedFile()" type="button" 
                                                class="absolute top-2 right-2 w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-700 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <form @submit.prevent="sendMessage()" class="flex items-center gap-2 md:gap-4 relative" enctype="multipart/form-data">
                                    <!-- Attachment Button -->
                                    <div class="relative">
                                        <input type="file" id="chatFile" class="hidden" @change="handleFileSelected($event)">
                                        <button type="button" onclick="document.getElementById('chatFile').click()"
                                            class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-gray-100 transition-all flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        </button>
                                        <template x-if="selectedFile">
                                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-600 rounded-full border-2 border-white"></div>
                                        </template>
                                    </div>

                                    <!-- Emoji Toggle -->
                                    <button type="button" @click="showEmoji = !showEmoji"
                                        class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-gray-100 transition-all flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>

                                    <!-- Emoji Picker (Native Simplicity) -->
                                    <div x-show="showEmoji" @click.away="showEmoji = false" 
                                        class="absolute bottom-full mb-4 left-0 p-4 bg-white shadow-2xl rounded-[30px] border border-gray-100 grid grid-cols-6 gap-2 z-50 animate-in slide-in-from-bottom-2">
                                        <template x-for="emoji in ['😀','😂','😍','😭','🙏','👍','🔥','❤️','🙌','✅','❌','⚠️']">
                                            <button type="button" @click="newMessage += emoji; showEmoji = false" class="text-2xl hover:scale-125 transition-transform p-2" x-text="emoji"></button>
                                        </template>
                                    </div>

                                    <input type="text" x-model="newMessage" placeholder="Tulis pesan kamu disini..." 
                                        class="flex-1 bg-gray-50 border-0 rounded-2xl focus:ring-2 focus:ring-red-500 px-6 py-4 text-sm font-medium">
                                    
                                    <button type="submit" :disabled="!newMessage.trim() && !selectedFile"
                                        class="w-14 h-14 rounded-2xl bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition-all shadow-xl shadow-red-100 disabled:opacity-50 disabled:shadow-none active:scale-95">
                                        <svg class="w-6 h-6 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </template>

                    <template x-if="!activeRoom">
                        <div class="h-full flex flex-col items-center justify-center text-center p-12">
                            <div class="w-32 h-32 bg-white rounded-[40px] shadow-sm flex items-center justify-center text-gray-200 mb-8 border border-gray-50">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <h4 class="text-xl font-black text-gray-900 leading-tight">Mulai Konsultasi</h4>
                            <p class="text-sm text-gray-400 mt-2 font-medium max-w-sm">Pilih salah satu percakapan di samping untuk mulai berkonsultasi dengan Guru BK.</p>
                        </div>
                    </template>
                </div>

                <!-- Lightbox (Pop up gambar) -->
                <div x-show="showLightbox" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4"
                    x-cloak>
                    <button @click="showLightbox = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <img :src="lightboxUrl" class="max-w-full max-h-full rounded-2xl shadow-2xl object-contain animate-in zoom-in-95 duration-300">
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function chatSystem() {
            return {
                activeRoom: null,
                activeRoomId: null,
                messages: [],
                newMessage: '',
                currentUserId: {{ Auth::id() }},
                currentUserRole: '{{ Auth::user()->roles->first()->name ?? "Siswa" }}',
                polling: null,
                selectedFile: null,
                previewUrl: null,
                showLightbox: false,
                lightboxUrl: '',

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const roomId = urlParams.get('room_id');
                    if (roomId) this.openRoom(parseInt(roomId));
                },

                async openRoom(roomId) {
                    this.activeRoomId = parseInt(roomId);
                    await this.fetchMessages(this.activeRoomId);
                    this.scrollToBottom();
                    
                    if (this.polling) clearInterval(this.polling);
                    this.polling = setInterval(() => this.fetchMessages(this.activeRoomId), 3000);
                },

                getCounterpartName() {
                    if (!this.activeRoom) return '';
                    if (this.currentUserRole.toLowerCase() === 'siswa') {
                        const guru = this.activeRoom.guru_b_k || this.activeRoom.guru_bk || this.activeRoom.gurubk;
                        return guru ? guru.name : 'Guru BK';
                    }
                    return this.activeRoom.siswa ? this.activeRoom.siswa.name : 'Siswa';
                },

                handleFileSelected(event) {
                    this.selectedFile = event.target.files[0];
                    if (this.selectedFile && this.selectedFile.type.startsWith('image/')) {
                        this.previewUrl = URL.createObjectURL(this.selectedFile);
                    } else {
                        this.previewUrl = null;
                    }
                },

                removeSelectedFile() {
                    this.selectedFile = null;
                    this.previewUrl = null;
                    document.getElementById('chatFile').value = '';
                },

                async fetchMessages(roomId) {
                    try {
                        const response = await fetch(`/api/chat/rooms/${roomId}`);
                        const data = await response.json();
                        this.activeRoom = data.room;
                        this.messages = data.messages;
                    } catch (error) {
                        console.error('Failed to fetch messages:', error);
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() && !this.selectedFile) return;

                    const formData = new FormData();
                    if (this.newMessage.trim()) formData.append('message', this.newMessage);
                    if (this.selectedFile) formData.append('file', this.selectedFile);

                    try {
                        const response = await fetch(`/api/chat/rooms/${this.activeRoomId}/send`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });
                        
                        const msg = await response.json();
                        this.messages.push(msg);
                        this.newMessage = '';
                        this.removeSelectedFile();
                        this.scrollToBottom();
                    } catch (error) {
                        console.error('Failed to send message:', error);
                    }
                },

                scrollToBottom() {
                    setTimeout(() => {
                        this.$refs.bottom?.scrollIntoView({ behavior: 'smooth' });
                    }, 100);
                },

                formatTime(timestamp) {
                    const date = new Date(timestamp);
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
