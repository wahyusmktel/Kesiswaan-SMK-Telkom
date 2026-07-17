@props(['name', 'label', 'value' => null])
<label>
    <span class="mb-1 block text-sm font-bold text-gray-700">{{ $label }}</span>
    <textarea name="{{ $name }}" rows="3"
        {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500']) }}>{{ old($name, $value) }}</textarea>
    @error($name)<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
</label>
