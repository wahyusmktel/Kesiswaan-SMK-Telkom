<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dapodik-guru.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Detail Dapodik</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $dapodikGuru->nama }}</p>
            </div>
        </div>
    </x-slot>

    @php
    function dRow(string $label, $value, string $type = 'text'): void {
        $display = ($value === null || $value === '') ? '<span class="text-gray-300 italic text-xs">Tidak diisi</span>' : e($value);
        if ($type === 'date' && $value instanceof \Carbon\Carbon) {
            $display = $value->translatedFormat('d F Y');
        } elseif ($type === 'date' && is_string($value) && $value) {
            try { $display = \Carbon\Carbon::parse($value)->translatedFormat('d F Y'); } catch (\Exception) {}
        }
        echo "<div class='flex flex-col sm:flex-row sm:items-start gap-1 py-3 border-b border-gray-50 last:border-0'>
                <dt class='text-xs font-bold text-gray-400 uppercase tracking-wider sm:w-52 shrink-0'>{$label}</dt>
                <dd class='text-sm text-gray-800 font-medium'>{$display}</dd>
              </div>";
    }
    @endphp

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Hero card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                <div class="p-6 flex flex-col sm:flex-row items-start gap-5">
                    {{-- Avatar --}}
                    <div class="shrink-0">
                        @if($dapodikGuru->masterGuru?->user?->avatar)
                            <img src="{{ $dapodikGuru->masterGuru->user->avatar }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-gray-100" alt="">
                        @else
                            <div class="w-20 h-20 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-black text-3xl border-2 border-blue-200">
                                {{ strtoupper(substr($dapodikGuru->nama, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Info utama --}}
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-black text-gray-900">{{ $dapodikGuru->nama }}</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            @if($dapodikGuru->jenis_ptk)
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $dapodikGuru->jenis_ptk }}</span>
                            @endif
                            @if($dapodikGuru->status_kepegawaian)
                                @php
                                    $sc = match(true) {
                                        str_contains($dapodikGuru->status_kepegawaian, 'PNS')   => 'bg-blue-50 text-blue-700 border-blue-100',
                                        str_contains($dapodikGuru->status_kepegawaian, 'Honor') => 'bg-amber-50 text-amber-700 border-amber-100',
                                        str_contains($dapodikGuru->status_kepegawaian, 'PPPK')  => 'bg-teal-50 text-teal-700 border-teal-100',
                                        default => 'bg-gray-50 text-gray-600 border-gray-100',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold border {{ $sc }}">{{ $dapodikGuru->status_kepegawaian }}</span>
                            @endif
                            @if($dapodikGuru->masterGuru)
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Terhubung: {{ $dapodikGuru->masterGuru->user?->name ?? $dapodikGuru->masterGuru->nama_lengkap }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-gray-50 text-gray-400 border border-gray-100">Tidak terhubung ke akun</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-4 mt-3">
                            @if($dapodikGuru->nik)
                                <div class="text-xs"><span class="text-gray-400 font-bold uppercase">NIK</span> <span class="font-mono text-gray-700 bg-gray-100 px-2 py-0.5 rounded ml-1">{{ $dapodikGuru->nik }}</span></div>
                            @endif
                            @if($dapodikGuru->nuptk)
                                <div class="text-xs"><span class="text-gray-400 font-bold uppercase">NUPTK</span> <span class="font-mono text-gray-700 bg-gray-100 px-2 py-0.5 rounded ml-1">{{ $dapodikGuru->nuptk }}</span></div>
                            @endif
                            @if($dapodikGuru->nip)
                                <div class="text-xs"><span class="text-gray-400 font-bold uppercase">NIP</span> <span class="font-mono text-gray-700 bg-gray-100 px-2 py-0.5 rounded ml-1">{{ $dapodikGuru->nip }}</span></div>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('dapodik-guru.edit', $dapodikGuru) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-400 transition-all shadow-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Data
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Data Pribadi --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center"><svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        Data Pribadi
                    </h3>
                    <dl>
                        @php dRow('Nama Lengkap', $dapodikGuru->nama) @endphp
                        @php dRow('Jenis Kelamin', $dapodikGuru->jenis_kelamin_label) @endphp
                        @php dRow('Tempat Lahir', $dapodikGuru->tempat_lahir) @endphp
                        @php dRow('Tanggal Lahir', $dapodikGuru->tanggal_lahir, 'date') @endphp
                        @php dRow('Agama', $dapodikGuru->agama) @endphp
                        @php dRow('Kewarganegaraan', $dapodikGuru->kewarganegaraan) @endphp
                        @php dRow('NIK', $dapodikGuru->nik) @endphp
                        @php dRow('No. KK', $dapodikGuru->no_kk) @endphp
                        @php dRow('Nama Ibu Kandung', $dapodikGuru->nama_ibu_kandung) @endphp
                    </dl>
                </div>

                {{-- Data Kepegawaian --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center"><svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                        Data Kepegawaian
                    </h3>
                    <dl>
                        @php dRow('NUPTK', $dapodikGuru->nuptk) @endphp
                        @php dRow('NIP', $dapodikGuru->nip) @endphp
                        @php dRow('Status Kepegawaian', $dapodikGuru->status_kepegawaian) @endphp
                        @php dRow('Jenis PTK', $dapodikGuru->jenis_ptk) @endphp
                        @php dRow('Tugas Tambahan', $dapodikGuru->tugas_tambahan) @endphp
                        @php dRow('Pangkat / Golongan', $dapodikGuru->pangkat_golongan) @endphp
                        @php dRow('Sumber Gaji', $dapodikGuru->sumber_gaji) @endphp
                        @php dRow('Lembaga Pengangkatan', $dapodikGuru->lembaga_pengangkatan) @endphp
                        @php dRow('SK Pengangkatan', $dapodikGuru->sk_pengangkatan) @endphp
                        @php dRow('TMT Pengangkatan', $dapodikGuru->tmt_pengangkatan, 'date') @endphp
                        @php dRow('SK CPNS', $dapodikGuru->sk_cpns) @endphp
                        @php dRow('Tanggal CPNS', $dapodikGuru->tanggal_cpns, 'date') @endphp
                        @php dRow('TMT PNS', $dapodikGuru->tmt_pns, 'date') @endphp
                        @php dRow('NUKS', $dapodikGuru->nuks) @endphp
                        @php dRow('Karpeg', $dapodikGuru->karpeg) @endphp
                        @php dRow('Karis / Karsu', $dapodikGuru->karis_karsu) @endphp
                    </dl>
                </div>

                {{-- Alamat & Kontak --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                        Alamat & Kontak
                    </h3>
                    <dl>
                        @php dRow('Alamat Jalan', $dapodikGuru->alamat_jalan) @endphp
                        @php dRow('RT / RW', ($dapodikGuru->rt || $dapodikGuru->rw) ? ($dapodikGuru->rt . ' / ' . $dapodikGuru->rw) : null) @endphp
                        @php dRow('Nama Dusun', $dapodikGuru->nama_dusun) @endphp
                        @php dRow('Desa / Kelurahan', $dapodikGuru->desa_kelurahan) @endphp
                        @php dRow('Kecamatan', $dapodikGuru->kecamatan) @endphp
                        @php dRow('Kode Pos', $dapodikGuru->kode_pos) @endphp
                        @php dRow('Koordinat', ($dapodikGuru->lintang || $dapodikGuru->bujur) ? ($dapodikGuru->lintang . ', ' . $dapodikGuru->bujur) : null) @endphp
                        @php dRow('Telepon', $dapodikGuru->telepon) @endphp
                        @php dRow('HP', $dapodikGuru->hp) @endphp
                        @php dRow('Email Dapodik', $dapodikGuru->email_dapodik) @endphp
                    </dl>
                </div>

                {{-- Keluarga & Keuangan --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                    {{-- Keluarga --}}
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-pink-50 flex items-center justify-center"><svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
                            Data Keluarga
                        </h3>
                        <dl>
                            @php dRow('Status Perkawinan', $dapodikGuru->status_perkawinan) @endphp
                            @php dRow('Nama Suami/Istri', $dapodikGuru->nama_pasangan) @endphp
                            @php dRow('NIP Suami/Istri', $dapodikGuru->nip_pasangan) @endphp
                            @php dRow('Pekerjaan Suami/Istri', $dapodikGuru->pekerjaan_pasangan) @endphp
                        </dl>
                    </div>

                    {{-- Keuangan --}}
                    <div class="border-t border-gray-100 pt-5">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                            Data Keuangan
                        </h3>
                        <dl>
                            @php dRow('NPWP', $dapodikGuru->npwp) @endphp
                            @php dRow('Nama Wajib Pajak', $dapodikGuru->nama_wajib_pajak) @endphp
                            @php dRow('Bank', $dapodikGuru->bank) @endphp
                            @php dRow('No. Rekening', $dapodikGuru->no_rekening) @endphp
                            @php dRow('Rekening a/n', $dapodikGuru->rekening_atas_nama) @endphp
                        </dl>
                    </div>
                </div>

            </div>

            {{-- Sertifikasi --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center"><svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg></div>
                    Sertifikasi & Keahlian Khusus
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @php
                    $certItems = [
                        ['label' => 'Lisensi Kepala Sekolah', 'value' => $dapodikGuru->lisensi_kepala_sekolah],
                        ['label' => 'Diklat Kepengawasan', 'value' => $dapodikGuru->diklat_kepengawasan],
                        ['label' => 'Keahlian Braille', 'value' => $dapodikGuru->keahlian_braille],
                        ['label' => 'Bahasa Isyarat', 'value' => $dapodikGuru->keahlian_bahasa_isyarat],
                    ];
                    @endphp
                    @foreach($certItems as $cert)
                        @php $ya = strtolower($cert['value'] ?? '') === 'ya'; @endphp
                        <div class="rounded-xl border p-4 flex flex-col items-center gap-2 text-center {{ $ya ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-100' }}">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center {{ $ya ? 'bg-green-100' : 'bg-gray-100' }}">
                                @if($ya)
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                            </div>
                            <p class="text-xs font-bold {{ $ya ? 'text-green-700' : 'text-gray-400' }}">{{ $cert['label'] }}</p>
                            <p class="text-xs {{ $ya ? 'text-green-600' : 'text-gray-300' }}">{{ $cert['value'] ?? 'Tidak diisi' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
