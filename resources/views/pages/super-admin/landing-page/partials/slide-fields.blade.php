<div class="grid max-h-[70vh] gap-4 overflow-y-auto p-6 md:grid-cols-2">
    <label><span class="mb-1 block text-sm font-semibold">Label Kecil</span><input name="eyebrow" value="{{ old('eyebrow') }}" placeholder="Ekosistem Sekolah Digital" class="w-full rounded-lg border-gray-300"></label>
    <label><span class="mb-1 block text-sm font-semibold">Urutan *</span><input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" required min="0" max="999" class="w-full rounded-lg border-gray-300"></label>
    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Judul *</span><input name="title" value="{{ old('title') }}" required maxlength="160" class="w-full rounded-lg border-gray-300"></label>
    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Deskripsi</span><textarea name="description" rows="3" maxlength="500" class="w-full rounded-lg border-gray-300">{{ old('description') }}</textarea></label>
    <label><span class="mb-1 block text-sm font-semibold">Label CTA</span><input name="cta_label" value="{{ old('cta_label') }}" placeholder="Pelajari Lebih Lanjut" class="w-full rounded-lg border-gray-300"></label>
    <label><span class="mb-1 block text-sm font-semibold">URL CTA</span><input name="cta_url" value="{{ old('cta_url') }}" placeholder="/halaman atau https://..." class="w-full rounded-lg border-gray-300"></label>
    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Gambar Hero *</span><input type="file" name="image" required accept=".jpg,.jpeg,.png,.webp" class="w-full rounded-lg border border-gray-300 p-2 text-sm"><span class="mt-1 block text-xs text-gray-500">JPG, PNG, atau WebP. Maksimal 8 MB.</span></label>
    <label class="md:col-span-2 inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-red-600"><span class="text-sm font-semibold">Langsung tampilkan slide</span></label>
</div>
