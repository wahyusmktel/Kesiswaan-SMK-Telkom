<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Edit Perangkat Pembelajaran</h2>
            <p class="text-xs text-gray-500">{{ $module->kode_modul }}</p>
        </div>
    </x-slot>

    <div class="w-full py-6">
        <div class="mx-auto w-full max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 border-b border-gray-200 pb-5">
                <a href="{{ route('guru-kelas.teaching-module.index') }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-md text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
                    title="Kembali">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sr-only">Kembali</span>
                </a>
                <div>
                    <h1 class="text-2xl font-black text-gray-950">Perbarui data utama</h1>
                    <p class="mt-1 text-sm text-gray-500">Perubahan metadata akan diterapkan pada hasil PDF berikutnya.</p>
                </div>
            </div>

            @include('pages.guru-kelas.teaching-module._form', [
                'formAction' => route('guru-kelas.teaching-module.update', $module),
                'formMethod' => 'PUT',
                'submitLabel' => 'Simpan Perubahan',
            ])
        </div>
    </div>
</x-app-layout>
