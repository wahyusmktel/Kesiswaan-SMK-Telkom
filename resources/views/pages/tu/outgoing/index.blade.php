<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-file-text">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                    <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                    <path d="M10 9H8" />
                    <path d="M16 13H8" />
                    <path d="M16 17H8" />
                </svg>
            </div>
            <h2 class="font-black text-2xl text-gray-900 tracking-tight">Manajemen Surat Keluar</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Form Card -->
            <div x-data="{ open: false }"
                class="bg-white rounded-[2.5rem] shadow-soft border border-gray-50 overflow-hidden">
                <button @click="open = !open"
                    class="w-full p-8 flex justify-between items-center bg-gray-50/50 hover:bg-gray-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-plus-circle">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M8 12h8" />
                                <path d="M12 8v8" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900">Terbitkan Nomor Surat Baru</h3>
                    </div>
                    <svg :class="open ? 'rotate-180' : ''" class="w-6 h-6 text-gray-400 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse>
                    <div class="p-8 border-t border-gray-100">
                        <form action="{{ route('tu.outgoing.store') }}" method="POST"
                            class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @csrf
                            <div class="space-y-2">
                                <label
                                    class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Klasifikasi
                                    Surat</label>
                                <select name="letter_code_id" required
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all">
                                    <option value="" disabled selected>Pilih Kode Klasifikasi...</option>
                                    @foreach($letterCodes->groupBy('unit') as $unit => $codes)
                                        <optgroup label="{{ $unit }}">
                                            @foreach($codes as $code)
                                                <option value="{{ $code->id }}">{{ $code->code }} - {{ $code->description }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Tanggal
                                    Surat</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all">
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Perihal /
                                    Keperluan</label>
                                <input type="text" name="subject" required
                                    placeholder="Contoh: Surat Tugas Pembina Pramuka"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Tujuan /
                                    Penerima</label>
                                <input type="text" name="recipient" placeholder="Contoh: Seluruh Guru"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all">
                            </div>
                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black transition-all shadow-lg shadow-indigo-100">Dapatkan
                                    Nomor Surat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-[2.5rem] shadow-soft border border-gray-50 overflow-hidden">
                <div class="p-8 border-b border-gray-50">
                    <h3 class="text-xl font-black text-gray-900">Riwayat Nomor Surat Terbit</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 tracking-widest">
                            <tr>
                                <th class="px-8 py-4">Nomor Surat</th>
                                <th class="px-8 py-4">Tgl. Surat</th>
                                <th class="px-8 py-4">Perihal</th>
                                <th class="px-8 py-4">Petugas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($letters as $letter)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-black text-indigo-700">{{ $letter->full_number }}</span>
                                            <span
                                                class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $letter->letterCode->description }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-sm font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse($letter->date)->translatedFormat('d M Y') }}</td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <p class="text-sm font-medium text-gray-900">{{ $letter->subject }}</p>
                                            <p class="text-[10px] font-bold text-gray-400">Kepada:
                                                {{ $letter->recipient ?? '-' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-xs font-bold text-gray-600">{{ $letter->user->name }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="px-8 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                                        Belum ada nomor surat yang terbit</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($letters->hasPages())
                    <div class="p-8 border-t border-gray-50 bg-gray-50/30">
                        {{ $letters->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>