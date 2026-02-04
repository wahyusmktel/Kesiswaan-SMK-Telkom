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
            <h2 class="font-black text-2xl text-gray-900 tracking-tight">Permohonan Nomor Surat</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2.5rem] shadow-soft border border-gray-50 p-8 space-y-6">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Ajukan Nomor Baru</h3>
                        <p class="text-xs font-bold text-gray-400 uppercase mt-1">Silakan isi detail surat Anda</p>
                    </div>

                    <form action="{{ route('correspondence.request.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ mode: 'upload' }">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Mode Pengajuan</label>
                            <div class="flex p-1 bg-gray-50 rounded-2xl border border-gray-100">
                                <button type="button" @click="mode = 'upload'" :class="mode === 'upload' ? 'bg-white shadow-soft text-indigo-600' : 'text-gray-400'" class="flex-1 py-2 text-xs font-black rounded-xl transition-all">UPLOAD PDF</button>
                                <button type="button" @click="mode = 'create'" :class="mode === 'create' ? 'bg-white shadow-soft text-indigo-600' : 'text-gray-400'" class="flex-1 py-2 text-xs font-black rounded-xl transition-all">BUAT DI APLIKASI</button>
                            </div>
                            <input type="hidden" name="type" :value="mode">
                        </div>

                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Klasifikasi
                                Surat</label>
                            <select name="letter_code_id" required
                                class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all text-sm font-medium">
                                <option value="" disabled selected>Pilih Klasifikasi...</option>
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
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Perihal /
                                Subject</label>
                            <input type="text" name="subject" required placeholder="Contoh: Surat Undangan Rapat"
                                class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all text-sm font-medium">
                        </div>

                        <template x-if="mode === 'upload'">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Lampiran PDF (Wajib)</label>
                                <div class="relative group">
                                    <input type="file" name="file" accept=".pdf" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="px-6 py-4 border-2 border-dashed border-gray-100 group-hover:border-indigo-200 rounded-2xl bg-gray-50/50 group-hover:bg-white transition-all flex items-center justify-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 group-hover:text-indigo-500 transition-colors"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                        <span class="text-xs font-black text-gray-400 group-hover:text-indigo-600 transition-colors uppercase tracking-widest">Pilih File PDF</span>
                                    </div>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 px-1 mt-1 italic">* Maksimal 2MB</p>
                            </div>
                        </template>

                        <template x-if="mode === 'create'">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Isi / Konten Surat</label>
                                <textarea name="content" rows="6" required placeholder="Tuliskan isi surat secara lengkap di sini..."
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all text-sm font-medium"></textarea>
                                <p class="text-[10px] font-bold text-gray-400 px-1 mt-1 italic">* Sistem akan otomatis menyertakan nomor surat dan kop sekolah saat dicetak.</p>
                            </div>
                        </template>

                        <button type="submit"
                            class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black transition-all shadow-lg shadow-indigo-100">Kirim
                            Permohonan</button>
                    </form>
                </div>
            </div>

            <!-- List Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] shadow-soft border border-gray-50 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Riwayat Pengajuan Anda</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 tracking-widest">
                                <tr>
                                    <th class="px-8 py-4">Status</th>
                                    <th class="px-8 py-4">Perihal</th>
                                    <th class="px-8 py-4">Nomor Surat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($myRequests as $req)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-8 py-5">
                                            @if($req->status == 'pending')
                                                <div class="flex items-center gap-2">
                                                    <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
                                                    <span
                                                        class="text-[10px] font-black text-amber-600 uppercase">Menunggu</span>
                                                </div>
                                            @elseif($req->status == 'approved')
                                                <div class="flex items-center gap-2">
                                                    <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                                    <span
                                                        class="text-[10px] font-black text-emerald-600 uppercase">Terbit</span>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-2">
                                                    <div class="w-2 h-2 rounded-full bg-rose-400"></div>
                                                    <span class="text-[10px] font-black text-rose-600 uppercase">Ditolak</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex flex-col">
                                                <p class="text-sm font-bold text-gray-900 line-clamp-1">{{ $req->subject }}
                                                </p>
                                                <p class="text-[10px] font-bold text-gray-400">{{ $req->letterCode->code }}
                                                    • {{ $req->created_at->format('d/m/Y') }}</p>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            @if($req->outgoingLetter)
                                                <div class="flex items-center gap-2">
                                                    <span class="px-3 py-1.5 rounded-xl bg-indigo-50 text-xs font-black text-indigo-700 border border-indigo-100">{{ $req->outgoingLetter->full_number }}</span>
                                                    
                                                    @if($req->type === 'upload')
                                                        <a href="{{ route('correspondence.request.download', $req) }}" class="p-2 bg-gray-50 hover:bg-indigo-50 text-gray-400 hover:text-indigo-600 rounded-lg transition-colors" title="Download Draft">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('correspondence.request.print', $req) }}" target="_blank" class="p-2 bg-gray-50 hover:bg-emerald-50 text-gray-400 hover:text-emerald-600 rounded-lg transition-colors" title="Cetak Surat">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-printer"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            @elseif($req->status === 'pending')
                                                @if($req->type === 'upload')
                                                    <a href="{{ route('correspondence.request.download', $req) }}" class="flex items-center gap-1 text-[10px] font-black text-indigo-400 uppercase hover:text-indigo-600 transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                                                        Lihat Draft
                                                    </a>
                                                @else
                                                    <span class="text-[10px] font-black text-emerald-400 uppercase">Draf Digital</span>
                                                @endif
                                            @else
                                                <span class="text-xs font-bold text-gray-300 italic">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3"
                                            class="px-8 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                                            Belum ada pengajuan nomor surat</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>