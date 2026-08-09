<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-800">Pembiayaan SPMB</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola rincian biaya yang ditampilkan pada website SPMB.</p>
            </div>
            <a href="{{ url('/api/spmb/fees') }}" target="_blank" rel="noreferrer"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm transition hover:border-red-300 hover:text-red-700">
                Lihat API Publik
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14M5 5h5M5 5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5" /></svg>
            </a>
        </div>
    </x-slot>

    <div class="w-full py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
            @endif

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-wider text-gray-400">Tahun Pelajaran</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ $setting->spmb_academic_year ?? '2027/2028' }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-wider text-gray-400">Biaya Aktif</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ $fees->where('is_active', true)->count() }} item</p>
                </div>
                <div class="rounded-xl bg-red-600 p-5 text-white shadow-lg shadow-red-100">
                    <p class="text-xs font-black uppercase tracking-wider text-red-100">Total Dipublikasikan</p>
                    <p class="mt-2 text-2xl font-black">Rp {{ number_format($fees->where('is_active', true)->sum('amount'), 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[360px_minmax(0,1fr)]">
                <div class="space-y-6">
                    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4">
                            <h3 class="font-black text-gray-900">Pengaturan Publikasi</h3>
                            <p class="mt-1 text-xs leading-5 text-gray-500">Tahun ini tampil sebagai judul tabel pada website SPMB.</p>
                        </div>
                        <form action="{{ route('super-admin.spmb-fees.settings.update') }}" method="POST" class="space-y-4 p-5">
                            @csrf
                            @method('PUT')
                            <div>
                                <label for="spmb_academic_year" class="block text-sm font-bold text-gray-700">Tahun Pelajaran</label>
                                <input id="spmb_academic_year" name="spmb_academic_year" type="text" maxlength="9"
                                    value="{{ old('spmb_academic_year', $setting->spmb_academic_year ?? '2027/2028') }}" placeholder="2027/2028"
                                    class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                @error('spmb_academic_year') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-black text-white transition hover:bg-gray-800">Simpan Tahun Pelajaran</button>
                        </form>
                    </section>

                    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4">
                            <h3 class="font-black text-gray-900">Tambah Rincian Biaya</h3>
                            <p class="mt-1 text-xs leading-5 text-gray-500">Item aktif langsung tersedia melalui API publik.</p>
                        </div>
                        <form action="{{ route('super-admin.spmb-fees.store') }}" method="POST" class="space-y-4 p-5">
                            @csrf
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700">Nama Biaya</label>
                                <input id="name" name="name" type="text" maxlength="150" value="{{ old('name') }}" placeholder="Contoh: Seragam Sekolah"
                                    class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="amount" class="block text-sm font-bold text-gray-700">Jumlah Biaya</label>
                                <div class="mt-2 flex overflow-hidden rounded-lg border border-gray-300 shadow-sm focus-within:border-red-500 focus-within:ring-1 focus-within:ring-red-500">
                                    <span class="grid place-items-center bg-gray-50 px-3 text-sm font-black text-gray-500">Rp</span>
                                    <input id="amount" name="amount" type="number" min="0" max="999999999999" step="1" value="{{ old('amount') }}" placeholder="5000000"
                                        class="block w-full border-0 text-sm focus:ring-0">
                                </div>
                                @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="sort_order" class="block text-sm font-bold text-gray-700">Urutan</label>
                                <input id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $fees->max('sort_order') + 1) }}"
                                    class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                @error('sort_order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <label class="flex items-center gap-3 rounded-lg bg-gray-50 p-3">
                                <input name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-sm font-bold text-gray-700">Tampilkan di website SPMB</span>
                            </label>
                            <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-black text-white shadow-sm transition hover:bg-red-700">Tambahkan Biaya</button>
                        </form>
                    </section>
                </div>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                        <div>
                            <h3 class="font-black text-gray-900">Daftar Pembiayaan</h3>
                            <p class="mt-1 text-xs text-gray-500">Ubah nama, nominal, urutan, dan status setiap item.</p>
                        </div>
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-black text-red-700">{{ $fees->count() }} item</span>
                    </div>

                    @if($fees->isEmpty())
                        <div class="p-12 text-center text-sm text-gray-500">Belum ada rincian biaya. Tambahkan item pertama melalui formulir di sebelah kiri.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[820px] text-left text-sm">
                                <thead class="bg-gray-50 text-xs font-black uppercase tracking-wider text-gray-500">
                                    <tr><th class="px-4 py-3">Urutan</th><th class="px-4 py-3">Nama Biaya</th><th class="px-4 py-3">Jumlah</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Aksi</th></tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($fees as $fee)
                                        @php($updateForm = 'update-spmb-fee-'.$fee->id)
                                        @php($deleteForm = 'delete-spmb-fee-'.$fee->id)
                                        <tr class="align-top transition hover:bg-gray-50/70">
                                            <td class="px-4 py-4">
                                                <input form="{{ $updateForm }}" name="sort_order" type="number" min="0" max="999" value="{{ $fee->sort_order }}"
                                                    class="w-20 rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                            </td>
                                            <td class="px-4 py-4">
                                                <input form="{{ $updateForm }}" name="name" type="text" maxlength="150" value="{{ $fee->name }}"
                                                    class="w-full min-w-[240px] rounded-lg border-gray-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500">
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex min-w-[190px] overflow-hidden rounded-lg border border-gray-300 focus-within:border-red-500 focus-within:ring-1 focus-within:ring-red-500">
                                                    <span class="grid place-items-center bg-gray-50 px-3 text-xs font-black text-gray-500">Rp</span>
                                                    <input form="{{ $updateForm }}" name="amount" type="number" min="0" max="999999999999" step="1" value="{{ $fee->amount }}" class="w-full border-0 text-sm font-bold focus:ring-0">
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <label class="inline-flex items-center gap-2 rounded-full px-3 py-2 {{ $fee->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                                    <input form="{{ $updateForm }}" name="is_active" type="checkbox" value="1" {{ $fee->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                    <span class="text-xs font-black">{{ $fee->is_active ? 'Tampil' : 'Disembunyikan' }}</span>
                                                </label>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex justify-end gap-2">
                                                    <button form="{{ $updateForm }}" type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-xs font-black text-white transition hover:bg-gray-700">Simpan</button>
                                                    <button form="{{ $deleteForm }}" type="submit" onclick="return confirm('Hapus rincian biaya ini?')" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-black text-red-700 transition hover:bg-red-50">Hapus</button>
                                                </div>
                                                <form id="{{ $updateForm }}" action="{{ route('super-admin.spmb-fees.update', $fee) }}" method="POST" class="hidden">@csrf @method('PUT')</form>
                                                <form id="{{ $deleteForm }}" action="{{ route('super-admin.spmb-fees.destroy', $fee) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
