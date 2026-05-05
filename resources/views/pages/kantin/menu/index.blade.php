<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Katalog Makanan & Minuman') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            @endif

            <div class="flex justify-between items-center bg-white p-6 rounded-[24px] shadow-lg border border-gray-100">
                <div>
                    <h3 class="text-xl font-black text-gray-900">Menu Kantin</h3>
                    <p class="text-gray-500 text-sm font-medium">Kelola daftar menu makanan, minuman, dan cemilan yang tersedia.</p>
                </div>
                <a href="{{ route('kantin.menu.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-orange-500/30 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Menu
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($menus as $menu)
                <div class="bg-white rounded-[24px] overflow-hidden shadow-lg border border-gray-100 group flex flex-col">
                    <div class="relative h-48 bg-gray-100 overflow-hidden">
                        @if($menu->images && count($menu->images) > 0)
                            <img src="{{ asset('storage/' . $menu->images[0]) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @if(count($menu->images) > 1)
                                <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-white px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ count($menu->images) }} Foto
                                </div>
                            @endif
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        
                        <div class="absolute top-3 left-3 flex gap-2">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-900 rounded-lg text-xs font-black uppercase tracking-wider shadow-sm">
                                {{ $menu->category }}
                            </span>
                            @if($menu->is_available)
                                <span class="px-3 py-1 bg-emerald-500 text-white rounded-lg text-xs font-black uppercase tracking-wider shadow-sm shadow-emerald-500/30">
                                    Tersedia
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-500 text-white rounded-lg text-xs font-black uppercase tracking-wider shadow-sm shadow-red-500/30">
                                    Habis
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-5 flex-1 flex flex-col">
                        <h4 class="text-lg font-black text-gray-900 mb-1 leading-tight">{{ $menu->name }}</h4>
                        <p class="text-orange-500 font-black text-xl mb-3">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 flex-1 line-clamp-2 mb-4">{{ $menu->description ?: 'Tidak ada deskripsi.' }}</p>
                        
                        <div class="grid grid-cols-2 gap-3 mt-auto">
                            <a href="{{ route('kantin.menu.edit', $menu->id) }}" class="flex items-center justify-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold py-2.5 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Edit
                            </a>
                            <form action="{{ route('kantin.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');" class="block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold py-2.5 rounded-xl transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-16 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-orange-50 text-orange-400 mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">Belum Ada Menu</h3>
                    <p class="text-gray-500 font-medium">Anda belum menambahkan katalog makanan/minuman apapun.</p>
                </div>
                @endforelse
            </div>
            
            @if($menus->hasPages())
                <div class="mt-6">
                    {{ $menus->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
