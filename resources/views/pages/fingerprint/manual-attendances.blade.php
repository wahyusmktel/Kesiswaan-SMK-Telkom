<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Koreksi Absensi Fingerprint</h2>
            <p class="text-sm text-gray-500 mt-0.5">Fasilitas khusus Super Admin untuk menambah atau mengubah waktu absensi pegawai.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('pages.fingerprint.partials.flash')

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <div class="flex gap-3">
                <svg class="h-5 w-5 shrink-0 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                <div>
                    <p class="text-sm font-black text-amber-800">Gunakan hanya untuk kasus koreksi resmi.</p>
                    <p class="mt-1 text-sm text-amber-700">Contoh: pegawai lupa absensi, mesin bermasalah, atau log salah waktu. Setiap perubahan menyimpan catatan dan nama Super Admin yang melakukan koreksi.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <form method="POST" action="{{ route('fingerprint.manual-attendances.store') }}" class="xl:col-span-1 rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Tambah Absensi Manual</h3>
                    <p class="mt-1 text-sm text-gray-500">Menambahkan log masuk atau pulang untuk pegawai yang sudah dimapping.</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Pegawai</label>
                        <select name="fingerprint_user_id" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">Pilih pegawai</option>
                            @foreach($fingerprintUsers as $fpUser)
                                <option value="{{ $fpUser->id }}" {{ (string) old('fingerprint_user_id') === (string) $fpUser->id ? 'selected' : '' }}>
                                    {{ $fpUser->appUser?->masterGuru?->nama_lengkap ?? $fpUser->appUser?->name ?? $fpUser->name }}
                                    - {{ $fpUser->device?->name }} / ID {{ $fpUser->user_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal</label>
                            <input type="date" name="attendance_date" value="{{ old('attendance_date', now()->toDateString()) }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Jam</label>
                            <input type="time" name="attendance_time" value="{{ old('attendance_time') }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Jenis Absensi</label>
                        <select name="punch" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            <option value="checkin" {{ old('punch') === 'checkin' ? 'selected' : '' }}>Masuk</option>
                            <option value="checkout" {{ old('punch') === 'checkout' ? 'selected' : '' }}>Pulang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Alasan Koreksi</label>
                        <textarea name="correction_note" rows="4" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Pegawai lupa absen masuk karena mesin offline.">{{ old('correction_note') }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">Tambah Absensi</button>
                </div>
            </form>

            <div class="xl:col-span-2 rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-col gap-4">
                    <div>
                        <h3 class="font-bold text-gray-900">Ubah Waktu Absensi</h3>
                        <p class="mt-1 text-sm text-gray-500">Edit waktu log yang salah. Gunakan filter agar data mudah ditemukan.</p>
                    </div>
                    <form method="GET" action="{{ route('fingerprint.manual-attendances') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <input type="date" name="date" value="{{ request('date') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        <input name="search" value="{{ request('search') }}" class="md:col-span-2 rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Nama / user ID mesin">
                        <div class="flex gap-2">
                            <button class="flex-1 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">Filter</button>
                            <a href="{{ route('fingerprint.manual-attendances') }}" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Pegawai</th>
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Waktu Saat Ini</th>
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Koreksi</th>
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Audit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($attendances as $attendance)
                                <tr class="align-top hover:bg-gray-50/60">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $attendance->appUser?->masterGuru?->nama_lengkap ?? $attendance->appUser?->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $attendance->device?->name ?? '-' }} / ID {{ $attendance->user_id }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-sm font-black text-gray-900">{{ $attendance->timestamp?->format('d M Y H:i:s') }}</div>
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">Status: {{ $attendance->status ?? '-' }}</span>
                                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Punch: {{ $attendance->punch ?? '-' }}</span>
                                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600">{{ $attendance->entry_source ?? 'machine' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 min-w-[360px]">
                                        <form method="POST" action="{{ route('fingerprint.manual-attendances.update', $attendance) }}" class="space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            <div class="grid grid-cols-3 gap-2">
                                                <input type="date" name="attendance_date" value="{{ $attendance->timestamp?->toDateString() }}" required class="rounded-xl border-gray-300 text-xs focus:border-red-500 focus:ring-red-500">
                                                <input type="time" name="attendance_time" value="{{ $attendance->timestamp?->format('H:i') }}" required class="rounded-xl border-gray-300 text-xs focus:border-red-500 focus:ring-red-500">
                                                <input name="punch" value="{{ $attendance->punch }}" class="rounded-xl border-gray-300 text-xs focus:border-red-500 focus:ring-red-500" placeholder="Punch">
                                            </div>
                                            <textarea name="correction_note" rows="2" required class="w-full rounded-xl border-gray-300 text-xs focus:border-red-500 focus:ring-red-500" placeholder="Alasan perubahan">{{ $attendance->correction_note }}</textarea>
                                            <button class="rounded-xl bg-gray-900 px-4 py-2 text-xs font-bold text-white hover:bg-red-600">Simpan Koreksi</button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-500">
                                            <div>Oleh: <span class="font-bold text-gray-800">{{ $attendance->correctedBy?->name ?? '-' }}</span></div>
                                            <div>Waktu asli: <span class="font-mono">{{ $attendance->original_timestamp?->format('d M Y H:i') ?? '-' }}</span></div>
                                            <div class="mt-2 max-w-xs text-gray-400">{{ $attendance->correction_note ?? '-' }}</div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada log absensi untuk filter ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($attendances->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">{{ $attendances->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
