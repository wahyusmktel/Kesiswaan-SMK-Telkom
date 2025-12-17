<div>
    <x-input-label for="tahun_pelajaran_id" :value="__('Tahun Pelajaran')" />
    <select name="tahun_pelajaran_id" id="tahun_pelajaran_id"
        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        required>
        <option value="">-- Pilih Tahun --</option>
        @foreach ($tahun_pelajaran as $tp)
            <option value="{{ $tp->id }}"
                {{ old('tahun_pelajaran_id', $rombel->tahun_pelajaran_id ?? $tahun_aktif_id) == $tp->id ? 'selected' : '' }}>
                {{ $tp->tahun }} - {{ $tp->semester }} {{ $tp->is_active ? '(Aktif)' : '' }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('tahun_pelajaran_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="kelas_id" :value="__('Pilih Kelas')" />
    <select name="kelas_id" id="kelas_id"
        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        required>
        <option value="">-- Pilih Kelas --</option>
        @foreach ($kelas as $id => $nama)
            <option value="{{ $id }}" {{ old('kelas_id', $rombel->kelas_id ?? '') == $id ? 'selected' : '' }}>
                {{ $nama }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('kelas_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="wali_kelas_id" :value="__('Pilih Wali Kelas')" />
    <select name="wali_kelas_id" id="wali_kelas_id"
        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        required>
        <option value="">-- Pilih Wali Kelas --</option>
        @foreach ($wali_kelas as $id => $nama)
            <option value="{{ $id }}"
                {{ old('wali_kelas_id', $rombel->wali_kelas_id ?? '') == $id ? 'selected' : '' }}>
                {{ $nama }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('wali_kelas_id')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-4">
    <a href="{{ route('master-data.rombel.index') }}">
        <x-secondary-button type="button">{{ __('Batal') }}</x-secondary-button>
    </a>
    <x-primary-button class="ms-4">{{ __('Simpan') }}</x-primary-button>
</div>
