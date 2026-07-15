@php
    $fieldClass = 'mt-1 block h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500';
    $errorClass = ' border-red-500 focus:border-red-500 focus:ring-red-500';
@endphp

<form method="POST" action="{{ $formAction }}" class="space-y-8">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    @if($errors->any())
        <div class="border-l-4 border-red-600 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <p class="font-bold">Data belum dapat disimpan.</p>
            <p class="mt-1">Periksa kembali kolom yang ditandai merah.</p>
        </div>
    @endif

    <section class="space-y-5 border-b border-gray-200 pb-8">
        <div>
            <p class="text-xs font-black uppercase text-red-700">Identitas Pembelajaran</p>
            <h2 class="mt-1 text-xl font-black text-gray-950">Data utama modul ajar</h2>
            <p class="mt-1 text-sm text-gray-500">Data ini menjadi sampul dan tabel identitas pada PDF.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div>
                <label for="program_keahlian" class="text-sm font-bold text-gray-800">Program Keahlian</label>
                <input id="program_keahlian" name="program_keahlian" type="text" list="program-keahlian-options"
                    value="{{ old('program_keahlian', $module->program_keahlian) }}" required
                    placeholder="Contoh: Teknik Komputer dan Jaringan"
                    class="{{ $fieldClass }}{{ $errors->has('program_keahlian') ? $errorClass : '' }}">
                <datalist id="program-keahlian-options">
                    @foreach($programs as $program)
                        <option value="{{ $program }}"></option>
                    @endforeach
                </datalist>
                @error('program_keahlian')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="mata_pelajaran_id" class="text-sm font-bold text-gray-800">Mata Pelajaran</label>
                <select id="mata_pelajaran_id" name="mata_pelajaran_id" required
                    class="{{ $fieldClass }}{{ $errors->has('mata_pelajaran_id') ? $errorClass : '' }}">
                    <option value="">Pilih mata pelajaran</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected((string) old('mata_pelajaran_id', $module->mata_pelajaran_id) === (string) $subject->id)>
                            {{ $subject->nama_mapel }}{{ $subject->kode_mapel ? ' · '.$subject->kode_mapel : '' }}
                        </option>
                    @endforeach
                </select>
                @error('mata_pelajaran_id')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="fase" class="text-sm font-bold text-gray-800">Fase</label>
                <select id="fase" name="fase" required
                    class="{{ $fieldClass }}{{ $errors->has('fase') ? $errorClass : '' }}">
                    @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $phase)
                        <option value="{{ $phase }}" @selected(old('fase', $module->fase) === $phase)>Fase {{ $phase }}</option>
                    @endforeach
                </select>
                @error('fase')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="nama_penyusun" class="text-sm font-bold text-gray-800">Nama Penyusun</label>
                <input id="nama_penyusun" name="nama_penyusun" type="text"
                    value="{{ old('nama_penyusun', $module->nama_penyusun) }}" required
                    class="{{ $fieldClass }}{{ $errors->has('nama_penyusun') ? $errorClass : '' }}">
                @error('nama_penyusun')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="instansi" class="text-sm font-bold text-gray-800">Instansi</label>
                <input id="instansi" name="instansi" type="text"
                    value="{{ old('instansi', $module->instansi) }}" required
                    class="{{ $fieldClass }}{{ $errors->has('instansi') ? $errorClass : '' }}">
                @error('instansi')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="tahun_pelajaran_id" class="text-sm font-bold text-gray-800">Tahun Pelajaran</label>
                <select id="tahun_pelajaran_id" name="tahun_pelajaran_id" required
                    class="{{ $fieldClass }}{{ $errors->has('tahun_pelajaran_id') ? $errorClass : '' }}">
                    <option value="">Pilih periode</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" @selected((string) old('tahun_pelajaran_id', $module->tahun_pelajaran_id) === (string) $year->id)>
                            {{ $year->tahun }} · {{ $year->semester }}{{ $year->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('tahun_pelajaran_id')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <section class="space-y-5 border-b border-gray-200 pb-8">
        <div>
            <p class="text-xs font-black uppercase text-red-700">Informasi Modul</p>
            <h2 class="mt-1 text-xl font-black text-gray-950">Konteks dan cakupan</h2>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <label for="nama_modul" class="text-sm font-bold text-gray-800">Nama Modul</label>
                <input id="nama_modul" name="nama_modul" type="text"
                    value="{{ old('nama_modul', $module->nama_modul) }}" required
                    placeholder="Contoh: Konsep Dasar Cloud Computing"
                    class="{{ $fieldClass }}{{ $errors->has('nama_modul') ? $errorClass : '' }}">
                @error('nama_modul')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="kode_modul" class="text-sm font-bold text-gray-800">Kode Modul</label>
                <input id="kode_modul" name="kode_modul" type="text"
                    value="{{ old('kode_modul', $module->kode_modul) }}" required
                    placeholder="MA-1.1"
                    class="{{ $fieldClass }} font-mono uppercase{{ $errors->has('kode_modul') ? $errorClass : '' }}">
                <p class="mt-1 text-xs text-gray-500">Contoh format: MA-1.1</p>
                @error('kode_modul')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="alokasi_waktu" class="text-sm font-bold text-gray-800">Alokasi Waktu</label>
                <select id="alokasi_waktu" name="alokasi_waktu" required
                    class="{{ $fieldClass }}{{ $errors->has('alokasi_waktu') ? $errorClass : '' }}">
                    @foreach($allocationOptions as $allocation)
                        <option value="{{ $allocation }}" @selected(old('alokasi_waktu', $module->alokasi_waktu) === $allocation)>{{ $allocation }}</option>
                    @endforeach
                </select>
                @error('alokasi_waktu')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="jenjang" class="text-sm font-bold text-gray-800">Jenjang</label>
                <select id="jenjang" name="jenjang" required
                    class="{{ $fieldClass }}{{ $errors->has('jenjang') ? $errorClass : '' }}">
                    @foreach(['SD', 'SMP', 'SMA', 'SMK', 'MA', 'MAK'] as $level)
                        <option value="{{ $level }}" @selected(old('jenjang', $module->jenjang) === $level)>{{ $level }}</option>
                    @endforeach
                </select>
                @error('jenjang')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="kelas" class="text-sm font-bold text-gray-800">Kelas</label>
                <select id="kelas" name="kelas" required
                    class="{{ $fieldClass }}{{ $errors->has('kelas') ? $errorClass : '' }}">
                    @foreach(['X', 'XI', 'XII', 'XIII'] as $classLevel)
                        <option value="{{ $classLevel }}" @selected(old('kelas', $module->kelas) === $classLevel)>Kelas {{ $classLevel }}</option>
                    @endforeach
                </select>
                @error('kelas')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="jumlah_murid" class="text-sm font-bold text-gray-800">Jumlah Murid</label>
                <select id="jumlah_murid" name="jumlah_murid" required
                    class="{{ $fieldClass }}{{ $errors->has('jumlah_murid') ? $errorClass : '' }}">
                    <option value="Disesuaikan" @selected(old('jumlah_murid', $module->jumlah_murid) === 'Disesuaikan')>Disesuaikan</option>
                    @foreach($studentCountOptions as $count)
                        <option value="{{ $count }}" @selected((string) old('jumlah_murid', $module->jumlah_murid) === (string) $count)>{{ $count }} murid</option>
                    @endforeach
                </select>
                @error('jumlah_murid')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="lg:col-span-2">
                <label for="lingkup_materi" class="text-sm font-bold text-gray-800">Lingkup Materi</label>
                <textarea id="lingkup_materi" name="lingkup_materi" rows="4" required
                    placeholder="Jelaskan lingkup materi yang dibahas dalam modul ini."
                    class="mt-1 block w-full resize-y rounded-md border-gray-300 text-sm leading-6 shadow-sm focus:border-red-500 focus:ring-red-500{{ $errors->has('lingkup_materi') ? $errorClass : '' }}">{{ old('lingkup_materi', $module->lingkup_materi) }}</textarea>
                @error('lingkup_materi')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('guru-kelas.teaching-module.index') }}"
            class="inline-flex h-11 items-center justify-center rounded-md border border-gray-300 bg-white px-5 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
            Batal
        </a>
        <button type="submit"
            class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-red-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ $submitLabel }}
        </button>
    </div>
</form>
