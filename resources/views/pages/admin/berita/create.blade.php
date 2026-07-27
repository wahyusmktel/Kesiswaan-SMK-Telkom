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
                <p class="text-xs text-gray-500">Buat berita atau informasi baru</p>
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

                <section class="border border-red-100 bg-red-50/40 rounded-xl p-5" aria-labelledby="stella-news-generator-title">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 shrink-0 rounded-lg bg-red-600 text-white flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.813 15.904 9 18l-.813-2.096a4.5 4.5 0 0 0-2.591-2.591L3.5 12.5l2.096-.813a4.5 4.5 0 0 0 2.591-2.591L9 7l.813 2.096a4.5 4.5 0 0 0 2.591 2.591l2.096.813-2.096.813a4.5 4.5 0 0 0-2.591 2.591ZM18 7l-.406-1.047a2.25 2.25 0 0 0-1.297-1.297L15.25 4.25l1.047-.406A2.25 2.25 0 0 0 17.594 2.547L18 1.5l.406 1.047a2.25 2.25 0 0 0 1.297 1.297l1.047.406-1.047.406a2.25 2.25 0 0 0-1.297 1.297L18 7Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 id="stella-news-generator-title" class="text-sm font-black text-gray-900">Hasilkan Artikel dengan Stella AI</h3>
                                <p class="text-xs leading-5 text-gray-500 mt-1">Draf akan mengisi judul, ringkasan, dan konten berdasarkan kategori terpilih.</p>
                            </div>
                        </div>
                        <span
                            class="inline-flex self-start items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                            :class="aiReady ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                        >
                            <span class="w-1.5 h-1.5 rounded-full" :class="aiReady ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <span x-text="aiReady ? 'Siap digunakan' : 'Belum dikonfigurasi'"></span>
                        </span>
                    </div>

                    <div class="mt-5 border-t border-red-100 pt-5 space-y-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" x-model="recommended" class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span>
                                <span class="block text-sm font-bold text-gray-800">Rekomendasi panjang berdasarkan AI</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Stella menentukan jumlah paragraf dan kalimat yang sesuai dengan kategori.</span>
                            </span>
                        </label>

                        <div x-show="!recommended" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ai_paragraph_count" class="block text-xs font-bold text-gray-600 mb-2">Jumlah Paragraf</label>
                                <input id="ai_paragraph_count" type="number" x-model.number="paragraphCount" min="2" max="12"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <p class="text-[11px] text-gray-400 mt-1">Antara 2 sampai 12 paragraf.</p>
                            </div>
                            <div>
                                <label for="ai_sentences_per_paragraph" class="block text-xs font-bold text-gray-600 mb-2">Kalimat per Paragraf</label>
                                <input id="ai_sentences_per_paragraph" type="number" x-model.number="sentencesPerParagraph" min="2" max="8"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <p class="text-[11px] text-gray-400 mt-1">Antara 2 sampai 8 kalimat.</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <button
                                type="button"
                                @click="generate"
                                :disabled="loading || !aiReady"
                                class="inline-flex min-h-11 items-center justify-center gap-2 px-5 py-2.5 bg-red-600 text-white font-bold text-sm rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.813 15.904 9 18l-.813-2.096a4.5 4.5 0 0 0-2.591-2.591L3.5 12.5l2.096-.813a4.5 4.5 0 0 0 2.591-2.591L9 7l.813 2.096a4.5 4.5 0 0 0 2.591 2.591l2.096.813-2.096.813a4.5 4.5 0 0 0-2.591 2.591Z" />
                                </svg>
                                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                                </svg>
                                <span x-text="loading ? 'Stella sedang menulis...' : 'Hasilkan Artikel'"></span>
                            </button>
                            <p x-show="lastResult" class="text-xs font-semibold text-emerald-700" x-text="lastResult"></p>
                            <p x-show="!aiReady" class="text-xs text-amber-700">Aktifkan Stella AI melalui halaman konfigurasi terlebih dahulu.</p>
                        </div>
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

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Gambar Cover</label>
                    <div x-data="{ preview: null }" class="space-y-3">
                        <div class="flex items-center gap-4">
                            <label
                                class="flex-1 cursor-pointer border-2 border-dashed border-gray-200 rounded-xl p-6 hover:border-red-300 hover:bg-red-50/30 transition-all text-center group">
                                <input type="file" name="gambar" accept="image/*" class="hidden"
                                    @change="preview = URL.createObjectURL($event.target.files[0])">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs font-bold text-gray-500">Klik untuk upload gambar</p>
                                <p class="text-[10px] text-gray-400 mt-1">JPEG, PNG, WEBP (maks 5MB)</p>
                            </label>
                        </div>
                        <template x-if="preview">
                            <img :src="preview" class="w-full max-h-48 object-cover rounded-xl border border-gray-100">
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
                        placeholder="Tulis konten berita di sini...">{{ old('konten') }}</textarea>
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
                recommended: true,
                paragraphCount: 4,
                sentencesPerParagraph: 3,
                lastResult: '',

                async generate() {
                    if (!this.aiReady || this.loading) return;

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
                            text: 'Judul, ringkasan, dan konten yang sudah diisi akan diganti dengan hasil Stella AI.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            confirmButtonText: 'Ya, hasilkan ulang',
                            cancelButtonText: 'Batal',
                        });
                        if (!confirmation.isConfirmed) return;
                    }

                    this.loading = true;
                    this.lastResult = '';

                    try {
                        const response = await fetch(config.endpoint, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken,
                            },
                            body: JSON.stringify({
                                kategori: this.$refs.category.value,
                                use_ai_recommendation: this.recommended,
                                paragraph_count: this.recommended ? null : this.paragraphCount,
                                sentences_per_paragraph: this.recommended ? null : this.sentencesPerParagraph,
                            }),
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
                        ['title', 'summary', 'content'].forEach((field) => {
                            this.$refs[field].dispatchEvent(new Event('input', { bubbles: true }));
                        });

                        this.lastResult = `${data.article.paragraph_count} paragraf, ${data.article.sentence_count} kalimat`;
                        this.notify('Draf artikel selesai', `${this.lastResult} berhasil dibuat dan masih dapat disunting.`, 'success');
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
