<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('wali-kelas.buku-induk.index', ['rombel_id' => $rombel->id]) }}" title="Kembali"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $student->nama_lengkap }}</h2>
                    <p class="text-sm text-gray-500">Buku Induk · {{ $rombel->kelas?->nama_kelas }} · NIS {{ $student->nis }}</p>
                </div>
            </div>
            <a href="{{ route('wali-kelas.buku-induk.print-student', $student) }}" target="_blank"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12-5h12v9H6v-9z"/></svg>
                Pratinjau PDF
            </a>
        </div>
    </x-slot>

    @php
        $errorFields = $errors->keys();
        $initialTab = collect($errorFields)->contains(fn ($field) => str_starts_with($field, 'grades') || str_starts_with($field, 'extracurriculars') || in_array($field, ['school_year', 'semester', 'sick_days', 'permitted_days', 'absent_days', 'conduct', 'development_notes'], true))
            ? 'academic'
            : (collect($errorFields)->contains(fn ($field) => str_starts_with($field, 'files') || in_array($field, ['category', 'title'], true)) ? 'attachments' : ($errors->any() ? 'history' : 'identity'));
    @endphp
    <div class="w-full px-4 py-6 sm:px-6 lg:px-8" x-data="{ tab: @js($initialTab) }">
        @if(session('success'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-bold">Ada data yang perlu diperbaiki:</p>
                <ul class="mt-1 list-disc pl-5"><li>{{ $errors->first() }}</li></ul>
            </div>
        @endif

        <div class="mb-6 overflow-x-auto border-b border-gray-200">
            <nav class="flex min-w-max gap-1" aria-label="Bagian Buku Induk">
                @foreach([
                    'identity' => 'Identitas & Keluarga',
                    'history' => 'Penerimaan & Riwayat',
                    'academic' => 'Akademik',
                    'attachments' => 'Lampiran',
                ] as $key => $label)
                    <button type="button" @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-red-600 text-red-700' : 'border-transparent text-gray-500 hover:text-gray-800'"
                        class="border-b-2 px-4 py-3 text-sm font-bold">{{ $label }}</button>
                @endforeach
            </nav>
        </div>

        <section x-show="tab === 'identity'" x-cloak>
            <div class="mb-6 grid gap-6 xl:grid-cols-2">
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-base font-bold text-gray-900">Identitas Peserta Didik</h3>
                    <p class="mb-4 mt-1 text-sm text-gray-500">Bersumber dari Master Siswa dan Dapodik.</p>
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                        @foreach([
                            'Nama lengkap' => $student->nama_lengkap,
                            'NIS / NIPD' => $student->nis.' / '.($student->dapodik?->nipd ?: '-'),
                            'NISN' => $student->dapodik?->nisn,
                            'NIK' => $student->dapodik?->nik,
                            'Tempat, tanggal lahir' => trim(($student->tempat_lahir ?: '-').', '.($student->tanggal_lahir?->translatedFormat('d F Y') ?: '-')),
                            'Jenis kelamin' => $student->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                            'Agama' => $student->dapodik?->agama,
                            'Nomor KK' => $student->dapodik?->no_kk,
                            'Nomor akta lahir' => $student->dapodik?->no_registrasi_akta_lahir,
                            'Alamat' => $student->dapodik?->alamat ?: $student->alamat,
                            'Telepon / HP' => $student->dapodik?->hp ?: $student->dapodik?->telepon,
                            'Email' => $student->dapodik?->email,
                        ] as $label => $value)
                            <div>
                                <dt class="text-xs font-bold uppercase text-gray-400">{{ $label }}</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-800">{{ filled($value) ? $value : '-' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-base font-bold text-gray-900">Orang Tua dan Wali</h3>
                    <p class="mb-4 mt-1 text-sm text-gray-500">Data keluarga tersinkron dari Dapodik.</p>
                    @foreach([
                        'Ayah' => [$student->dapodik?->nama_ayah, $student->dapodik?->nik_ayah, $student->dapodik?->pekerjaan_ayah, $student->dapodik?->jenjang_pendidikan_ayah],
                        'Ibu' => [$student->dapodik?->nama_ibu, $student->dapodik?->nik_ibu, $student->dapodik?->pekerjaan_ibu, $student->dapodik?->jenjang_pendidikan_ibu],
                        'Wali' => [$student->dapodik?->nama_wali, $student->dapodik?->nik_wali, $student->dapodik?->pekerjaan_wali, $student->dapodik?->jenjang_pendidikan_wali],
                    ] as $relation => $values)
                        <div class="mb-3 grid grid-cols-2 gap-3 rounded-lg border border-gray-200 p-3 sm:grid-cols-4">
                            <div><p class="text-xs font-bold uppercase text-gray-400">{{ $relation }}</p><p class="mt-1 text-sm font-bold">{{ $values[0] ?: '-' }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-400">NIK</p><p class="mt-1 text-sm">{{ $values[1] ?: '-' }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-400">Pekerjaan</p><p class="mt-1 text-sm">{{ $values[2] ?: '-' }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-400">Pendidikan</p><p class="mt-1 text-sm">{{ $values[3] ?: '-' }}</p></div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                Koreksi identitas resmi tetap dilakukan melalui menu Dapodik agar satu sumber data tetap konsisten. Bagian pelengkap Buku Induk dapat diisi pada tab berikutnya.
            </div>
        </section>

        <section x-show="tab === 'history'" x-cloak>
            <form method="POST" action="{{ route('wali-kelas.buku-induk.update', $student) }}" class="space-y-8">
                @csrf @method('PUT')
                <div>
                    <h3 class="text-base font-bold text-gray-900">Penerimaan Peserta Didik</h3>
                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <x-buku-induk.input name="admission_date" label="Tanggal Diterima" type="date" :value="$book->admission_date?->format('Y-m-d')" />
                        <x-buku-induk.input name="admission_status" label="Status Penerimaan" :value="$book->admission_status" placeholder="Siswa baru / pindahan" />
                        <x-buku-induk.input name="previous_school" label="Sekolah Asal" :value="$book->previous_school ?: $student->dapodik?->sekolah_asal" />
                        <x-buku-induk.input name="additional_data[accepted_grade]" label="Diterima di Tingkat" :value="data_get($book->additional_data, 'accepted_grade')" placeholder="X / XI / XII" />
                        <x-buku-induk.input name="previous_diploma_number" label="Nomor Ijazah Sebelumnya" :value="$book->previous_diploma_number" />
                        <x-buku-induk.input name="previous_diploma_date" label="Tanggal Ijazah" type="date" :value="$book->previous_diploma_date?->format('Y-m-d')" />
                        <x-buku-induk.input name="additional_data[accepted_program]" label="Program/Konsentrasi Keahlian" :value="data_get($book->additional_data, 'accepted_program')" />
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-7">
                    <h3 class="text-base font-bold text-gray-900">Data Pribadi Pelengkap dan Kesehatan</h3>
                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <x-buku-induk.input name="additional_data[citizenship]" label="Kewarganegaraan" :value="data_get($book->additional_data, 'citizenship')" />
                        <x-buku-induk.input name="additional_data[child_status]" label="Status dalam Keluarga" :value="data_get($book->additional_data, 'child_status')" />
                        <x-buku-induk.input name="additional_data[daily_language]" label="Bahasa Sehari-hari" :value="data_get($book->additional_data, 'daily_language')" />
                        <x-buku-induk.input name="additional_data[guardian_phone]" label="Nomor Orang Tua/Wali" :value="data_get($book->additional_data, 'guardian_phone')" />
                        <x-buku-induk.input name="additional_data[hobby]" label="Hobi" :value="data_get($book->additional_data, 'hobby')" />
                        <x-buku-induk.input name="additional_data[aspiration]" label="Cita-cita" :value="data_get($book->additional_data, 'aspiration')" />
                        <label><span class="mb-1 block text-sm font-bold text-gray-700">Golongan Darah</span><select name="blood_type" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">@foreach(['' => 'Belum diketahui', 'A'=>'A','B'=>'B','AB'=>'AB','O'=>'O','-'=>'Lainnya'] as $value => $label)<option value="{{ $value }}" @selected($book->blood_type === $value)>{{ $label }}</option>@endforeach</select></label>
                    </div>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <x-buku-induk.textarea name="medical_history" label="Riwayat Penyakit" :value="$book->medical_history" />
                        <x-buku-induk.textarea name="special_needs_notes" label="Kebutuhan Khusus" :value="$book->special_needs_notes ?: $student->dapodik?->kebutuhan_khusus" />
                        <x-buku-induk.textarea name="additional_data[health_notes]" label="Catatan Pemeriksaan Kesehatan" :value="data_get($book->additional_data, 'health_notes')" />
                        <x-buku-induk.textarea name="additional_data[education_notes]" label="Catatan Perkembangan Pendidikan" :value="data_get($book->additional_data, 'education_notes')" />
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-7">
                    <h3 class="text-base font-bold text-gray-900">Mutasi, Kelulusan, dan Catatan Akhir</h3>
                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <label><span class="mb-1 block text-sm font-bold text-gray-700">Status Siswa</span><select name="student_status" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">@foreach(['aktif'=>'Aktif','pindah'=>'Pindah','lulus'=>'Lulus','keluar'=>'Keluar'] as $value => $label)<option value="{{ $value }}" @selected($book->student_status === $value)>{{ $label }}</option>@endforeach</select></label>
                        <x-buku-induk.input name="transfer_date" label="Tanggal Pindah/Keluar" type="date" :value="$book->transfer_date?->format('Y-m-d')" />
                        <x-buku-induk.input name="transfer_destination" label="Sekolah Tujuan" :value="$book->transfer_destination" />
                        <x-buku-induk.input name="graduation_date" label="Tanggal Kelulusan" type="date" :value="$book->graduation_date?->format('Y-m-d')" />
                        <x-buku-induk.input name="graduation_certificate_number" label="Nomor Ijazah Kelulusan" :value="$book->graduation_certificate_number" />
                    </div>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <x-buku-induk.textarea name="transfer_reason" label="Alasan Pindah/Keluar" :value="$book->transfer_reason" />
                        <x-buku-induk.textarea name="homeroom_notes" label="Catatan Wali Kelas" :value="$book->homeroom_notes" />
                    </div>
                </div>

                <div class="sticky bottom-0 flex flex-col gap-3 border-t border-gray-200 bg-white/95 py-4 backdrop-blur sm:flex-row sm:items-center sm:justify-between">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="mark_complete" value="1" @checked($book->completed_at) class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        Tandai data utama telah diperiksa dan lengkap
                    </label>
                    <button class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">Simpan Data Utama</button>
                </div>
            </form>
        </section>

        <section x-show="tab === 'academic'" x-cloak>
            <div class="mb-5 flex items-start justify-between gap-4">
                <div><h3 class="text-base font-bold text-gray-900">Riwayat Akademik per Semester</h3><p class="mt-1 text-sm text-gray-500">Nilai, kegiatan ekstrakurikuler, ketidakhadiran, dan perkembangan siswa.</p></div>
            </div>
            <div class="space-y-4">
                @foreach($book->periods as $period)
                    <details class="rounded-lg border border-gray-200 bg-white" @if($errors->has('period_id')) open @endif>
                        <summary class="cursor-pointer px-5 py-4 font-bold text-gray-800">{{ $period->school_year }} · Semester {{ $period->semester }} <span class="ml-2 text-xs font-medium text-gray-400">{{ count($period->grades ?? []) }} mata pelajaran</span></summary>
                        <div class="border-t border-gray-200 p-5">
                            @include('pages.wali-kelas.buku-induk.partials.period-form', ['period' => $period])
                            <form method="POST" action="{{ route('wali-kelas.buku-induk.periods.destroy', [$student, $period]) }}" class="mt-3 text-right" onsubmit="return confirm('Hapus riwayat semester ini?')">
                                @csrf @method('DELETE')
                                <button class="text-sm font-bold text-red-600 hover:text-red-800">Hapus Semester</button>
                            </form>
                        </div>
                    </details>
                @endforeach
                <details class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50" open>
                    <summary class="cursor-pointer px-5 py-4 font-bold text-gray-800">Tambah Riwayat Semester</summary>
                    <div class="border-t border-gray-200 p-5">@include('pages.wali-kelas.buku-induk.partials.period-form', ['period' => null])</div>
                </details>
            </div>
        </section>

        <section x-show="tab === 'attachments'" x-cloak>
            <div class="grid gap-8 xl:grid-cols-[minmax(300px,420px)_1fr]">
                <form method="POST" action="{{ route('wali-kelas.buku-induk.attachments.store', $student) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div><h3 class="text-base font-bold text-gray-900">Unggah Lampiran</h3><p class="mt-1 text-sm text-gray-500">PDF/JPG/PNG/WebP, maksimal 10 MB per berkas.</p></div>
                    <label><span class="mb-1 block text-sm font-bold text-gray-700">Jenis Dokumen</span><select name="category" required class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">@foreach(['foto'=>'Foto Siswa','akta'=>'Akta Kelahiran','kartu_keluarga'=>'Kartu Keluarga','ijazah'=>'Ijazah Sebelumnya','kesehatan'=>'Dokumen Kesehatan','prestasi'=>'Sertifikat Prestasi','mutasi'=>'Dokumen Mutasi','lainnya'=>'Dokumen Lainnya'] as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label>
                    <x-buku-induk.input name="title" label="Judul Lampiran" placeholder="Contoh: Akta kelahiran asli" />
                    <label class="block cursor-pointer rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center hover:border-red-400 hover:bg-red-50">
                        <svg class="mx-auto h-9 w-9 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.9A5 5 0 1115.9 6H16a5 5 0 011 9.9M12 12v9m0-9-3 3m3-3 3 3"/></svg>
                        <span class="mt-2 block text-sm font-bold text-gray-700">Pilih satu atau beberapa berkas</span>
                        <input type="file" name="files[]" multiple required accept=".pdf,.jpg,.jpeg,.png,.webp" class="sr-only">
                    </label>
                    <button class="w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">Unggah Lampiran</button>
                </form>

                <div>
                    <h3 class="text-base font-bold text-gray-900">Dokumen Tersimpan</h3>
                    <div class="mt-4 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white">
                        @forelse($book->attachments as $attachment)
                            <div class="flex items-center gap-3 p-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-xs font-black uppercase text-gray-600">{{ pathinfo($attachment->original_name, PATHINFO_EXTENSION) }}</div>
                                <div class="min-w-0 flex-1"><p class="truncate text-sm font-bold text-gray-900">{{ $attachment->title }}</p><p class="truncate text-xs text-gray-500">{{ $attachment->original_name }} · {{ number_format($attachment->file_size / 1024, 0) }} KB</p></div>
                                <a href="{{ route('wali-kelas.buku-induk.attachments.download', [$student, $attachment]) }}" title="Unduh" class="p-2 text-gray-500 hover:text-red-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/></svg></a>
                                <form method="POST" action="{{ route('wali-kelas.buku-induk.attachments.destroy', [$student, $attachment]) }}" onsubmit="return confirm('Hapus lampiran ini?')">@csrf @method('DELETE')<button title="Hapus" class="p-2 text-gray-500 hover:text-red-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14"/></svg></button></form>
                            </div>
                        @empty
                            <p class="p-10 text-center text-sm text-gray-500">Belum ada lampiran.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
