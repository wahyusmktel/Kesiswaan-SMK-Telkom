@php($editing = $mode === 'edit')
<div>
    <label class="mb-2 block text-sm font-bold">Nama Aplikasi</label>
    <input name="name" required maxlength="120" {{ $editing ? 'x-model=edit.name' : '' }} value="{{ $editing ? '' : old('name') }}" class="h-11 w-full rounded-md border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">
    @if(!$editing) @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror @endif
</div>
<div>
    <label class="mb-2 block text-sm font-bold">Homepage URL</label>
    <input name="homepage_url" type="url" placeholder="https://aplikasi.example.id" {{ $editing ? 'x-model=edit.homepage_url' : '' }} value="{{ $editing ? '' : old('homepage_url') }}" class="h-11 w-full rounded-md border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">
</div>
@if(!$editing)
<div class="sm:col-span-2">
    <label class="mb-2 block text-sm font-bold">Jenis Client</label>
    <div class="grid gap-3 sm:grid-cols-2">
        <label class="flex cursor-pointer gap-3 rounded-md border border-slate-200 p-4 has-[:checked]:border-red-500 has-[:checked]:bg-red-50"><input type="radio" name="client_type" value="public_pkce" checked class="mt-1 text-red-600 focus:ring-red-500"><span><strong class="block text-sm">Public + PKCE</strong><small class="text-xs text-slate-500">Web modern, SPA, Flutter, dan aplikasi mobile. Direkomendasikan.</small></span></label>
        <label class="flex cursor-pointer gap-3 rounded-md border border-slate-200 p-4 has-[:checked]:border-red-500 has-[:checked]:bg-red-50"><input type="radio" name="client_type" value="confidential" class="mt-1 text-red-600 focus:ring-red-500"><span><strong class="block text-sm">Confidential</strong><small class="text-xs text-slate-500">Backend server yang mampu menyimpan client secret.</small></span></label>
    </div>
</div>
@endif
<div class="sm:col-span-2">
    <label class="mb-2 block text-sm font-bold">Deskripsi</label>
    <textarea name="description" rows="3" maxlength="1000" {{ $editing ? 'x-model=edit.description' : '' }} class="w-full rounded-md border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">{{ $editing ? '' : old('description') }}</textarea>
</div>
<div class="sm:col-span-2">
    <div class="mb-2 flex items-center justify-between"><label class="text-sm font-bold">Redirect URI</label><button type="button" @click="{{ $editing ? 'edit.redirect_uris' : 'createUris' }}.push('')" class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-bold hover:bg-slate-200">+ Tambah URI</button></div>
    <div class="space-y-2">
        <template x-for="(uri, index) in {{ $editing ? 'edit.redirect_uris' : 'createUris' }}" :key="index">
            <div class="flex gap-2"><input name="redirect_uris[]" x-model="{{ $editing ? 'edit.redirect_uris' : 'createUris' }}[index]" type="url" required placeholder="https://aplikasi.example.id/auth/callback" class="h-11 min-w-0 flex-1 rounded-md border-slate-300 text-sm focus:border-red-500 focus:ring-red-500"><button type="button" @click="{{ $editing ? 'edit.redirect_uris' : 'createUris' }}.splice(index, 1)" x-show="{{ $editing ? 'edit.redirect_uris' : 'createUris' }}.length > 1" class="h-11 w-11 rounded-md border border-red-200 text-red-600 hover:bg-red-50" title="Hapus URI">&times;</button></div>
        </template>
    </div>
    <p class="mt-2 text-xs text-slate-500">Harus sama persis dengan callback aplikasi. Gunakan HTTPS; localhost HTTP hanya untuk pengembangan.</p>
    @if(!$editing) @error('redirect_uris')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror @error('redirect_uris.*')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror @endif
</div>
<div class="sm:col-span-2">
    <label class="mb-2 block text-sm font-bold">Logo Aplikasi <span class="font-normal text-slate-400">(opsional)</span></label>
    <input name="logo" type="file" accept="image/png,image/jpeg,image/webp" class="block w-full rounded-md border border-slate-300 text-sm file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:font-bold">
</div>
