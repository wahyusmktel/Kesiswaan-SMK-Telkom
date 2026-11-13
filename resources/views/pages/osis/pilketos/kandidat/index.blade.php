<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Kandidat Calon Ketua OSIS</h2>
                <p class="text-xs text-gray-500 mt-1">Daftarkan pasangan calon ketua &amp; wakil ketua OSIS dari sumber data siswa</p>
            </div>
            <a href="{{ route('osis.pilketos.kandidat.create') }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Daftarkan Paslon Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($kandidats as $k)
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between space-y-6">
                        <div class="space-y-4">
                            <!-- Nomor Urut & Foto Paslon -->
                            <div class="flex items-start justify-between">
                                <span class="w-12 h-12 rounded-2xl bg-rose-600 text-white font-black text-2xl flex items-center justify-center shadow-md shadow-rose-500/20">
                                    0{{ $k->nomor_urut }}
                                </span>
                                <span class="text-xs font-bold text-gray-400 font-mono">
                                    {{ $k->votes_count }} Suara Masuk
                                </span>
                            </div>

                            <div class="flex justify-center">
                                <img src="{{ $k->foto_paslon_url }}" alt="{{ $k->nama_ketua }}" class="w-32 h-32 rounded-2xl object-cover border-2 border-gray-100 shadow-md">
                            </div>

                            <div class="text-center space-y-1">
                                <h3 class="font-black text-base text-gray-800">{{ $k->nama_ketua }}</h3>
                                @if($k->nama_wakil)
                                    <div class="text-xs text-gray-600 font-semibold">&amp; {{ $k->nama_wakil }}</div>
                                @endif
                                <div class="text-[11px] text-gray-400 font-mono">{{ $k->kelas_ketua }} @if($k->kelas_wakil) &bull; {{ $k->kelas_wakil }} @endif</div>
                            </div>

                            @if($k->tagline)
                                <div class="p-2.5 rounded-xl bg-gray-50 text-[11px] text-gray-600 italic text-center">
                                    "{{ $k->tagline }}"
                                </div>
                            @endif

                            <div class="space-y-2 text-xs">
                                <div>
                                    <span class="font-bold text-gray-700 block">Visi:</span>
                                    <p class="text-gray-500 line-clamp-2 mt-0.5">{{ $k->visi }}</p>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-700 block">Misi:</span>
                                    <p class="text-gray-500 line-clamp-2 mt-0.5 whitespace-pre-line">{{ $k->misi }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                            <a href="{{ route('osis.pilketos.kandidat.edit', $k) }}" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold text-center transition-colors">
                                Edit Paslon
                            </a>
                            <form action="{{ route('osis.pilketos.kandidat.destroy', $k) }}" method="POST" onsubmit="return confirm('Hapus kandidat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-bold transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 bg-white rounded-3xl border border-gray-100 p-8">
                        <p class="text-gray-400 text-sm">Belum ada kandidat calon ketua OSIS terdaftar.</p>
                        <a href="{{ route('osis.pilketos.kandidat.create') }}" class="mt-4 inline-block px-5 py-2.5 bg-rose-600 text-white rounded-xl text-xs font-bold">
                            + Daftarkan Paslon Pertama
                        </a>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
