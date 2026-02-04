<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-mail">
                    <rect width="20" height="16" x="2" y="4" rx="2" />
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
            </div>
            <h2 class="font-black text-2xl text-gray-900 tracking-tight">Manajemen Surat Masuk</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Form Card (Collapsed by default maybe?) -->
            <div x-data="{ open: false }"
                class="bg-white rounded-[2.5rem] shadow-soft border border-gray-50 overflow-hidden">
                <button @click="open = !open"
                    class="w-full p-8 flex justify-between items-center bg-gray-50/50 hover:bg-gray-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-plus-circle">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M8 12h8" />
                                <path d="M12 8v8" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900">Catat Surat Masuk Baru</h3>
                    </div>
                    <svg :class="open ? 'rotate-180' : ''" class="w-6 h-6 text-gray-400 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse>
                    <div class="p-8 border-t border-gray-100">
                        <form action="#" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @csrf
                            <div class="space-y-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Tanggal
                                    Terima</label>
                                <input type="date" name="date" required
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Instansi
                                    Pengirim</label>
                                <input type="text" name="sender" required
                                    placeholder="Contoh: Yayasan Pendidikan Telkom"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-all">
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Perihal /
                                    Keperluan</label>
                                <input type="text" name="subject" required
                                    placeholder="Contoh: Undangan Rapat Koordinasi"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Nomor
                                    Surat (Opsional)</label>
                                <input type="text" name="letter_number" placeholder="Nomor dari pengirim"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-all">
                            </div>
                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full md:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black transition-all shadow-lg shadow-blue-100">Simpan
                                    Surat Masuk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-[2.5rem] shadow-soft border border-gray-50 overflow-hidden">
                <div class="p-8 border-b border-gray-50">
                    <h3 class="text-xl font-black text-gray-900">Daftar Surat Masuk</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 tracking-widest">
                            <tr>
                                <th class="px-8 py-4">Tgl. Terima</th>
                                <th class="px-8 py-4">Instansi/Pengirim</th>
                                <th class="px-8 py-4">Perihal</th>
                                <th class="px-8 py-4">Nomor Surat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($letters as $letter)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-5 text-sm font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse($letter->date)->translatedFormat('d M Y') }}</td>
                                    <td class="px-8 py-5 text-sm font-medium text-gray-600">{{ $letter->sender }}</td>
                                    <td class="px-8 py-5 text-sm font-medium text-gray-900">{{ $letter->subject }}</td>
                                    <td class="px-8 py-5 text-sm font-mono text-gray-400 italic">
                                        {{ $letter->letter_number ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="px-8 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                                        Belum ada data surat masuk</td>
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