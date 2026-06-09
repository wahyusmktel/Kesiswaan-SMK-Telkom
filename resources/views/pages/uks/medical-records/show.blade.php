<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Detail Rekam Medis UKS</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $record->student?->nama_lengkap }} - {{ $record->visited_at->format('d M Y H:i') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ editOpen: false, signOpen: false, signType: 'UKS_SICK_NOTE', referralOpen: false, homeOpen: false }">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">{{ session('error') }}</div>
        @endif

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <a href="{{ route('uks.records.index') }}" class="w-fit rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">Kembali</a>
            <div class="flex flex-wrap gap-2">
                <button @click="editOpen = true" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">Edit</button>
                <a href="{{ route('uks.records.sick-note', $record) }}" target="_blank" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-black text-white hover:bg-blue-700">Cetak Surat Sakit</a>
                @if($record->disposition === 'istirahat_uks')
                    <button type="button" @click="referralOpen = true" class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white hover:bg-red-700">Rujukan</button>
                    <button type="button" @click="homeOpen = true" class="rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-black text-white hover:bg-amber-600">Dipulangkan</button>
                @endif
                @if($record->disposition === 'rujukan')
                    <a href="{{ route('uks.records.referral', $record) }}" target="_blank" class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white hover:bg-red-700">Cetak Rujukan</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Pasien Siswa</p>
                        <h3 class="mt-2 text-2xl font-black text-gray-900">{{ $record->student?->nama_lengkap }}</h3>
                        <p class="mt-1 text-sm font-semibold text-gray-500">{{ $record->student?->nis }} - {{ $record->student?->rombels->first()?->kelas?->nama_kelas ?? '-' }}</p>
                    </div>
                    <span class="rounded-full px-4 py-2 text-xs font-black {{ $record->condition === 'berat' ? 'bg-red-50 text-red-700' : ($record->condition === 'sedang' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">{{ $record->condition_label }}</span>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="rounded-2xl bg-gray-50 p-4"><p class="text-xs font-black text-gray-400 uppercase">Suhu</p><p class="mt-2 text-xl font-black">{{ $record->temperature ? $record->temperature . ' C' : '-' }}</p></div>
                    <div class="rounded-2xl bg-gray-50 p-4"><p class="text-xs font-black text-gray-400 uppercase">Tensi</p><p class="mt-2 text-xl font-black">{{ $record->blood_pressure ?: '-' }}</p></div>
                    <div class="rounded-2xl bg-gray-50 p-4"><p class="text-xs font-black text-gray-400 uppercase">Nadi</p><p class="mt-2 text-xl font-black">{{ $record->pulse ?: '-' }}</p></div>
                    <div class="rounded-2xl bg-gray-50 p-4"><p class="text-xs font-black text-gray-400 uppercase">O2</p><p class="mt-2 text-xl font-black">{{ $record->oxygen_saturation ? $record->oxygen_saturation . '%' : '-' }}</p></div>
                </div>

                <div class="mt-6 space-y-5">
                    <div><p class="text-xs font-black uppercase tracking-widest text-gray-400">Keluhan</p><p class="mt-1 font-bold text-gray-900">{{ $record->complaint }}</p></div>
                    <div><p class="text-xs font-black uppercase tracking-widest text-gray-400">Gejala</p><div class="mt-2 flex flex-wrap gap-2">@forelse($record->symptoms ?? [] as $symptom)<span class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-black text-red-700">{{ $symptom }}</span>@empty<span class="text-sm text-gray-400">Tidak dicatat</span>@endforelse</div></div>
                    <div><p class="text-xs font-black uppercase tracking-widest text-gray-400">Diagnosis / Analisa UKS</p><p class="mt-1 text-gray-700">{{ $record->diagnosis ?: '-' }}</p></div>
                    <div><p class="text-xs font-black uppercase tracking-widest text-gray-400">Tindakan dan Obat</p><p class="mt-1 text-gray-700">{{ $record->treatment ?: '-' }}</p><p class="mt-1 text-gray-700">{{ $record->medicine ?: '-' }}</p></div>
                    <div><p class="text-xs font-black uppercase tracking-widest text-gray-400">Tindak Lanjut</p><p class="mt-1 font-bold text-gray-900">{{ $record->disposition_label }}</p></div>
                    @if($record->disposition === 'rujukan')
                        <div class="rounded-2xl border border-red-100 bg-red-50 p-4">
                            <p class="text-xs font-black uppercase tracking-widest text-red-500">Rujukan</p>
                            <p class="mt-2 font-black text-red-900">{{ $record->referral_facility_type }} - {{ $record->referral_facility_name }}</p>
                            <p class="mt-1 text-sm text-red-800">{{ $record->referral_reason }}</p>
                        </div>
                    @endif
                    @if($record->disposition === 'pulang')
                        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                            <p class="text-xs font-black uppercase tracking-widest text-amber-600">Dipulangkan</p>
                            <p class="mt-2 text-sm font-bold text-amber-900">{{ $record->parent_notification ?: '-' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="font-black text-gray-900">Tanda Tangan Digital</h3>
                    <div class="mt-4 space-y-3">
                        <div class="rounded-2xl bg-gray-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div><p class="font-bold text-gray-900">Surat Sakit</p><p class="text-xs text-gray-500">{{ $sickDocument ? 'Ditandatangani ' . $sickDocument->signed_at->format('d M Y H:i') : 'Belum ditandatangani' }}</p></div>
                                @if($sickDocument)<span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">Sah</span>@endif
                            </div>
                            @if(!$sickDocument)
                                <button @click="signOpen = true; signType = 'UKS_SICK_NOTE'" class="mt-3 w-full rounded-xl bg-gray-900 px-3 py-2 text-xs font-black text-white hover:bg-red-600">Tandatangani Manual</button>
                            @endif
                        </div>
                        @if($record->disposition === 'rujukan')
                            <div class="rounded-2xl bg-gray-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div><p class="font-bold text-gray-900">Surat Rujukan</p><p class="text-xs text-gray-500">{{ $referralDocument ? 'Ditandatangani ' . $referralDocument->signed_at->format('d M Y H:i') : 'Belum ditandatangani' }}</p></div>
                                    @if($referralDocument)<span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">Sah</span>@endif
                                </div>
                                @if(!$referralDocument)
                                    <button @click="signOpen = true; signType = 'UKS_REFERRAL'" class="mt-3 w-full rounded-xl bg-gray-900 px-3 py-2 text-xs font-black text-white hover:bg-red-600">Tandatangani Manual</button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('uks.records.destroy', $record) }}" onsubmit="return confirm('Hapus rekam medis ini?')" class="rounded-2xl border border-red-100 bg-red-50 p-5">
                    @csrf
                    @method('DELETE')
                    <button class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white hover:bg-red-700">Hapus Rekam Medis</button>
                </form>
            </div>
        </div>

        <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 p-4 backdrop-blur-sm">
            <div class="flex min-h-full items-center justify-center">
                <form method="POST" action="{{ route('uks.records.update', $record) }}" class="w-full max-w-5xl rounded-3xl bg-white shadow-2xl overflow-hidden">
                    @csrf
                    @method('PUT')
                    <div class="border-b border-gray-100 p-6 flex justify-between">
                        <h3 class="text-xl font-black text-gray-900">Edit Rekam Medis UKS</h3>
                        <button type="button" @click="editOpen = false" class="h-10 w-10 rounded-xl border border-gray-200">X</button>
                    </div>
                    @include('pages.uks.medical-records.partials.form', ['record' => $record, 'students' => $students])
                </form>
            </div>
        </div>

        <div x-show="signOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">
            <form method="POST" action="{{ route('uks.records.sign', $record) }}" class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
                @csrf
                <input type="hidden" name="document_type" :value="signType">
                <h3 class="text-xl font-black text-gray-900">Tanda Tangan Manual</h3>
                <p class="mt-1 text-sm text-gray-500">Masukkan PIN tanda tangan digital Petugas UKS.</p>
                <input type="password" name="pin" required inputmode="numeric" class="mt-5 w-full rounded-xl border-gray-300 text-center text-lg font-black tracking-widest focus:border-red-500 focus:ring-red-500" placeholder="PIN">
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="signOpen = false" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold">Batal</button>
                    <button class="rounded-xl bg-red-600 px-4 py-2 text-sm font-black text-white">Tandatangani</button>
                </div>
            </form>
        </div>

        @if($record->disposition === 'istirahat_uks')
            <div x-show="referralOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">
                <form method="POST" target="_blank" action="{{ route('uks.records.convert-referral', $record) }}" @submit="setTimeout(() => window.location.reload(), 1200)" class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl">
                    @csrf
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Buat Surat Rujukan</h3>
                            <p class="mt-1 text-sm font-semibold text-gray-500">{{ $record->student?->nama_lengkap }} - {{ $record->student?->nis }}</p>
                        </div>
                        <button type="button" @click="referralOpen = false" class="h-10 w-10 rounded-xl border border-gray-200 text-gray-600">X</button>
                    </div>
                    <div class="mt-5 space-y-4">
                        <label class="block">
                            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Jenis Faskes Rujukan</span>
                            <select name="referral_facility_type" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                <option value="">Pilih faskes</option>
                                <option value="Puskesmas">Puskesmas</option>
                                <option value="Rumah Sakit">Rumah Sakit</option>
                                <option value="Klinik">Klinik</option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Nama Faskes Rujukan</span>
                            <input name="referral_facility_name" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Puskesmas Ajibarang">
                        </label>
                        <label class="block">
                            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Alasan Rujukan</span>
                            <textarea name="referral_reason" required rows="4" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Tuliskan kondisi dan alasan siswa perlu dirujuk."></textarea>
                        </label>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="referralOpen = false" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700">Batal</button>
                        <button class="rounded-xl bg-red-600 px-4 py-2 text-sm font-black text-white hover:bg-red-700">Simpan & Cetak Rujukan</button>
                    </div>
                </form>
            </div>

            <div x-show="homeOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">
                <form method="POST" target="_blank" action="{{ route('uks.records.convert-home', $record) }}" @submit="setTimeout(() => window.location.reload(), 1200)" class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl">
                    @csrf
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Siswa Dipulangkan</h3>
                            <p class="mt-1 text-sm font-semibold text-gray-500">{{ $record->student?->nama_lengkap }} - {{ $record->student?->nis }}</p>
                        </div>
                        <button type="button" @click="homeOpen = false" class="h-10 w-10 rounded-xl border border-gray-200 text-gray-600">X</button>
                    </div>
                    <label class="mt-5 block">
                        <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Orang Tua / Alasan Dipulangkan</span>
                        <textarea name="parent_notification" required rows="5" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Orang tua sudah dihubungi dan siswa dijemput karena kondisi perlu istirahat di rumah."></textarea>
                    </label>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="homeOpen = false" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700">Batal</button>
                        <button class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-black text-white hover:bg-amber-600">Simpan & Cetak Surat</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
