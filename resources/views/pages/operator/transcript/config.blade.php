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
                previewImage:@js($config->letterhead_path ? asset('storage/'.$config->letterhead_path) : null),
                dragging:false,
                formatDate(v){ if(!v) return ''; const d=new Date(v+'T00:00:00'); return d.toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}); },
                get preview(){ return `${this.start || '400.3.11/800.01'} - ${this.end || '400.3.11/800.190'}${this.suffix || ''} ${this.formatDate(this.date)}` },
                pickFile(){ this.$refs.letterheadInput.click(); },
                setFile(file){ if(!file) return; const dt = new DataTransfer(); dt.items.add(file); this.$refs.letterheadInput.files = dt.files; this.previewImage = URL.createObjectURL(file); },
                handleDrop(event){ this.dragging=false; this.setFile(event.dataTransfer.files[0]); },
                handleChange(event){ this.setFile(event.target.files[0]); }
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
                        <h3 class="text-lg font-black text-slate-900">Layout Halaman PDF</h3>
                        <div class="mt-5 grid gap-4 sm:grid-cols-5">
                            <label class="space-y-1"><span class="text-sm font-bold">Atas</span><input type="number" step="0.01" min="0" name="margin_top" value="{{ $config->margin_top ?? 15 }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Kanan</span><input type="number" step="0.01" min="0" name="margin_right" value="{{ $config->margin_right ?? 15 }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Bawah</span><input type="number" step="0.01" min="0" name="margin_bottom" value="{{ $config->margin_bottom ?? 15 }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Kiri</span><input type="number" step="0.01" min="0" name="margin_left" value="{{ $config->margin_left ?? 15 }}" class="w-full rounded-2xl border-slate-200"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Kertas</span><select name="paper_size" class="w-full rounded-2xl border-slate-200">@foreach(['A4','F4','Letter','Legal'] as $size)<option value="{{ $size }}" @selected(($config->paper_size ?? 'A4') === $size)>{{ $size }}</option>@endforeach</select></label>
                        </div>
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
        </div>
    </div>
</x-app-layout>
