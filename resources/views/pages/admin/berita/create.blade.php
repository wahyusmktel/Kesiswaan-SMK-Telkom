<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.berita.index') }}"
                class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-lg font-bold text-gray-800 leading-tight">Tambah Berita</h2>
                <p class="text-xs text-gray-500">Buat berita atau publikasikan informasi sekolah</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form
            action="{{ route('super-admin.berita.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
            x-data="newsArticleGenerator({
                endpoint: @js(route('super-admin.berita.generate-ai')),
                extractEndpoint: @js(route('super-admin.berita.extract-social')),
                csrfToken: @js(csrf_token()),
                aiReady: @js($aiReady),
            })"
        >
            @csrf

            {{-- Main Content --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori" x-ref="category" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            @foreach (['Akademik', 'Kesiswaan', 'Kegiatan', 'Prestasi', 'Pengumuman', 'Lainnya'] as $kat)
                                <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                </div>

                {{-- Stella AI Box --}}
                <section class="border border-red-200 bg-gradient-to-b from-red-50/70 to-red-50/30 rounded-2xl p-5 shadow-sm" aria-labelledby="stella-news-generator-title">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 shrink-0 rounded-xl bg-gradient-to-br from-red-600 to-rose-600 text-white flex items-center justify-center shadow-md shadow-red-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.813 15.904 9 18l-.813-2.096a4.5 4.5 0 0 0-2.591-2.591L3.5 12.5l2.096-.813a4.5 4.5 0 0 0 2.591-2.591L9 7l.813 2.096a4.5 4.5 0 0 0 2.591 2.591l2.096.813-2.096.813a4.5 4.5 0 0 0-2.591 2.591ZM18 7l-.406-1.047a2.25 2.25 0 0 0-1.297-1.297L15.25 4.25l1.047-.406A2.25 2.25 0 0 0 17.594 2.547L18 1.5l.406 1.047a2.25 2.25 0 0 0 1.297 1.297l1.047.406-1.047.406a2.25 2.25 0 0 0-1.297 1.297L18 7Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 id="stella-news-generator-title" class="text-sm font-black text-gray-900 flex items-center gap-2">
                                    <span>Hasilkan Artikel dengan Stella AI</span>
                                </h3>
                                <p class="text-xs leading-5 text-gray-600 mt-1">
                                    Generate artikel otomatis dari <strong>URL Instagram/Medsos/Web</strong> atau susun dari <strong>topik manual</strong>.
                                </p>
                            </div>
                        </div>
                        <span
                            class="inline-flex self-start items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shrink-0"
                            :class="aiReady ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                        >
                            <span class="w-1.5 h-1.5 rounded-full" :class="aiReady ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <span x-text="aiReady ? 'Siap digunakan' : 'Belum dikonfigurasi'"></span>
                        </span>
                    </div>

                    {{-- Mode Selector Tabs --}}
                    <div class="mt-5 pt-4 border-t border-red-200/80">
                        <div class="inline-flex p-1 bg-white rounded-xl border border-red-200/70 shadow-sm gap-1">
                            <button
                                type="button"
                                @click="mode = 'social'"
                                :class="mode === 'social' ? 'bg-red-600 text-white font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-red-50/50 font-medium'"
                                class="px-3.5 py-2 rounded-lg text-xs transition-all flex items-center gap-2"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                <span>Dari URL Medsos / Web</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-black tracking-wider"
                                    :class="mode === 'social' ? 'bg-red-700 text-white' : 'bg-red-100 text-red-700'">Ajaib ✨</span>
                            </button>
                            <button
                                type="button"
                                @click="mode = 'manual'"
                                :class="mode === 'manual' ? 'bg-red-600 text-white font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-red-50/50 font-medium'"
                                class="px-3.5 py-2 rounded-lg text-xs transition-all flex items-center gap-2"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Instruksi Topik Manual</span>
                            </button>
                        </div>
                    </div>

                    {{-- TAB 1: Dari URL Media Sosial / Web --}}
                    <div x-show="mode === 'social'" x-transition class="mt-4 space-y-4">
                        <div>
                            <label for="social_url_input" class="block text-xs font-bold text-gray-700 mb-1.5">
                                Masukkan Tautan Media Sosial / Berita Web
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                    </div>
                                    <input
                                        id="social_url_input"
                                        type="url"
                                        x-model="socialUrl"
                                        @keydown.enter.prevent="extractUrl"
                                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 placeholder-gray-400"
                                        placeholder="Contoh: https://www.instagram.com/p/DeMXQrTgdS5/ atau link berita web"
                                    >
                                </div>
                                <button
                                    type="button"
                                    @click="extractUrl"
                                    :disabled="extracting || !socialUrl.trim()"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-900 text-white font-bold text-xs rounded-xl hover:bg-black disabled:opacity-50 disabled:cursor-not-allowed transition-all shrink-0"
                                >
                                    <svg x-show="!extracting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <svg x-show="extracting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                                    </svg>
                                    <span x-text="extracting ? 'Mengambil Data...' : 'Ambil Konten'"></span>
                                </button>
                            </div>
                            <div class="flex items-center gap-2 mt-2 text-[11px] text-gray-500 flex-wrap">
                                <span class="font-medium">Mendukung:</span>
                                <span class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded-md font-semibold">Instagram</span>
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-md font-semibold">YouTube</span>
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-md font-semibold">TikTok</span>
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-md font-semibold">Facebook</span>
                                <span class="px-2 py-0.5 bg-gray-200 text-gray-800 rounded-md font-semibold">Web Berita / Blog</span>
                            </div>
                        </div>

                        {{-- Alert Error jika scrape gagal --}}
                        <div x-show="extractError" x-transition class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div class="space-y-1">
                                    <p class="font-bold" x-text="extractError"></p>
                                    <p class="text-[11px] text-amber-700">Tips: Anda tetap dapat menempelkan caption atau ringkasan berita secara langsung di kotak teks di bawah ini, lalu Stella AI akan langsung menyusun artikelnya!</p>
                                </div>
                            </div>
                        </div>

                        {{-- Kotak Preview Konten Hasil Ekstraksi --}}
                        <div x-show="extractedData || extractedCaption" x-transition class="bg-white border border-gray-200 rounded-xl p-4 space-y-4 shadow-sm">
                            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2.5 py-1 text-xs font-bold rounded-lg"
                                        :class="{
                                            'bg-pink-100 text-pink-700': extractedData?.platform === 'Instagram',
                                            'bg-red-100 text-red-700': extractedData?.platform === 'YouTube',
                                            'bg-gray-100 text-gray-800': extractedData?.platform === 'TikTok',
                                            'bg-blue-100 text-blue-700': extractedData?.platform === 'Facebook',
                                            'bg-emerald-100 text-emerald-700': extractedData?.platform === 'Website',
                                        }"
                                        x-text="extractedData?.platform || 'Konten Sumber'"
                                    ></span>
                                    <span x-show="extractedData?.author" class="text-xs font-semibold text-gray-600">
                                        oleh <strong x-text="extractedData?.author"></strong>
                                    </span>
                                </div>
                                <a :href="socialUrl" target="_blank" rel="noopener noreferrer" class="text-[11px] font-medium text-red-600 hover:underline flex items-center gap-1">
                                    <span>Buka tautan</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>

                            {{-- Gambar Preview & Pilihan Jadikan Cover --}}
                            <div x-show="selectedCoverImageUrl" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <img :src="selectedCoverImageUrl" class="w-16 h-16 object-cover rounded-lg border border-gray-200 shrink-0">
                                <div class="flex-1 space-y-1">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" x-model="useExtractedImage" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        <span class="text-xs font-bold text-gray-800">Gunakan foto ini otomatis sebagai Cover Berita</span>
                                    </label>
                                    <p class="text-[11px] text-gray-500">Foto akan diunduh dan disimpan secara permanen di server sekolah.</p>
                                </div>
                            </div>

                            {{-- Caption Terambil (Editable) --}}
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="text-xs font-bold text-gray-700">Teks / Caption Sumber (Dapat Disunting)</label>
                                    <span class="text-[11px] text-gray-400" x-text="`${extractedCaption.length} karakter`"></span>
                                </div>
                                <textarea
                                    x-model="extractedCaption"
                                    rows="5"
                                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono text-gray-800 focus:bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    placeholder="Teks atau caption hasil ekstraksi akan muncul di sini..."
                                ></textarea>
                                <p class="text-[11px] text-gray-500 mt-1">Anda dapat menambahkan atau membetulkan informasi (misal: nama guru, lokasi, atau tanggal) sebelum artikel digenerate.</p>
                            </div>

                            {{-- Instruksi Tambahan Opsional --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Instruksi Tambahan dari Redaksi (Opsional)</label>
                                <input
                                    type="text"
                                    x-model="extraInstructions"
                                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    placeholder="Contoh: Berikan apresiasi khusus dari Kepala Sekolah dan harapan prestasi ke tingkat nasional."
                                >
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: Instruksi Topik Manual --}}
                    <div x-show="mode === 'manual'" x-transition class="mt-4 space-y-4">
                        <div>
                            <label for="ai_instructions" class="block text-xs font-bold text-gray-700 mb-2">Instruksi Topik / Arahan Artikel</label>
                            <textarea id="ai_instructions" x-model="instructions" rows="4" maxlength="3000"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="Contoh: Buat artikel tentang prestasi siswa SMK Telkom Lampung di ajang LKS tingkat provinsi. Jelaskan persiapan, cabang lomba, dan motivasi bagi siswa lain."></textarea>
                            <div class="flex justify-between gap-4 mt-1">
                                <p class="text-[11px] text-gray-500">Sertakan tujuan pembaca, topik, sudut pembahasan, atau fakta yang wajib digunakan.</p>
                                <span class="text-[11px] text-gray-400" x-text="`${instructions.length}/3000`"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Pilihan Opsi AI Bersama --}}
                    <div class="mt-5 pt-4 border-t border-red-200/80 space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" x-model="recommended" class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span>
                                <span class="block text-xs font-bold text-gray-800">Rekomendasi panjang terbaik berdasarkan Stella AI</span>
                                <span class="block text-[11px] text-gray-500">Stella otomatis menyesuaikan struktur paragraf dan kedalaman narasi sesuai kategori berita.</span>
                            </span>
                        </label>

                        <div x-show="!recommended" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4 pl-6">
                            <div>
                                <label for="ai_paragraph_count" class="block text-xs font-bold text-gray-600 mb-1">Jumlah Paragraf</label>
                                <input id="ai_paragraph_count" type="number" x-model.number="paragraphCount" min="2" max="12"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <p class="text-[10px] text-gray-400 mt-0.5">Antara 2 sampai 12 paragraf.</p>
                            </div>
                            <div>
                                <label for="ai_sentences_per_paragraph" class="block text-xs font-bold text-gray-600 mb-1">Kalimat per Paragraf</label>
                                <input id="ai_sentences_per_paragraph" type="number" x-model.number="sentencesPerParagraph" min="2" max="8"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <p class="text-[10px] text-gray-400 mt-0.5">Antara 2 sampai 8 kalimat.</p>
                            </div>
                        </div>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" x-model="includeCodeSnippets" class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span>
                                <span class="block text-xs font-bold text-gray-800">Sertakan snippet kode teknologi (jika artikel tutorial)</span>
                                <span class="block text-[11px] text-gray-500">Stella menambahkan blok kode dengan syntax highlight yang dapat disalin langsung.</span>
                            </span>
                        </label>
                    </div>

                    {{-- Tombol Utama Generate Artikel AI --}}
                    <div class="mt-5 pt-4 border-t border-red-200/80 flex flex-col sm:flex-row sm:items-center gap-3">
                        <button
                            type="button"
                            @click="generate"
                            :disabled="loading || !aiReady || (mode === 'social' && !extractedCaption.trim() && !socialUrl.trim())"
                            class="inline-flex min-h-11 items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold text-sm rounded-xl hover:from-red-700 hover:to-rose-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-md shadow-red-600/20 transition-all"
                        >
                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.813 15.904 9 18l-.813-2.096a4.5 4.5 0 0 0-2.591-2.591L3.5 12.5l2.096-.813a4.5 4.5 0 0 0 2.591-2.591L9 7l.813 2.096a4.5 4.5 0 0 0 2.591 2.591l2.096.813-2.096.813a4.5 4.5 0 0 0-2.591 2.591Z" />
                            </svg>
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                            </svg>
                            <span x-text="loading ? 'Stella sedang menyusun artikel...' : (mode === 'social' ? '✨ Hasilkan Artikel dari URL' : 'Hasilkan Artikel')"></span>
                        </button>
                        <p x-show="lastResult" class="text-xs font-semibold text-emerald-700" x-text="lastResult"></p>
                        <p x-show="!aiReady" class="text-xs text-amber-700">Aktifkan Stella AI melalui pengaturan terlebih dahulu.</p>
                    </div>
                </section>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Judul Berita <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" x-ref="title" value="{{ old('judul') }}" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500 placeholder-gray-400"
                        placeholder="Masukkan judul berita...">
                    @error('judul')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Ringkasan</label>
                    <textarea name="ringkasan" x-ref="summary" rows="2"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500 placeholder-gray-400 resize-none"
                        placeholder="Ringkasan singkat berita (opsional)...">{{ old('ringkasan') }}</textarea>
                    @error('ringkasan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <section class="border-t border-gray-100 pt-6 space-y-5" aria-labelledby="seo-fields-title">
                    <div>
                        <h3 id="seo-fields-title" class="text-sm font-black text-gray-900">Optimasi SEO</h3>
                        <p class="text-xs text-gray-500 mt-1">Metadata dibuat otomatis oleh Stella AI dan tetap dapat disunting sebelum publikasi.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Judul SEO</label>
                        <input type="text" name="seo_title" x-ref="seoTitle" value="{{ old('seo_title') }}" maxlength="255"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Judul yang jelas untuk hasil pencarian Google">
                        @error('seo_title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Deskripsi SEO</label>
                        <textarea name="seo_description" x-ref="seoDescription" rows="3" maxlength="320"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
                            placeholder="Ringkasan informatif yang menjelaskan manfaat artikel">{{ old('seo_description') }}</textarea>
                        @error('seo_description')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Fokus Keyword</label>
                            <input type="text" name="focus_keyword" x-ref="focusKeyword" value="{{ old('focus_keyword') }}" maxlength="255"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="prestasi pencak silat SMK Telkom">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Keyword Pendukung</label>
                            <input type="text" name="seo_keywords" x-ref="seoKeywords" value="{{ old('seo_keywords') }}" maxlength="2000"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="SMK Telkom, pencak silat, juara silat, prestasi siswa">
                        </div>
                    </div>
                </section>

                {{-- Gambar Cover Berita --}}
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Gambar Cover</label>
                    <input type="hidden" name="cover_image_url" :value="useExtractedImage && selectedCoverImageUrl ? selectedCoverImageUrl : ''">

                    <div class="space-y-3">
                        {{-- Cover dari URL Sosial Media yang dipilih --}}
                        <template x-if="useExtractedImage && selectedCoverImageUrl && !manualFilePreview">
                            <div class="relative rounded-2xl overflow-hidden border border-emerald-200 bg-emerald-50/30 p-3">
                                <img :src="selectedCoverImageUrl" class="w-full max-h-56 object-cover rounded-xl border border-emerald-100">
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-white px-2.5 py-1 rounded-lg border border-emerald-200 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Cover otomatis dari postingan <span x-text="extractedData?.platform || 'Media Sosial'"></span>
                                    </span>
                                    <button type="button" @click="useExtractedImage = false; selectedCoverImageUrl = ''" class="text-xs font-bold text-red-600 hover:text-red-700 hover:underline">
                                        Ganti Cover Manual
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Input Upload Gambar Manual --}}
                        <div class="flex items-center gap-4" x-show="!useExtractedImage || !selectedCoverImageUrl || manualFilePreview">
                            <label
                                class="flex-1 cursor-pointer border-2 border-dashed border-gray-200 rounded-xl p-6 hover:border-red-300 hover:bg-red-50/30 transition-all text-center group">
                                <input type="file" name="gambar" accept="image/*" class="hidden"
                                    @change="handleFileUpload($event)">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs font-bold text-gray-500">Klik untuk upload gambar manual</p>
                                <p class="text-[10px] text-gray-400 mt-1">JPEG, PNG, WEBP (maks 5MB)</p>
                            </label>
                        </div>

                        {{-- Preview Upload File Manual --}}
                        <template x-if="manualFilePreview">
                            <div class="relative">
                                <img :src="manualFilePreview" class="w-full max-h-48 object-cover rounded-xl border border-gray-100">
                                <button type="button" @click="manualFilePreview = null" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 shadow hover:bg-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    @error('gambar')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Konten Berita <span class="text-red-500">*</span></label>
                    <textarea name="konten" x-ref="content" rows="12" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500 placeholder-gray-400"
                        placeholder="Tulis dengan Markdown. Gunakan ```php, ```python, atau nama bahasa lain untuk snippet kode.">{{ old('konten') }}</textarea>
                    @error('konten')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('super-admin.berita.index') }}"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-8 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-red-600/20 hover:shadow-red-600/40 hover:scale-[1.02] transition-all">
                    Simpan Berita
                </button>
            </div>
        </form>
    </div>

    <script>
        function newsArticleGenerator(config) {
            return {
                aiReady: config.aiReady,
                loading: false,
                mode: 'social',
                socialUrl: '',
                extracting: false,
                extractedData: null,
                extractedCaption: '',
                extraInstructions: '',
                extractError: '',
                useExtractedImage: true,
                selectedCoverImageUrl: '',
                manualFilePreview: null,
                recommended: true,
                paragraphCount: 4,
                sentencesPerParagraph: 3,
                instructions: '',
                includeCodeSnippets: false,
                lastResult: '',

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.manualFilePreview = URL.createObjectURL(file);
                        this.useExtractedImage = false;
                    }
                },

                async extractUrl() {
                    const url = this.socialUrl.trim();
                    if (!url) {
                        this.notify('URL belum diisi', 'Tempel tautan postingan Instagram, YouTube, atau web berita terlebih dahulu.', 'warning');
                        return;
                    }

                    this.extracting = true;
                    this.extractError = '';

                    try {
                        const response = await fetch(config.extractEndpoint, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken,
                            },
                            body: JSON.stringify({ url }),
                        });

                        const resData = await response.json();

                        if (!response.ok || !resData.success) {
                            throw new Error(resData.message || 'Gagal mengekstrak konten dari tautan tersebut.');
                        }

                        this.extractedData = resData.data;
                        this.extractedCaption = resData.data.caption || resData.data.title || '';

                        if (resData.data.image_url) {
                            this.selectedCoverImageUrl = resData.data.image_url;
                            this.useExtractedImage = true;
                        }

                        // Auto deteksi kategori berdasarkan teks
                        const lower = (this.extractedCaption + ' ' + (resData.data.title || '')).toLowerCase();
                        if (lower.includes('juara') || lower.includes('prestasi') || lower.includes('kejuaraan') || lower.includes('medali') || lower.includes('tanding') || lower.includes('lomba')) {
                            this.$refs.category.value = 'Prestasi';
                        } else if (lower.includes('workshop') || lower.includes('kegiatan') || lower.includes('pelatihan') || lower.includes('kunjungan') || lower.includes('upacara') || lower.includes('study tour')) {
                            this.$refs.category.value = 'Kegiatan';
                        } else if (lower.includes('pengumuman') || lower.includes('jadwal') || lower.includes('pemberitahuan') || lower.includes('edaran')) {
                            this.$refs.category.value = 'Pengumuman';
                        } else if (lower.includes('akademik') || lower.includes('ujian') || lower.includes('kurikulum') || lower.includes('kelulusan') || lower.includes('rapor')) {
                            this.$refs.category.value = 'Akademik';
                        }

                        this.notify(
                            'Konten Berhasil Diambil!',
                            `Informasi dari ${resData.data.platform} berhasil diekstrak. Silakan periksa caption lalu klik "Hasilkan Artikel".`,
                            'success'
                        );
                    } catch (error) {
                        this.extractError = error.message;
                        this.notify('Pemberitahuan Ekstraksi', error.message, 'warning');
                    } finally {
                        this.extracting = false;
                    }
                },

                async generate() {
                    if (!this.aiReady || this.loading) return;

                    // Jika mode social namun belum mengekstrak atau mengisi URL
                    if (this.mode === 'social' && !this.extractedCaption.trim()) {
                        if (this.socialUrl.trim()) {
                            await this.extractUrl();
                            if (!this.extractedCaption.trim()) return;
                        } else {
                            this.notify('Konten belum tersedia', 'Masukkan tautan media sosial dan klik "Ambil Konten" atau tempel caption terlebih dahulu.', 'warning');
                            return;
                        }
                    }

                    if (!this.recommended && (
                        this.paragraphCount < 2 || this.paragraphCount > 12 ||
                        this.sentencesPerParagraph < 2 || this.sentencesPerParagraph > 8
                    )) {
                        this.notify('Pengaturan panjang belum valid', 'Periksa kembali jumlah paragraf dan kalimat.', 'warning');
                        return;
                    }

                    const hasDraft = this.$refs.title.value.trim() || this.$refs.summary.value.trim() || this.$refs.content.value.trim();
                    if (hasDraft && window.Swal) {
                        const confirmation = await Swal.fire({
                            title: 'Ganti draf saat ini?',
                            text: 'Judul, ringkasan, dan konten yang sudah diisi akan diganti dengan hasil artikel Stella AI.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            confirmButtonText: 'Ya, susun ulang',
                            cancelButtonText: 'Batal',
                        });
                        if (!confirmation.isConfirmed) return;
                    }

                    this.loading = true;
                    this.lastResult = '';

                    try {
                        const payload = {
                            kategori: this.$refs.category.value,
                            use_ai_recommendation: this.recommended,
                            paragraph_count: this.recommended ? null : this.paragraphCount,
                            sentences_per_paragraph: this.recommended ? null : this.sentencesPerParagraph,
                            instructions: (this.mode === 'social' ? this.extraInstructions : this.instructions).trim() || null,
                            include_code_snippets: this.includeCodeSnippets,
                            source_url: this.mode === 'social' && this.socialUrl.trim() ? this.socialUrl.trim() : null,
                            source_content: this.mode === 'social' && this.extractedCaption.trim() ? this.extractedCaption.trim() : null,
                        };

                        const response = await fetch(config.endpoint, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken,
                            },
                            body: JSON.stringify(payload),
                        });
                        const data = await response.json();

                        if (!response.ok) {
                            const validationMessage = data.errors
                                ? Object.values(data.errors).flat().join(' ')
                                : data.message;
                            throw new Error(validationMessage || 'Stella AI gagal menghasilkan artikel.');
                        }

                        this.$refs.title.value = data.article.title;
                        this.$refs.summary.value = data.article.summary;
                        this.$refs.content.value = data.article.content;
                        this.$refs.seoTitle.value = data.article.seo_title || '';
                        this.$refs.seoDescription.value = data.article.seo_description || '';
                        this.$refs.focusKeyword.value = data.article.focus_keyword || '';
                        this.$refs.seoKeywords.value = data.article.seo_keywords || '';
                        ['title', 'summary', 'content', 'seoTitle', 'seoDescription', 'focusKeyword', 'seoKeywords'].forEach((field) => {
                            this.$refs[field].dispatchEvent(new Event('input', { bubbles: true }));
                        });

                        this.lastResult = `${data.article.paragraph_count} paragraf, ${data.article.sentence_count} kalimat`;
                        this.notify('Artikel Berhasil Dibuat ✨', `Draf berita lengkap berhasil disusun oleh Stella AI dan siap ditinjau.`, 'success');
                        this.$nextTick(() => this.$refs.title.focus());
                    } catch (error) {
                        this.notify('Gagal menghasilkan artikel', error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                notify(title, text, icon) {
                    if (window.Swal) {
                        Swal.fire({ title, text, icon, confirmButtonColor: '#dc2626' });
                        return;
                    }
                    window.alert(`${title}\n${text}`);
                },
            };
        }
    </script>
</x-app-layout>
