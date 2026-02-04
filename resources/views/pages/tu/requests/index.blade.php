<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center shadow-lg shadow-amber-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-clipboard-list">
                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                    <path d="M12 11h4" />
                    <path d="M12 16h4" />
                    <path d="M8 11h.01" />
                    <path d="M8 16h.01" />
                </svg>
            </div>
            <h2 class="font-black text-2xl text-gray-900 tracking-tight">Antrean Permohonan Nomor</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white rounded-[2.5rem] shadow-soft border border-gray-50 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-900">Daftar Permohonan Nomor Surat</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 tracking-widest">
                            <tr>
                                <th class="px-8 py-4">Status</th>
                                <th class="px-8 py-4">Pemohon</th>
                                <th class="px-8 py-4">Perihal</th>
                                <th class="px-8 py-4">Klasifikasi</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($requests as $req)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-5">
                                        @if($req->status == 'pending')
                                            <span
                                                class="px-3 py-1 rounded-full bg-amber-100 text-[10px] font-black text-amber-700 uppercase">Menunggu</span>
                                        @elseif($req->status == 'approved')
                                            <span
                                                class="px-3 py-1 rounded-full bg-emerald-100 text-[10px] font-black text-emerald-700 uppercase">Disetujui</span>
                                        @else
                                            <span
                                                class="px-3 py-1 rounded-full bg-rose-100 text-[10px] font-black text-rose-700 uppercase">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-black text-xs">
                                                {{ substr($req->user->name, 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-900">{{ $req->user->name }}</span>
                                                <span
                                                    class="text-[10px] text-gray-400 font-medium">{{ $req->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <p class="text-sm font-medium text-gray-900">{{ $req->subject }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($req->type === 'upload')
                                                    <a href="{{ route('tu.requests.download', $req) }}"
                                                        class="flex items-center gap-1 text-[10px] font-black text-indigo-500 uppercase hover:text-indigo-700 transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                            class="lucide lucide-download-cloud">
                                                            <path
                                                                d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242" />
                                                            <path d="M12 12v9" />
                                                            <path d="m8 17 4 4 4-4" />
                                                        </svg>
                                                        Lihat PDF
                                                    </a>
                                                @else
                                                    <a href="{{ route('tu.requests.print', $req) }}" target="_blank"
                                                        class="flex items-center gap-1 text-[10px] font-black text-emerald-500 uppercase hover:text-emerald-700 transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                            class="lucide lucide-eye">
                                                            <path
                                                                d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z" />
                                                            <circle cx="12" cy="12" r="3" />
                                                        </svg>
                                                        Pratinjau Surat
                                                    </a>
                                                @endif
                                                @if($req->outgoingLetter)
                                                    <span class="text-[10px] font-black text-gray-300">|</span>
                                                    <p class="text-[10px] font-black text-indigo-600 tracking-tight">
                                                        {{ $req->outgoingLetter->full_number }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span
                                            class="px-3 py-1 rounded-lg bg-gray-100 text-[10px] font-black text-gray-600 uppercase">{{ $req->letterCode->code }}</span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        @if($req->status == 'pending')
                                            <div class="flex items-center justify-end gap-2" x-data="{ notes: '' }">
                                                <form action="{{ route('tu.requests.approve', $req) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="notes" :value="notes">
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black transition-all">Setujui</button>
                                                </form>
                                                <form action="{{ route('tu.requests.reject', $req) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="notes" :value="notes">
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-gray-100 hover:bg-rose-50 hover:text-rose-600 text-gray-400 rounded-xl text-xs font-black transition-all">Tolak</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-[10px] font-black text-gray-300 uppercase italic">Sudah
                                                Diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-8 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                                        Belum ada permohonan nomor surat</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($requests->hasPages())
                    <div class="p-8 border-t border-gray-50 bg-gray-50/30">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>