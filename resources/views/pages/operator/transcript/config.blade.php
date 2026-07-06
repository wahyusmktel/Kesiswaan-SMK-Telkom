<x-app-layout>
    <x-slot name="header"><div><h2 class="text-xl font-black text-slate-900">Config Transkrip</h2><p class="text-sm text-slate-500">Pengaturan dokumen PDF transkrip nilai dan penomoran transkrip.</p></div></x-slot>
    <div class="py-8"><div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8" x-data="{ start:@js($config->number_start ?? '400.3.11/800.01'), end:@js($config->number_end ?? '400.3.11/800.190'), suffix:@js($config->number_suffix ?? '/SMKTEL-LPG/KURL.03/V/2026'), date:@js(optional($config->number_date)->format('Y-m-d') ?? now()->format('Y-m-d')), formatDate(v){ if(!v) return ''; const d=new Date(v+'T00:00:00'); return d.toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}); }, get preview(){ return `${this.start || '400.3.11/800.01'} - ${this.end || '400.3.11/800.190'}${this.suffix || ''} ${this.formatDate(this.date)}` } }">
        <form method="POST" action="{{ route('operator.transcript.config.update') }}" class="grid gap-6 lg:grid-cols-[1fr_360px]">@csrf @method('PUT')
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
                    <label class="space-y-1 sm:col-span-2"><span class="text-sm font-bold">Kop Transkrip</span><textarea name="letterhead" rows="5" class="w-full rounded-2xl border-slate-200">{{ $config->letterhead }}</textarea></label>
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
    </div></div>
</x-app-layout>
