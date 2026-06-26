@php
    $current = $industri ?? null;
    $prefix = $prefix ?? 'industri';
@endphp

<div
    class="space-y-5"
    x-data="prakerinIndustryForm({
        prefix: @js($prefix),
        initialAddress: @js(old('alamat', $current->alamat ?? '')),
        initialLat: @js(old('latitude', $current->latitude ?? null)),
        initialLng: @js(old('longitude', $current->longitude ?? null)),
        provinceCode: @js(old('provinsi_code', $current->provinsi_code ?? '')),
        provinceName: @js(old('provinsi_name', $current->provinsi_name ?? '')),
        regencyCode: @js(old('kabupaten_code', $current->kabupaten_code ?? '')),
        regencyName: @js(old('kabupaten_name', $current->kabupaten_name ?? $current->kota ?? '')),
        districtCode: @js(old('kecamatan_code', $current->kecamatan_code ?? '')),
        districtName: @js(old('kecamatan_name', $current->kecamatan_name ?? '')),
        villageCode: @js(old('desa_code', $current->desa_code ?? '')),
        villageName: @js(old('desa_name', $current->desa_name ?? '')),
    })"
    x-init="init()"
>
    <div>
        <x-input-label :for="$prefix . '_nama_industri'" value="Nama Industri" />
        <x-text-input :id="$prefix . '_nama_industri'" class="block mt-1 w-full"
            type="text" name="nama_industri" :value="old('nama_industri', $current->nama_industri ?? '')" required />
    </div>

    <div class="grid gap-4 lg:grid-cols-4">
        <div>
            <x-input-label :for="$prefix . '_provinsi'" value="Provinsi" />
            <select :id="prefix + '_provinsi'" x-model="provinceCode" @change="onProvinceChange"
                class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring focus:ring-red-100">
                <option value="">Pilih provinsi</option>
                <template x-for="province in provinces" :key="province.code">
                    <option :value="province.code" x-text="province.name"></option>
                </template>
            </select>
        </div>
        <div>
            <x-input-label :for="$prefix . '_kabupaten'" value="Kota/Kabupaten" />
            <select :id="prefix + '_kabupaten'" x-model="regencyCode" @change="onRegencyChange"
                class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring focus:ring-red-100">
                <option value="" x-text="loading.regencies ? 'Memuat...' : 'Pilih kota/kabupaten'"></option>
                <template x-for="regency in regencies" :key="regency.code">
                    <option :value="regency.code" x-text="regency.name"></option>
                </template>
            </select>
        </div>
        <div>
            <x-input-label :for="$prefix . '_kecamatan'" value="Kecamatan" />
            <select :id="prefix + '_kecamatan'" x-model="districtCode" @change="onDistrictChange"
                class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring focus:ring-red-100">
                <option value="" x-text="loading.districts ? 'Memuat...' : 'Pilih kecamatan'"></option>
                <template x-for="district in districts" :key="district.code">
                    <option :value="district.code" x-text="district.name"></option>
                </template>
            </select>
        </div>
        <div>
            <x-input-label :for="$prefix . '_desa'" value="Desa/Kelurahan" />
            <select :id="prefix + '_desa'" x-model="villageCode" @change="onVillageChange"
                class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring focus:ring-red-100">
                <option value="" x-text="loading.villages ? 'Memuat...' : 'Pilih desa/kelurahan'"></option>
                <template x-for="village in villages" :key="village.code">
                    <option :value="village.code" x-text="village.name"></option>
                </template>
            </select>
        </div>
    </div>

    <input type="hidden" name="kota" :value="regencyName">
    <input type="hidden" name="provinsi_code" :value="provinceCode">
    <input type="hidden" name="provinsi_name" :value="provinceName">
    <input type="hidden" name="kabupaten_code" :value="regencyCode">
    <input type="hidden" name="kabupaten_name" :value="regencyName">
    <input type="hidden" name="kecamatan_code" :value="districtCode">
    <input type="hidden" name="kecamatan_name" :value="districtName">
    <input type="hidden" name="desa_code" :value="villageCode">
    <input type="hidden" name="desa_name" :value="villageName">
    <input type="hidden" name="latitude" :value="latitude">
    <input type="hidden" name="longitude" :value="longitude">

    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
        <div class="mb-3">
            <x-input-label :for="$prefix . '_alamat'" value="Alamat Lengkap" />
            <textarea :id="prefix + '_alamat'" name="alamat" rows="3" x-model="address" required
                class="mt-1 w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-300 focus:ring focus:ring-red-100"
                placeholder="Klik peta atau cari lokasi untuk mengisi alamat otomatis. Alamat tetap bisa diedit manual."></textarea>
        </div>

        <div class="mb-3 flex flex-col gap-2 sm:flex-row">
            <input type="text" x-model="searchQuery" @keydown.enter.prevent="searchLocation"
                class="flex-1 rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring focus:ring-red-100"
                placeholder="Cari nama industri, jalan, atau landmark...">
            <button type="button" @click="searchLocation"
                class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                Cari di Map
            </button>
            <button type="button" @click="useBrowserLocation"
                class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                Lokasi Saya
            </button>
        </div>

        <div class="mb-3 rounded-xl border border-red-100 bg-white px-4 py-3 text-sm text-gray-700">
            <p class="text-xs font-bold uppercase tracking-wide text-red-600">Alamat dari Pin</p>
            <p class="mt-1" x-text="address || 'Belum ada alamat. Klik peta atau geser pin lokasi industri.'"></p>
        </div>

        <div :id="prefix + '_map'" class="h-80 w-full rounded-2xl border border-gray-200 bg-gray-100"></div>
        <p class="mt-2 text-xs text-gray-500">
            Titik koordinat: <span class="font-semibold" x-text="latitude || '-'"></span>,
            <span class="font-semibold" x-text="longitude || '-'"></span>
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label :for="$prefix . '_telepon'" value="Telepon" />
            <x-text-input :id="$prefix . '_telepon'" class="block mt-1 w-full"
                type="text" name="telepon" :value="old('telepon', $current->telepon ?? '')" />
        </div>
        <div>
            <x-input-label :for="$prefix . '_email_pic'" value="Email PIC" />
            <x-text-input :id="$prefix . '_email_pic'" class="block mt-1 w-full"
                type="email" name="email_pic" :value="old('email_pic', $current->email_pic ?? '')" />
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label :for="$prefix . '_nama_pic'" value="Nama PIC (PIC Di Industri)" />
            <x-text-input :id="$prefix . '_nama_pic'" class="block mt-1 w-full"
                type="text" name="nama_pic" :value="old('nama_pic', $current->nama_pic ?? '')" />
        </div>
        <div>
            <x-input-label :for="$prefix . '_nomor_mou'" value="Nomor MOU" />
            <x-text-input :id="$prefix . '_nomor_mou'" class="block mt-1 w-full"
                type="text" name="nomor_mou" :value="old('nomor_mou', $current->nomor_mou ?? '')" />
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label :for="$prefix . '_tanggal_mou'" value="Tanggal MOU" />
            <x-text-input :id="$prefix . '_tanggal_mou'" class="block mt-1 w-full"
                type="date" name="tanggal_mou" :value="old('tanggal_mou', optional($current->tanggal_mou ?? null)->format('Y-m-d'))" />
        </div>
        <div>
            <x-input-label :for="$prefix . '_tanggal_akhir_mou'" value="Akhir MOU" />
            <x-text-input :id="$prefix . '_tanggal_akhir_mou'" class="block mt-1 w-full"
                type="date" name="tanggal_akhir_mou" :value="old('tanggal_akhir_mou', optional($current->tanggal_akhir_mou ?? null)->format('Y-m-d'))" />
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
        <input type="checkbox" name="is_mou_active" value="1" @checked(old('is_mou_active', $current->is_mou_active ?? true)) class="rounded border-gray-300 text-red-600 focus:ring-red-500">
        MOU Aktif
    </label>

    <div>
        <x-input-label :for="$prefix . '_catatan_mou'" value="Catatan MOU" />
        <textarea id="{{ $prefix }}_catatan_mou" name="catatan_mou" rows="2"
            class="mt-1 w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-300 focus:ring focus:ring-red-100">{{ old('catatan_mou', $current->catatan_mou ?? '') }}</textarea>
    </div>
</div>
