<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Kantin Online') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Hero Section GoFood Style --}}
            <div class="bg-gradient-to-r from-red-600 to-orange-500 rounded-[32px] p-8 md:p-12 text-white relative overflow-hidden shadow-2xl shadow-red-500/30 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="absolute right-0 top-0 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none translate-x-1/3 -translate-y-1/2"></div>
                <div class="absolute left-0 bottom-0 w-64 h-64 bg-orange-500/40 rounded-full blur-2xl pointer-events-none -translate-x-1/2 translate-y-1/2"></div>
                
                <div class="relative z-10 md:w-1/2 space-y-4 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-sm font-black tracking-widest text-white shadow-sm border border-white/20">
                        <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        REKOMENDASI HARI INI
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black leading-tight tracking-tight">Pesan Makan<br>Tanpa Antri!</h1>
                    <p class="text-red-100 text-lg font-medium max-w-md">Pesan makanan favoritmu dari kantin sekolah, bayar pakai QRIS, dan tinggal ambil saat jam istirahat.</p>
                </div>
                
                <div class="relative z-10 md:w-1/2 flex justify-center md:justify-end">
                    <!-- Decorational Food Images (Mockup) -->
                    <div class="relative w-64 h-64 md:w-80 md:h-80">
                        <div class="absolute inset-0 bg-white/20 backdrop-blur-sm rounded-full animate-pulse"></div>
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Food" class="absolute inset-2 w-[calc(100%-16px)] h-[calc(100%-16px)] object-cover rounded-full shadow-2xl border-4 border-white/50">
                        
                        <div class="absolute -bottom-4 -left-4 bg-white text-gray-900 px-4 py-3 rounded-2xl shadow-xl border border-gray-100 font-bold flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="leading-tight">
                                <div class="text-xs text-gray-500">Status</div>
                                <div class="text-sm font-black text-green-600">Siap Diambil</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kantin Tersedia --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black text-gray-900">Pilih Kantin</h3>
                    <a href="#" class="text-red-600 hover:text-red-700 font-bold text-sm">Lihat Semua</a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse($kantins as $kantin)
                    <a href="{{ route('food-order.kantin', $kantin->id) }}" class="bg-white rounded-[24px] overflow-hidden shadow-lg border border-gray-100 hover:shadow-2xl transition-all group block">
                        <div class="h-32 bg-gray-200 relative overflow-hidden">
                            @if($kantin->banner_image)
                                <img src="{{ asset('storage/' . $kantin->banner_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-red-400 to-orange-400 flex items-center justify-center text-white">
                                    <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-3 left-3 flex items-center gap-2">
                                <span class="bg-emerald-500 text-white text-[10px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">Buka</span>
                            </div>
                        </div>
                        <div class="p-5">
                            <h4 class="text-lg font-black text-gray-900 mb-1 truncate">{{ $kantin->name ?? 'Kantin ' . $kantin->user->name }}</h4>
                            <p class="text-xs text-gray-500 font-medium line-clamp-2">{{ $kantin->description ?? 'Menyediakan berbagai makanan dan minuman lezat.' }}</p>
                        </div>
                    </a>
                    @empty
                    <div class="col-span-full py-12 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                        <p class="text-gray-500 font-bold">Saat ini belum ada kantin yang buka.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Rekomendasi Menu (Global) --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black text-gray-900">Mungkin Kamu Suka</h3>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @forelse($menus as $menu)
                    <a href="{{ route('food-order.kantin', $menu->user->kantinProfile->id ?? $menu->user_id) }}" class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all block group">
                        <div class="aspect-square bg-gray-100 relative overflow-hidden">
                            @if($menu->images && count($menu->images) > 0)
                                <img src="{{ asset('storage/' . $menu->images[0]) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <h4 class="text-sm font-bold text-gray-900 truncate mb-1">{{ $menu->name }}</h4>
                            <p class="text-xs text-gray-500 truncate mb-2">{{ $menu->user->kantinProfile->name ?? $menu->user->name }}</p>
                            <p class="text-sm font-black text-orange-500">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        </div>
                    </a>
                    @empty
                    <div class="col-span-full py-8 text-center text-gray-500 text-sm">Belum ada menu yang tersedia.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
