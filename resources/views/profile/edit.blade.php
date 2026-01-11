<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full" x-data="{ activeTab: 'profile' }">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- Page Header with Subtle Background --}}
            <div class="relative mb-8">
                <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 rounded-3xl p-8 border border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-black text-gray-900 mb-1">Pengaturan Akun</h1>
                            <p class="text-gray-500 text-sm">Kelola informasi profil, keamanan, dan preferensi akun Anda.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm text-sm font-medium text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="10"/></svg>
                                Terakhir login: {{ now()->translatedFormat('d M Y, H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                @if (session('status') === 'avatar-updated')
                <div class="lg:col-span-12" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <p class="font-bold text-sm">Foto profil berhasil diperbarui!</p>
                    </div>
                </div>
                @endif

                {{-- Profile Card (Sidebar) --}}
                <div class="lg:col-span-4 xl:col-span-3">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden sticky top-6">
                        
                        {{-- Card Header with Gradient --}}
                        <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 h-24 relative">
                            <div class="absolute inset-0 opacity-20">
                                <div class="absolute top-0 right-0 w-20 h-20 rounded-full bg-white/30 blur-xl"></div>
                                <div class="absolute bottom-0 left-0 w-16 h-16 rounded-full bg-white/20 blur-lg"></div>
                            </div>
                        </div>
                        
                        {{-- Avatar Section --}}
                        <div class="relative pt-12 pb-6 px-6 text-center" x-data="{
                                imagePreview: '{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366f1&color=fff&size=128&bold=true&font-size=0.4' }}',
                                updateAvatar(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        this.imagePreview = URL.createObjectURL(file);
                                        document.getElementById('avatar-upload-form').submit();
                                    }
                                }
                            }">
                            
                            {{-- Hidden Form for Avatar Upload --}}
                            <form id="avatar-upload-form" action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="hidden">
                                @csrf
                                @method('PATCH')
                                <input type="file" name="avatar" id="avatar-input" accept="image/*" @change="updateAvatar($event)">
                            </form>

                            {{-- Avatar Image with Overlap --}}
                            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                                <div class="relative group cursor-pointer" @click="document.getElementById('avatar-input').click()">
                                    {{-- Glow Effect --}}
                                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-full blur opacity-75 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    
                                    {{-- Avatar Container --}}
                                    <div class="relative w-24 h-24 rounded-full border-4 border-white shadow-xl overflow-hidden bg-white group-hover:scale-105 transition-transform duration-300">
                                        <img :src="imagePreview" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                        
                                        {{-- Overlay On Hover --}}
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                    </div>
                                    
                                    {{-- Online Status Indicator --}}
                                    <div class="absolute bottom-1 right-1 w-5 h-5 bg-emerald-500 rounded-full border-3 border-white shadow-lg z-10"></div>
                                </div>
                            </div>

                            {{-- User Info --}}
                            <div class="mt-8">
                                <h3 class="text-xl font-black text-gray-900 tracking-tight mb-1">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-500 font-medium mb-4">{{ $user->email }}</p>

                                {{-- Role Badges --}}
                                <div class="flex flex-wrap justify-center gap-2 mb-2">
                                    @if (method_exists($user, 'getRoleNames') && $user->getRoleNames()->isNotEmpty())
                                        @foreach ($user->getRoleNames() as $role)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-xs font-bold uppercase tracking-wider shadow-md shadow-indigo-500/20">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                {{ $role }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 text-xs font-bold uppercase tracking-wider">User</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Stats Section --}}
                        <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-3 rounded-2xl bg-white shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-100 transition-all cursor-default">
                                    <div class="flex items-center justify-center gap-1.5 mb-1">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                        <span class="text-sm font-black text-gray-900">Aktif</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</span>
                                </div>
                                <div class="text-center p-3 rounded-2xl bg-white shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-100 transition-all cursor-default">
                                    <span class="block text-sm font-black text-gray-900">{{ $user->created_at->format('M Y') }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Bergabung</span>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Navigation --}}
                        <div class="border-t border-gray-100 px-4 py-4">
                            <nav class="space-y-1">
                                <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'text-gray-600 hover:bg-gray-50 border-transparent'" 
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all border">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Informasi Profil
                                </button>
                                <button @click="activeTab = 'password'" :class="activeTab === 'password' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'text-gray-600 hover:bg-gray-50 border-transparent'"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all border">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Keamanan
                                </button>
                                <button @click="activeTab = 'danger'" :class="activeTab === 'danger' ? 'bg-red-50 text-red-700 border-red-200' : 'text-gray-600 hover:bg-gray-50 border-transparent'"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all border">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Zona Berbahaya
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Main Content Area --}}
                <div class="lg:col-span-8 xl:col-span-9 space-y-6">

                    {{-- Profile Information Form --}}
                    <div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-2xl">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-black text-white">Informasi Profil</h2>
                                        <p class="text-sm text-indigo-100">Perbarui informasi profil akun dan alamat email Anda.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-8">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>

                    {{-- Password Form --}}
                    <div x-show="activeTab === 'password'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-2xl">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-black text-white">Ganti Password</h2>
                                        <p class="text-sm text-purple-100">Pastikan akun Anda menggunakan password yang panjang dan aman.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-8">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>

                    {{-- Danger Zone --}}
                    <div x-show="activeTab === 'danger'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white rounded-3xl shadow-xl border border-red-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-red-500 to-rose-600 px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-2xl">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-black text-white">Zona Berbahaya</h2>
                                        <p class="text-sm text-red-100">Tindakan ini tidak dapat dibatalkan. Semua data akan hilang permanen.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-8 bg-red-50/30">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @keyframes gradient-x {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .animate-gradient-x {
            background-size: 200% 200%;
            animation: gradient-x 15s ease infinite;
        }
    </style>
    @endpush
</x-app-layout>
