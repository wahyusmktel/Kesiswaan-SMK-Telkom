<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Master Data Industri Prakerin') }}</h2>
            <p class="text-sm text-gray-500">Kelola data mitra industri untuk kebutuhan prakerin secara cepat dan rapi.</p>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ createOpen: false }">
            <div class="bg-white border border-gray-100 shadow-sm sm:rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Prakerin</span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">Master Data</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Daftar Industri</h3>
                                <p class="text-sm text-gray-500">Pantau mitra industri, kota, dan PIC yang terhubung.</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" placeholder="Cari industri atau kota..."
                                    class="w-full sm:w-64 rounded-xl border border-gray-200 bg-white px-10 py-2 text-sm text-gray-700 shadow-sm focus:border-red-300 focus:ring focus:ring-red-100">
                            </div>
                            <button type="button" @click="createOpen = true"
                                class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring focus:ring-red-200">
                                + Tambah Industri
                            </button>
                        </div>
                    </div>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Industri</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $industri->total() }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Data Ditampilkan</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $industri->count() }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-red-50/70 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Halaman</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $industri->currentPage() }} / {{ $industri->lastPage() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white border border-gray-100 shadow-sm sm:rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Data Industri</p>
                        <p class="text-xs text-gray-500">Kelola data industri prakerin dengan cepat.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                        {{ $industri->total() }} data
                    </span>
                </div>
                <div class="relative" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 600)">
                    <div x-show="loading" x-transition.opacity class="absolute inset-0 bg-white/90 px-6 py-4">
                        <div class="space-y-4 animate-pulse">
                            <div class="h-4 w-1/3 rounded-full bg-gray-200"></div>
                            <div class="space-y-3">
                                <div class="h-10 rounded-xl bg-gray-100"></div>
                                <div class="h-10 rounded-xl bg-gray-100"></div>
                                <div class="h-10 rounded-xl bg-gray-100"></div>
                                <div class="h-10 rounded-xl bg-gray-100"></div>
                            </div>
                        </div>
                    </div>
                    <div x-show="!loading" x-transition.opacity x-cloak class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Industri</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kota</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontak PIC</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($industri as $item)
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-semibold text-gray-900">{{ $item->nama_industri }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->alamat ?? 'Alamat belum tersedia' }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $item->kota }}</td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-700">{{ $item->nama_pic ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->email_pic ?? '-' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-2" x-data="{ editOpen: false }">
                                                <button type="button" @click="editOpen = true"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-gray-300 hover:bg-gray-50">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14m0 0H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v5" />
                                                    </svg>
                                                    Edit
                                                </button>
                                                <form action="{{ route('prakerin.industri.destroy', $item->id) }}"
                                                    method="POST" class="inline-block js-delete" data-industri="{{ $item->nama_industri }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-50">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-3h4m-4 0a1 1 0 00-1 1v1h6V5a1 1 0 00-1-1m-4 0h4" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>

                                                <div x-show="editOpen" @click.outside="editOpen = false" @keydown.escape.window="editOpen = false"
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4 py-6"
                                                    style="display: none;">
                                                    <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl">
                                                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                                                            <div>
                                                                <h3 class="text-base font-semibold text-gray-900">Edit Industri</h3>
                                                                <p class="text-xs text-gray-500">Perbarui data industri sesuai kebutuhan.</p>
                                                            </div>
                                                            <button type="button" @click="editOpen = false" class="text-gray-400 hover:text-gray-600">
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <form method="POST" action="{{ route('prakerin.industri.update', $item->id) }}" class="px-6 py-6 space-y-4">
                                                            @csrf
                                                            @method('PUT')
                                                            <div>
                                                                <x-input-label for="nama_industri_{{ $item->id }}" value="Nama Industri" />
                                                                <x-text-input id="nama_industri_{{ $item->id }}" class="block mt-1 w-full"
                                                                    type="text" name="nama_industri" value="{{ old('nama_industri', $item->nama_industri) }}" required />
                                                            </div>
                                                            <div>
                                                                <x-input-label for="alamat_{{ $item->id }}" value="Alamat Lengkap" />
                                                                <textarea name="alamat" id="alamat_{{ $item->id }}" rows="3"
                                                                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-red-300 focus:ring focus:ring-red-100">{{ old('alamat', $item->alamat) }}</textarea>
                                                            </div>
                                                            <div class="grid gap-4 md:grid-cols-2">
                                                                <div>
                                                                    <x-input-label for="kota_{{ $item->id }}" value="Kota/Kabupaten" />
                                                                    <x-text-input id="kota_{{ $item->id }}" class="block mt-1 w-full"
                                                                        type="text" name="kota" value="{{ old('kota', $item->kota) }}" required />
                                                                </div>
                                                                <div>
                                                                    <x-input-label for="telepon_{{ $item->id }}" value="Telepon" />
                                                                    <x-text-input id="telepon_{{ $item->id }}" class="block mt-1 w-full"
                                                                        type="text" name="telepon" value="{{ old('telepon', $item->telepon) }}" />
                                                                </div>
                                                            </div>
                                                            <div class="grid gap-4 md:grid-cols-2">
                                                                <div>
                                                                    <x-input-label for="nama_pic_{{ $item->id }}" value="Nama PIC (Person in Charge)" />
                                                                    <x-text-input id="nama_pic_{{ $item->id }}" class="block mt-1 w-full"
                                                                        type="text" name="nama_pic" value="{{ old('nama_pic', $item->nama_pic) }}" />
                                                                </div>
                                                                <div>
                                                                    <x-input-label for="email_pic_{{ $item->id }}" value="Email PIC" />
                                                                    <x-text-input id="email_pic_{{ $item->id }}" class="block mt-1 w-full"
                                                                        type="email" name="email_pic" value="{{ old('email_pic', $item->email_pic) }}" />
                                                                </div>
                                                            </div>
                                                            <div class="flex flex-col gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                                                                <button type="button" @click="editOpen = false"
                                                                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                                                                    Batal
                                                                </button>
                                                                <x-primary-button class="justify-center">Simpan Perubahan</x-primary-button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="mx-auto flex max-w-sm flex-col items-center gap-3">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">Belum ada data industri</p>
                                                    <p class="text-xs text-gray-500">Tambahkan industri baru untuk memulai.</p>
                                                </div>
                                                <button type="button" @click="createOpen = true"
                                                    class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                                                    + Tambah Industri
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="border-t border-gray-100 px-6 py-4">
                    {{ $industri->links() }}
                </div>
            </div>

            <div x-show="createOpen" @click.outside="createOpen = false" @keydown.escape.window="createOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4 py-6"
                style="display: none;">
                <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Tambah Industri</h3>
                            <p class="text-xs text-gray-500">Lengkapi data industri baru untuk prakerin.</p>
                        </div>
                        <button type="button" @click="createOpen = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('prakerin.industri.store') }}" class="px-6 py-6 space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="nama_industri" value="Nama Industri" />
                            <x-text-input id="nama_industri" class="block mt-1 w-full"
                                type="text" name="nama_industri" value="{{ old('nama_industri') }}" required />
                        </div>
                        <div>
                            <x-input-label for="alamat" value="Alamat Lengkap" />
                            <textarea name="alamat" id="alamat" rows="3"
                                class="w-full rounded-xl border-gray-200 shadow-sm focus:border-red-300 focus:ring focus:ring-red-100">{{ old('alamat') }}</textarea>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="kota" value="Kota/Kabupaten" />
                                <x-text-input id="kota" class="block mt-1 w-full"
                                    type="text" name="kota" value="{{ old('kota') }}" required />
                            </div>
                            <div>
                                <x-input-label for="telepon" value="Telepon" />
                                <x-text-input id="telepon" class="block mt-1 w-full"
                                    type="text" name="telepon" value="{{ old('telepon') }}" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="nama_pic" value="Nama PIC (Person in Charge)" />
                                <x-text-input id="nama_pic" class="block mt-1 w-full"
                                    type="text" name="nama_pic" value="{{ old('nama_pic') }}" />
                            </div>
                            <div>
                                <x-input-label for="email_pic" value="Email PIC" />
                                <x-text-input id="email_pic" class="block mt-1 w-full"
                                    type="email" name="email_pic" value="{{ old('email_pic') }}" />
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                            <button type="button" @click="createOpen = false"
                                class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                                Batal
                            </button>
                            <x-primary-button class="justify-center">Simpan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.js-delete').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const industriName = form.getAttribute('data-industri') || 'data ini';

                    Swal.fire({
                        title: 'Hapus Industri?',
                        text: 'Anda akan menghapus ' + industriName + '. Tindakan ini tidak dapat dibatalkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endpush
