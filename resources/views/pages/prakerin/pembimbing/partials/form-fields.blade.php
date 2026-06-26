@php
    $current = $pembimbing ?? null;
    $selectedGuru = old('master_guru_id', $current->master_guru_id ?? '');
    $selectedIndustri = old('prakerin_industri_id', $current->prakerin_industri_id ?? '');
@endphp

<div class="grid gap-4 lg:grid-cols-4">
    <div>
        <label for="{{ $prefix }}_tipe" class="block text-xs font-bold uppercase tracking-wide text-gray-500">Tipe Pembimbing</label>
        <select id="{{ $prefix }}_tipe" name="tipe" x-model="tipe"
            class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring-red-100" required>
            <option value="internal">Pembimbing Internal</option>
            <option value="external">Pembimbing External</option>
        </select>
    </div>

    <div x-show="tipe === 'internal'" x-cloak class="lg:col-span-2">
        <label for="{{ $prefix }}_master_guru_id" class="block text-xs font-bold uppercase tracking-wide text-gray-500">Guru Internal</label>
        <select id="{{ $prefix }}_master_guru_id" name="master_guru_id" :required="tipe === 'internal'" class="js-search-select mt-1 w-full" data-placeholder="Cari guru internal...">
            <option value="">Pilih guru internal</option>
            @foreach($guru as $g)
                <option value="{{ $g->id }}" @selected((string) $selectedGuru === (string) $g->id)>{{ $g->nama_lengkap }}</option>
            @endforeach
        </select>
    </div>

    <div x-show="tipe === 'internal'" x-cloak>
        <label class="block text-xs font-bold uppercase tracking-wide text-gray-500">Asal Sekolah</label>
        <div class="mt-1 flex min-h-[42px] items-center rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm font-semibold text-gray-700">
            {{ $schoolName }}
        </div>
    </div>

    <div x-show="tipe === 'external'" x-cloak class="lg:col-span-2">
        <label for="{{ $prefix }}_nama" class="block text-xs font-bold uppercase tracking-wide text-gray-500">Nama Pembimbing External</label>
        <input id="{{ $prefix }}_nama" name="nama" value="{{ old('nama', $current->nama ?? '') }}"
            :required="tipe === 'external'"
            class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring-red-100"
            placeholder="Nama pembimbing dari industri">
    </div>

    <div x-show="tipe === 'external'" x-cloak>
        <label for="{{ $prefix }}_prakerin_industri_id" class="block text-xs font-bold uppercase tracking-wide text-gray-500">Asal Industri</label>
        <select id="{{ $prefix }}_prakerin_industri_id" name="prakerin_industri_id" :required="tipe === 'external'" class="js-search-select mt-1 w-full" data-placeholder="Cari industri...">
            <option value="">Pilih industri</option>
            @foreach($industri as $i)
                <option value="{{ $i->id }}" @selected((string) $selectedIndustri === (string) $i->id)>{{ $i->nama_industri }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid gap-4 lg:grid-cols-4">
    <div>
        <label for="{{ $prefix }}_jabatan" class="block text-xs font-bold uppercase tracking-wide text-gray-500">Jabatan</label>
        <input id="{{ $prefix }}_jabatan" name="jabatan" value="{{ old('jabatan', $current->jabatan ?? '') }}"
            class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring-red-100"
            placeholder="Contoh: HRD / Guru Produktif">
    </div>
    <div>
        <label for="{{ $prefix }}_telepon" class="block text-xs font-bold uppercase tracking-wide text-gray-500">Telepon</label>
        <input id="{{ $prefix }}_telepon" name="telepon" value="{{ old('telepon', $current->telepon ?? '') }}"
            class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring-red-100"
            placeholder="Nomor telepon">
    </div>
    <div>
        <label for="{{ $prefix }}_email" class="block text-xs font-bold uppercase tracking-wide text-gray-500">Email</label>
        <input id="{{ $prefix }}_email" type="email" name="email" value="{{ old('email', $current->email ?? '') }}"
            class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring-red-100"
            placeholder="email@domain.com">
    </div>
    <label class="flex items-end gap-2 pb-2 text-sm font-semibold text-gray-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $current->is_active ?? true))
            class="rounded border-gray-300 text-red-600 focus:ring-red-500">
        Aktif
    </label>
</div>
