<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Nota Dinas Elektronik</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-black text-gray-800">Nota Dinas</h3>
                    <p class="text-gray-500">Kirim dan terima nota dinas secara elektronik.</p>
                </div>
                <a href="{{ route('shared.nde.create') }}" 
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition-all shadow-lg shadow-red-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Nota Dinas
                </a>
            </div>

            <div x-data="{ activeTab: 'masuk' }" class="space-y-6">
                {{-- Tabs --}}
                <div class="flex border-b border-gray-200">
                    <button @click="activeTab = 'masuk'" 
                        :class="activeTab === 'masuk' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-3 border-b-2 font-bold text-sm transition-all">
                        Nota Dinas Masuk
                    </button>
                    <button @click="activeTab = 'keluar'" 
                        :class="activeTab === 'keluar' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-3 border-b-2 font-bold text-sm transition-all">
                        Nota Dinas Keluar
                    </button>
                </div>

                {{-- Tab Content: Masuk --}}
                <div x-show="activeTab === 'masuk'" class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden animate-fadeIn">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-widest">
                                <tr>
                                    <th class="px-6 py-4">Pengirim</th>
                                    <th class="px-6 py-4">Nomor & Perihal</th>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($ndeMasuk as $nde)
                                    <tr class="hover:bg-gray-50 transition-colors {{ !$nde->pivot->is_read ? 'bg-blue-50/30' : '' }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                                                    {{ substr($nde->pengirim->name, 0, 1) }}
                                                </div>
                                                <span class="font-bold text-gray-900">{{ $nde->pengirim->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('shared.nde.show', $nde->id) }}" class="block group">
                                                <span class="text-xs text-gray-400 block">{{ $nde->nomor_nota }}</span>
                                                <span class="font-bold text-gray-800 group-hover:text-red-600 transition-colors">{{ $nde->perihal }}</span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($nde->tanggal)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(!$nde->pivot->is_read)
                                                <span class="px-2 py-1 bg-red-100 text-red-700 text-[10px] font-bold uppercase rounded-full">Baru</span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold uppercase rounded-full">Dibaca</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Belum ada nota dinas masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        {{ $ndeMasuk->links() }}
                    </div>
                </div>

                {{-- Tab Content: Keluar --}}
                <div x-show="activeTab === 'keluar'" class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden animate-fadeIn">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-widest">
                                <tr>
                                    <th class="px-6 py-4">Penerima</th>
                                    <th class="px-6 py-4">Nomor & Perihal</th>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($ndeKeluar as $nde)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-bold text-gray-900">{{ $nde->penerimas->count() }} Penerima</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('shared.nde.show', $nde->id) }}" class="block group">
                                                <span class="text-xs text-gray-400 block">{{ $nde->nomor_nota }}</span>
                                                <span class="font-bold text-gray-800 group-hover:text-red-600 transition-colors">{{ $nde->perihal }}</span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($nde->tanggal)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold uppercase rounded-full">Dikirim</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Belum ada nota dinas keluar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        {{ $ndeKeluar->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
