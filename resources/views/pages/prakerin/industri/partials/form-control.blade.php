<div class="space-y-4">
    <div><x-input-label for="nama_industri" value="Nama Industri" /><x-text-input id="nama_industri"
            class="block mt-1 w-full" type="text" name="nama_industri" :value="old('nama_industri', $industri->nama_industri ?? '')" required autofocus /></div>
    <div><x-input-label for="alamat" value="Alamat Lengkap" />
        <textarea name="alamat" id="alamat" rows="3" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('alamat', $industri->alamat ?? '') }}</textarea>
    </div>
    <div><x-input-label for="kota" value="Kota/Kabupaten" /><x-text-input id="kota" class="block mt-1 w-full"
            type="text" name="kota" :value="old('kota', $industri->kota ?? '')" required /></div>
    <div><x-input-label for="telepon" value="Telepon" /><x-text-input id="telepon" class="block mt-1 w-full"
            type="text" name="telepon" :value="old('telepon', $industri->telepon ?? '')" /></div>
    <div><x-input-label for="nama_pic" value="Nama PIC (PIC Di Industri)" /><x-text-input id="nama_pic"
            class="block mt-1 w-full" type="text" name="nama_pic" :value="old('nama_pic', $industri->nama_pic ?? '')" /></div>
    <div><x-input-label for="email_pic" value="Email PIC" /><x-text-input id="email_pic" class="block mt-1 w-full"
            type="email" name="email_pic" :value="old('email_pic', $industri->email_pic ?? '')" /></div>
    <div><x-input-label for="nomor_mou" value="Nomor MOU" /><x-text-input id="nomor_mou" class="block mt-1 w-full"
            type="text" name="nomor_mou" :value="old('nomor_mou', $industri->nomor_mou ?? '')" /></div>
    <div class="grid grid-cols-2 gap-3">
        <div><x-input-label for="tanggal_mou" value="Tanggal MOU" /><x-text-input id="tanggal_mou" class="block mt-1 w-full"
                type="date" name="tanggal_mou" :value="old('tanggal_mou', optional($industri->tanggal_mou ?? null)->format('Y-m-d'))" /></div>
        <div><x-input-label for="tanggal_akhir_mou" value="Akhir MOU" /><x-text-input id="tanggal_akhir_mou" class="block mt-1 w-full"
                type="date" name="tanggal_akhir_mou" :value="old('tanggal_akhir_mou', optional($industri->tanggal_akhir_mou ?? null)->format('Y-m-d'))" /></div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div><x-input-label for="latitude" value="Latitude" /><x-text-input id="latitude" class="block mt-1 w-full"
                type="text" name="latitude" :value="old('latitude', $industri->latitude ?? '')" /></div>
        <div><x-input-label for="longitude" value="Longitude" /><x-text-input id="longitude" class="block mt-1 w-full"
                type="text" name="longitude" :value="old('longitude', $industri->longitude ?? '')" /></div>
    </div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_mou_active" value="1" @checked(old('is_mou_active', $industri->is_mou_active ?? true))> MOU Aktif</label>
    <div><x-input-label for="catatan_mou" value="Catatan MOU" />
        <textarea name="catatan_mou" id="catatan_mou" rows="2" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('catatan_mou', $industri->catatan_mou ?? '') }}</textarea>
    </div>
    <div class="flex items-center justify-end pt-4 border-t"><a
            href="{{ route('prakerin.industri.index') }}"><x-secondary-button
                type="button">{{ __('Batal') }}</x-secondary-button></a><x-primary-button
            class="ms-4">{{ __('Simpan') }}</x-primary-button></div>
</div>
