<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-black text-slate-900">Config Transkrip</h2>
            <p class="text-sm text-slate-500">Pengaturan dokumen PDF transkrip nilai, kop, layout halaman, dan penomoran.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"
            x-data="{
                start:@js($config->number_start ?? '400.3.11/800.01'),
                end:@js($config->number_end ?? '400.3.11/800.190'),
                suffix:@js($config->number_suffix ?? '/SMKTEL-LPG/KURL.03/V/2026'),
                date:@js(optional($config->number_date)->format('Y-m-d') ?? now()->format('Y-m-d')),
                paperSize:@js(old('paper_size', $config->paper_size ?? 'A4')),
                previewImage:@js($config->letterhead_path ? asset('storage/'.$config->letterhead_path) : null),
                watermarkPreview:@js($config->watermark_path ? asset('storage/'.$config->watermark_path) : null),
                manualEnabled:@js((bool) old('manual_signature_enabled', $config->manual_signature_enabled)),
                manualPreview:@js($config->manual_signature_path ? asset('storage/'.$config->manual_signature_path) : null),
                manualModal:false,
                manualX:Number(@js((float) old('manual_signature_x', $config->manual_signature_x ?? 54))),
                manualY:Number(@js((float) old('manual_signature_y', $config->manual_signature_y ?? 74))),
                manualWidth:Number(@js((float) old('manual_signature_width', $config->manual_signature_width ?? 43))),
                manualDragging:null,
                dragging:false,
                watermarkDragging:false,
                formatDate(v){ if(!v) return ''; const d=new Date(v+'T00:00:00'); return d.toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}); },
                get preview(){ return `${this.start || '400.3.11/800.01'}${this.suffix || ''}` },
                pickFile(){ this.$refs.letterheadInput.click(); },
                setFile(file){ if(!file) return; const dt = new DataTransfer(); dt.items.add(file); this.$refs.letterheadInput.files = dt.files; this.previewImage = URL.createObjectURL(file); },
                handleDrop(event){ this.dragging=false; this.setFile(event.dataTransfer.files[0]); },
                handleChange(event){ this.setFile(event.target.files[0]); },
                pickWatermark(){ this.$refs.watermarkInput.click(); },
                setWatermark(file){ if(!file) return; const dt = new DataTransfer(); dt.items.add(file); this.$refs.watermarkInput.files = dt.files; this.watermarkPreview = URL.createObjectURL(file); },
                handleWatermarkDrop(event){ this.watermarkDragging=false; this.setWatermark(event.dataTransfer.files[0]); },
                handleWatermarkChange(event){ this.setWatermark(event.target.files[0]); },
                pickManualSignature(){ this.$refs.manualSignatureInput.click(); },
                setManualSignature(file){
                    if(!file) return;
                    const dt = new DataTransfer(); dt.items.add(file); this.$refs.manualSignatureInput.files = dt.files;
                    if(this.manualPreview && this.manualPreview.startsWith('blob:')) URL.revokeObjectURL(this.manualPreview);
                    this.manualPreview = URL.createObjectURL(file);
                    this.manualEnabled = true;
                    this.$nextTick(() => this.manualModal = true);
                },
                handleManualSignature(event){ this.setManualSignature(event.target.files[0]); },
                clampManual(){
                    this.manualWidth = Math.max(15, Math.min(70, Number(this.manualWidth) || 43));
                    this.manualX = Math.max(0, Math.min(100 - this.manualWidth, Number(this.manualX) || 0));
                    this.manualY = Math.max(0, Math.min(92, Number(this.manualY) || 0));
                },
                startManualDrag(event){
                    if(!this.$refs.manualStage) return;
                    const rect = this.$refs.manualStage.getBoundingClientRect();
                    this.manualDragging = {
                        x: event.clientX - rect.left - (this.manualX / 100 * rect.width),
                        y: event.clientY - rect.top - (this.manualY / 100 * rect.height)
                    };
                    event.target.setPointerCapture?.(event.pointerId);
                },
                moveManualDrag(event){
                    if(!this.manualDragging || !this.$refs.manualStage) return;
                    const rect = this.$refs.manualStage.getBoundingClientRect();
                    this.manualX = ((event.clientX - rect.left - this.manualDragging.x) / rect.width) * 100;
                    this.manualY = ((event.clientY - rect.top - this.manualDragging.y) / rect.height) * 100;
                    this.clampManual();
                },
                stopManualDrag(){ this.manualDragging = null; },
                get manualPaperRatio(){
                    return ({ A4:'210 / 297', F4:'210 / 330', Letter:'215.9 / 279.4', Legal:'215.9 / 355.6' })[this.paperSize] || '210 / 297';
                }
            }">
            <form method="POST" action="{{ route('operator.transcript.config.update') }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-[1fr_390px]">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-black text-slate-900">Identitas Dokumen</h3>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <label class="space-y-1"><span class="text-sm font-bold">Nama Satuan Pendidikan</span><input name="school_name" value="{{ $config->school_name }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">NPSN</span><input name="npsn" value="{{ $config->npsn }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Tanggal Kelulusan</span><input type="date" name="graduation_date" value="{{ optional($config->graduation_date)->format('Y-m-d') }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Kota Tanda Tangan</span><input name="signature_city" value="{{ $config->signature_city }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Tanggal Tanda Tangan</span><input type="date" name="signature_date" value="{{ optional($config->signature_date)->format('Y-m-d') }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Nama Kepala Sekolah</span><input name="principal_name" value="{{ $config->principal_name }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">NIP Kepala Sekolah</span><input name="principal_nip" value="{{ $config->principal_nip }}" class="w-full rounded-2xl border-slate-200"></label>
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-black text-slate-900">Kop Transkrip</h3>
                                <p class="text-sm text-slate-500">Unggah gambar kop untuk PDF transkrip.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">Max 10 MB</span>
                        </div>
                        <input x-ref="letterheadInput" type="file" name="letterhead_image" accept="image/*" class="hidden" @change="handleChange">
                        <button type="button" @click="pickFile" @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false" @drop.prevent="handleDrop"
                            class="mt-5 flex min-h-[210px] w-full flex-col items-center justify-center rounded-[24px] border-2 border-dashed p-5 text-center transition"
                            :class="dragging ? 'border-red-400 bg-red-50' : 'border-slate-300 bg-slate-50 hover:border-red-300 hover:bg-red-50/50'">
                            <template x-if="previewImage">
                                <div class="w-full">
                                    <img :src="previewImage" class="mx-auto max-h-44 rounded-2xl border border-slate-200 bg-white object-contain p-2">
                                    <p class="mt-4 text-sm font-black text-red-600">Ubah Kop Transkrip</p>
                                </div>
                            </template>
                            <template x-if="!previewImage">
                                <div>
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-2xl font-black text-red-600 shadow-sm">+</div>
                                    <p class="mt-4 text-sm font-black text-slate-900">Drag and drop gambar kop di sini</p>
                                    <p class="text-xs font-semibold text-slate-500">atau klik untuk unggah gambar</p>
                                </div>
                            </template>
                        </button>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-lg font-black text-slate-900">Arsip Scan Bertanda Tangan</h3>
                                    <span class="rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-black text-red-700">ARSIP DINAS</span>
                                </div>
                                <p class="mt-1 max-w-2xl text-sm text-slate-500">Tempel hasil scan tanda tangan, stempel, tanggal, dan identitas kepala sekolah pada transkrip. PDF akan diraster agar menyerupai hasil mesin scanner.</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" name="manual_signature_enabled" value="1" x-model="manualEnabled" class="peer sr-only">
                                <span class="h-7 w-12 rounded-full bg-slate-200 transition peer-checked:bg-red-600 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                            </label>
                        </div>

                        <div x-show="manualEnabled" x-collapse class="mt-5 space-y-5">
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs font-semibold leading-relaxed text-amber-900">
                                Gunakan gambar hasil crop yang sudah mencakup kota/tanggal, jabatan, tanda tangan, stempel, nama, dan NIP. Saat fitur aktif, gambar wajib tersedia dan teks tanda tangan biasa pada PDF akan digantikan oleh gambar ini.
                            </div>

                            <input x-ref="manualSignatureInput" type="file" name="manual_signature_image" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleManualSignature">
                            <input type="hidden" name="manual_signature_x" x-model.number="manualX">
                            <input type="hidden" name="manual_signature_y" x-model.number="manualY">
                            <input type="hidden" name="manual_signature_width" x-model.number="manualWidth">

                            <div class="grid gap-4 md:grid-cols-[220px_1fr]">
                                <button type="button" @click="pickManualSignature" class="flex min-h-[165px] items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-3 hover:border-red-300 hover:bg-red-50/50">
                                    <template x-if="manualPreview"><img :src="manualPreview" class="max-h-36 w-full object-contain"></template>
                                    <template x-if="!manualPreview"><span class="text-center"><strong class="block text-sm text-slate-900">Unggah scan tanda tangan</strong><small class="mt-1 block text-xs text-slate-500">JPG, PNG, atau WebP · maks. 15 MB</small></span></template>
                                </button>
                                <div class="flex flex-col justify-center rounded-2xl border border-slate-200 p-5">
                                    <strong class="text-sm text-slate-900" x-text="manualPreview ? 'Gambar siap digunakan' : 'Gambar belum tersedia'"></strong>
                                    <span class="mt-1 text-xs font-semibold text-slate-500">Posisi <span x-text="manualX.toFixed(1)"></span>% dari kiri, <span x-text="manualY.toFixed(1)"></span>% dari atas, lebar <span x-text="manualWidth.toFixed(1)"></span>% halaman.</span>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <button type="button" @click="pickManualSignature" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-black text-slate-700">Ganti gambar</button>
                                        <button type="button" x-show="manualPreview" @click="manualModal=true" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-black text-white">Atur posisi pada halaman</button>
                                    </div>
                                </div>
                            </div>
                            @error('manual_signature_image')<p class="text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                            @error('manual_signature_width')<p class="text-sm font-bold text-red-600">{{ $message }}</p>@enderror

                            <fieldset>
                                <legend class="text-sm font-black text-slate-900">Mode hasil scan</legend>
                                <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border p-4 transition has-[:checked]:border-red-400 has-[:checked]:bg-red-50">
                                        <input type="radio" name="scan_color_mode" value="color" class="mt-1 border-slate-300 text-red-600 focus:ring-red-500" @checked(old('scan_color_mode', $config->scan_color_mode ?? 'color') === 'color')>
                                        <span><strong class="block text-sm text-slate-900">Berwarna</strong><small class="mt-1 block text-xs text-slate-500">Mempertahankan warna stempel dan kop seperti hasil scan asli.</small></span>
                                    </label>
                                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border p-4 transition has-[:checked]:border-red-400 has-[:checked]:bg-red-50">
                                        <input type="radio" name="scan_color_mode" value="grayscale" class="mt-1 border-slate-300 text-red-600 focus:ring-red-500" @checked(old('scan_color_mode', $config->scan_color_mode ?? 'color') === 'grayscale')>
                                        <span><strong class="block text-sm text-slate-900">Hitam putih</strong><small class="mt-1 block text-xs text-slate-500">Meraster seluruh halaman dalam grayscale seperti mesin fotokopi/scanner.</small></span>
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-black text-slate-900">Watermark Transkrip</h3>
                                <p class="text-sm text-slate-500">Unggah gambar watermark transparan untuk bagian tengah PDF.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">Max 10 MB</span>
                        </div>
                        <input x-ref="watermarkInput" type="file" name="watermark_image" accept="image/*" class="hidden" @change="handleWatermarkChange">
                        <button type="button" @click="pickWatermark" @dragover.prevent="watermarkDragging=true" @dragleave.prevent="watermarkDragging=false" @drop.prevent="handleWatermarkDrop"
                            class="mt-5 flex min-h-[190px] w-full flex-col items-center justify-center rounded-[24px] border-2 border-dashed p-5 text-center transition"
                            :class="watermarkDragging ? 'border-red-400 bg-red-50' : 'border-slate-300 bg-slate-50 hover:border-red-300 hover:bg-red-50/50'">
                            <template x-if="watermarkPreview">
                                <div class="w-full">
                                    <img :src="watermarkPreview" class="mx-auto max-h-36 rounded-2xl border border-slate-200 bg-white object-contain p-3 opacity-70">
                                    <p class="mt-4 text-sm font-black text-red-600">Ubah Watermark</p>
                                </div>
                            </template>
                            <template x-if="!watermarkPreview">
                                <div>
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-2xl font-black text-red-600 shadow-sm">+</div>
                                    <p class="mt-4 text-sm font-black text-slate-900">Drag and drop watermark di sini</p>
                                    <p class="text-xs font-semibold text-slate-500">atau klik untuk unggah gambar</p>
                                </div>
                            </template>
                        </button>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-black text-slate-900">Layout Halaman PDF</h3>
                        <div class="mt-5 grid gap-4 sm:grid-cols-5">
                            <label class="space-y-1"><span class="text-sm font-bold">Atas</span><input type="number" step="0.01" min="0" name="margin_top" value="{{ $config->margin_top ?? 15 }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Kanan</span><input type="number" step="0.01" min="0" name="margin_right" value="{{ $config->margin_right ?? 15 }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Bawah</span><input type="number" step="0.01" min="0" name="margin_bottom" value="{{ $config->margin_bottom ?? 15 }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Kiri</span><input type="number" step="0.01" min="0" name="margin_left" value="{{ $config->margin_left ?? 15 }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Kertas</span><select name="paper_size" x-model="paperSize" class="w-full rounded-2xl border-slate-200">@foreach(['A4','F4','Letter','Legal'] as $size)<option value="{{ $size }}">{{ $size }}</option>@endforeach</select></label>
                        </div>
                        <label class="mt-5 flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <input type="checkbox" name="is_borderless" value="1" class="mt-1 rounded border-slate-300 text-red-600 focus:ring-red-500" @checked($config->is_borderless)>
                            <span>
                                <span class="block text-sm font-black text-slate-900">Tanpa margin / borderless</span>
                                <span class="block text-xs font-semibold text-slate-500">Gunakan untuk printer borderless agar kop dan halaman bisa memenuhi sisi kertas. Jika aktif, margin PDF dibuat 0 mm.</span>
                            </span>
                        </label>
                        <p class="mt-3 text-xs font-semibold text-slate-500">Margin memakai satuan milimeter untuk kebutuhan cetak PDF.</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-black text-slate-900">Penomoran</h3>
                        <div class="mt-5 space-y-4">
                            <label class="space-y-1"><span class="text-sm font-bold">Awalan Nomor</span><input name="number_start" x-model="start" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Akhiran Nomor</span><input name="number_end" x-model="end" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Prefix/Suffix Tetap</span><input name="number_suffix" x-model="suffix" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Tanggal Nomor</span><input type="date" name="number_date" x-model="date" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Preview Nomor</span><textarea readonly x-text="preview" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm font-bold text-slate-700"></textarea></label>
                        </div>
                    </div>
                    <button class="w-full rounded-2xl bg-red-600 px-5 py-3 font-black text-white shadow-lg shadow-red-100">Simpan Config Transkrip</button>
                </div>
            </form>

            <div x-show="manualModal" x-cloak @keydown.escape.window="manualModal=false" @pointermove.window="moveManualDrag($event)" @pointerup.window="stopManualDrag()" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/70 p-3 sm:p-6">
                <div @click.outside="manualModal=false" class="flex max-h-[96vh] w-full max-w-6xl flex-col overflow-hidden rounded-[24px] bg-white shadow-2xl">
                    <header class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div><p class="text-[10px] font-black uppercase text-red-600">Preview penempatan</p><h3 class="text-lg font-black text-slate-900">Atur Posisi Scan Tanda Tangan</h3></div>
                        <button type="button" @click="manualModal=false" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-xl text-slate-600">&times;</button>
                    </header>
                    <div class="grid min-h-0 flex-1 gap-5 overflow-y-auto bg-slate-100 p-4 lg:grid-cols-[minmax(0,1fr)_280px] lg:p-6">
                        <div class="flex min-h-[480px] items-start justify-center overflow-auto">
                            <div x-ref="manualStage" class="relative w-full max-w-[620px] overflow-hidden bg-[#fbfbf9] shadow-xl ring-1 ring-slate-300 select-none" :style="`aspect-ratio:${manualPaperRatio};`">
                                <div class="absolute inset-x-[8%] top-[5%] h-[8%] border-b-2 border-slate-700 text-center text-[clamp(7px,1.2vw,12px)] font-black text-slate-500">KOP SATUAN PENDIDIKAN</div>
                                <div class="absolute inset-x-[8%] top-[16%] text-center text-[clamp(8px,1.5vw,14px)] font-black">TRANSKRIP NILAI</div>
                                <div class="absolute inset-x-[8%] top-[21%] space-y-[1.5%] text-[clamp(5px,1vw,10px)] text-slate-400"><div class="h-2 w-3/4 bg-slate-200"></div><div class="h-2 w-2/3 bg-slate-200"></div><div class="h-2 w-4/5 bg-slate-200"></div></div>
                                <div class="absolute inset-x-[8%] top-[31%] h-[38%] border border-slate-400 bg-[repeating-linear-gradient(to_bottom,transparent_0,transparent_8%,#cbd5e1_8.2%,#cbd5e1_8.6%)]"></div>
                                <div class="absolute bottom-[6%] left-[8%] h-[14%] w-[27%] border border-dashed border-slate-200"></div>
                                <img x-show="manualPreview" :src="manualPreview" draggable="false" @pointerdown.prevent="startManualDrag($event)" class="absolute z-20 cursor-move touch-none object-contain shadow-[0_0_0_1px_rgba(239,68,68,.5)]" :class="manualDragging ? 'opacity-80' : ''" :style="`left:${manualX}%;top:${manualY}%;width:${manualWidth}%;`">
                            </div>
                        </div>
                        <aside class="space-y-5 rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                            <div><strong class="text-sm text-slate-900">Geser langsung pada kertas</strong><p class="mt-1 text-xs leading-relaxed text-slate-500">Tarik gambar ke area kanan bawah. Posisi tersimpan dalam persentase sehingga tetap konsisten pada A4, F4, Letter, maupun Legal.</p></div>
                            <label class="block"><span class="flex justify-between text-xs font-black text-slate-700"><span>Ukuran gambar</span><span x-text="`${manualWidth.toFixed(1)}%`"></span></span><input type="range" min="15" max="70" step="0.5" x-model.number="manualWidth" @input="clampManual" class="mt-3 w-full accent-red-600"></label>
                            <div class="grid grid-cols-2 gap-3"><label class="text-xs font-bold text-slate-600">Posisi X<input type="number" min="0" max="90" step="0.1" x-model.number="manualX" @input="clampManual" class="mt-1 w-full rounded-xl border-slate-200 text-sm"></label><label class="text-xs font-bold text-slate-600">Posisi Y<input type="number" min="0" max="92" step="0.1" x-model.number="manualY" @input="clampManual" class="mt-1 w-full rounded-xl border-slate-200 text-sm"></label></div>
                            <button type="button" @click="manualX=54;manualY=74;manualWidth=43;clampManual()" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-black text-slate-700">Kembalikan posisi awal</button>
                            <button type="button" @click="manualModal=false" class="w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-black text-white">Gunakan posisi ini</button>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
