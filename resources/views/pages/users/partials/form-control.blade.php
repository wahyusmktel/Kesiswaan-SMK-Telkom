<div class="mt-4">
    <x-input-label for="name" :value="__('Nama')" />
    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email ?? '')" required />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="role" :value="__('Peran')" />
    <select name="role" id="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="">Pilih Peran</option>
        @foreach ($roles as $role)
            <option value="{{ $role }}" {{ old('role', ($user ?? null)?->getRoleNames()->first()) == $role ? 'selected' : '' }}>
                {{ $role }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('role')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="phone_number" :value="__('Nomor HP / WhatsApp')" />
    <x-text-input id="phone_number" class="block mt-1 w-full" type="tel" name="phone_number"
        inputmode="tel" autocomplete="tel" placeholder="Contoh: 081234567890"
        :value="old('phone_number', $user->phone_number ?? '')" />
    <p class="mt-1 text-xs text-gray-500">Digunakan untuk notifikasi resmi, termasuk rekap absensi fingerprint.</p>
    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="password" :value="__('Password')" />
    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
    @if(isset($user))
        <small class="text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password.</small>
    @endif
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-4">
    <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Batal
    </a>
    <x-primary-button class="ms-4">
        {{ isset($user) ? 'Perbarui' : 'Simpan' }}
    </x-primary-button>
</div>
