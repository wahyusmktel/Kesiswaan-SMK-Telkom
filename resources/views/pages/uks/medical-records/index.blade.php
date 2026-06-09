<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Rekam Medis UKS</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pendataan kunjungan siswa sakit, rujukan, analisa penyakit, dan laporan UKS.</p>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ createOpen: false, referralOpen: null, homeOpen: null }">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Total Kunjungan</p>
                <p class="mt-3 text-3xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-blue-600">Hari Ini</p>
                <p class="mt-3 text-3xl font-black text-blue-700">{{ number_format($stats['today']) }}</p>
            </div>
            <div class="rounded-2xl border border-red-100 bg-red-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-red-600">Rujukan</p>
                <p class="mt-3 text-3xl font-black text-red-700">{{ number_format($stats['referrals']) }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-amber-600">Istirahat UKS</p>
                <p class="mt-3 text-3xl font-black text-amber-700">{{ number_format($stats['resting']) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h3 class="font-black text-gray-900">Kunjungan UKS</h3>
                        <p class="text-sm text-gray-500">Filter data dan tambah rekam medis siswa.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="createOpen = true" class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white hover:bg-red-700">Tambah Rekam Medis</button>
                        <a href="{{ route('uks.records.report', ['period' => 'weekly', 'date' => now()->toDateString()]) }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">PDF Mingguan</a>
                        <a href="{{ route('uks.records.report', ['period' => 'monthly', 'date' => now()->toDateString()]) }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">PDF Bulanan</a>
                    </div>
                </div>

                <form method="GET" action="{{ route('uks.records.index') }}" class="mt-5 grid grid-cols-1 md:grid-cols-6 gap-3">
                    <input name="search" value="{{ request('search') }}" class="md:col-span-2 rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Cari nama, NIS, keluhan, diagnosis">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <select name="disposition" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">Semua tindak lanjut</option>
                        <option value="kembali_kelas" @selected(request('disposition') === 'kembali_kelas')>Kembali kelas</option>
                        <option value="istirahat_uks" @selected(request('disposition') === 'istirahat_uks')>Istirahat UKS</option>
                        <option value="pulang" @selected(request('disposition') === 'pulang')>Pulang</option>
                        <option value="rujukan" @selected(request('disposition') === 'rujukan')>Rujukan</option>
                    </select>
                    <div class="flex gap-2">
                        <button class="flex-1 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">Filter</button>
                        <a href="{{ route('uks.records.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50">Reset</a>
                    </div>
                </form>

                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Siswa</th>
                                <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Kunjungan</th>
                                <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Keluhan/Diagnosis</th>
                                <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Tindak Lanjut</th>
                                <th class="px-4 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($records as $record)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-gray-900">{{ $record->student?->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-400">{{ $record->student?->nis }} - {{ $record->student?->rombels->first()?->kelas?->nama_kelas ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-600">{{ $record->visited_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $record->complaint }}</div>
                                        <div class="text-xs text-gray-500">{{ $record->diagnosis ?: 'Diagnosis belum diisi' }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full px-3 py-1.5 text-xs font-black {{ $record->disposition === 'rujukan' ? 'bg-red-50 text-red-700' : ($record->disposition === 'istirahat_uks' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">{{ $record->disposition_label }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            @if($record->disposition === 'istirahat_uks')
                                                <button type="button" @click="referralOpen = {{ $record->id }}" class="rounded-xl bg-red-600 px-3 py-2 text-xs font-black text-white hover:bg-red-700">Rujukan</button>
                                                <button type="button" @click="homeOpen = {{ $record->id }}" class="rounded-xl bg-amber-500 px-3 py-2 text-xs font-black text-white hover:bg-amber-600">Dipulangkan</button>
                                            @endif
                                            <a href="{{ route('uks.records.show', $record) }}" class="rounded-xl bg-gray-900 px-3 py-2 text-xs font-black text-white hover:bg-red-600">Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Belum ada rekam medis UKS.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($records->hasPages())
                    <div class="mt-5">{{ $records->links() }}</div>
                @endif

                @foreach($records as $record)
                    @if($record->disposition === 'istirahat_uks')
                        <div x-show="referralOpen === {{ $record->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">
                            <form method="POST" target="_blank" action="{{ route('uks.records.convert-referral', $record) }}" @submit="setTimeout(() => window.location.reload(), 1200)" class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl">
                                @csrf
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-xl font-black text-gray-900">Buat Surat Rujukan</h3>
                                        <p class="mt-1 text-sm font-semibold text-gray-500">{{ $record->student?->nama_lengkap }} - {{ $record->student?->nis }}</p>
                                    </div>
                                    <button type="button" @click="referralOpen = null" class="h-10 w-10 rounded-xl border border-gray-200 text-gray-600">X</button>
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
                                    <button type="button" @click="referralOpen = null" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700">Batal</button>
                                    <button class="rounded-xl bg-red-600 px-4 py-2 text-sm font-black text-white hover:bg-red-700">Simpan & Cetak Rujukan</button>
                                </div>
                            </form>
                        </div>

                        <div x-show="homeOpen === {{ $record->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">
                            <form method="POST" target="_blank" action="{{ route('uks.records.convert-home', $record) }}" @submit="setTimeout(() => window.location.reload(), 1200)" class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl">
                                @csrf
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-xl font-black text-gray-900">Siswa Dipulangkan</h3>
                                        <p class="mt-1 text-sm font-semibold text-gray-500">{{ $record->student?->nama_lengkap }} - {{ $record->student?->nis }}</p>
                                    </div>
                                    <button type="button" @click="homeOpen = null" class="h-10 w-10 rounded-xl border border-gray-200 text-gray-600">X</button>
                                </div>
                                <label class="mt-5 block">
                                    <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Orang Tua / Alasan Dipulangkan</span>
                                    <textarea name="parent_notification" required rows="5" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Orang tua sudah dihubungi dan siswa dijemput karena kondisi perlu istirahat di rumah."></textarea>
                                </label>
                                <div class="mt-6 flex justify-end gap-2">
                                    <button type="button" @click="homeOpen = null" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700">Batal</button>
                                    <button class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-black text-white hover:bg-amber-600">Simpan & Cetak Surat</button>
                                </div>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Analisa Penyakit</p>
                <h3 class="mt-2 text-xl font-black text-gray-900">Keluhan/diagnosis terbanyak</h3>
                <div class="mt-5 space-y-4">
                    @forelse($topDiagnoses as $diagnosis => $count)
                        <div>
                            <div class="flex justify-between text-sm font-bold">
                                <span class="truncate text-gray-700">{{ $diagnosis }}</span>
                                <span class="text-gray-400">{{ $count }}</span>
                            </div>
                            <div class="mt-2 h-2.5 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-red-600 to-rose-400" style="width: {{ max(8, ($count / max($topDiagnoses->max(), 1)) * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-2xl bg-gray-50 p-4 text-sm font-semibold text-gray-500">Belum ada diagnosis untuk dianalisa.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 p-4 backdrop-blur-sm">
            <div class="flex min-h-full items-center justify-center">
                <form method="POST" action="{{ route('uks.records.store') }}" class="w-full max-w-5xl rounded-3xl bg-white shadow-2xl overflow-hidden">
                    @csrf
                    <div class="border-b border-gray-100 p-6 flex justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Tambah Rekam Medis UKS</h3>
                            <p class="mt-1 text-sm text-gray-500">Input kunjungan siswa seperti pencatatan klinik sederhana.</p>
                        </div>
                        <button type="button" @click="createOpen = false" class="h-10 w-10 rounded-xl border border-gray-200 text-gray-600">X</button>
                    </div>
                    @include('pages.uks.medical-records.partials.form', ['record' => null, 'students' => $students])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
