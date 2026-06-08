<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Seting Waktu Fingerprint</h2>
            <p class="text-sm text-gray-500 mt-0.5">Konfigurasi rentang jam datang dan checkout untuk monitoring keterlambatan.</p>
        </div>
    </x-slot>

    <div class="max-w-5xl space-y-6">
        @include('pages.fingerprint.partials.flash')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Rentang Datang</p>
                        <p class="mt-2 text-3xl font-black text-gray-900">{{ substr($setting->checkin_start, 0, 5) }} - {{ substr($setting->checkin_end, 0, 5) }}</p>
                        <p class="mt-2 text-sm text-gray-500">Scan pertama setelah jam akhir datang akan ditandai terlambat.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Rentang Checkout</p>
                        <p class="mt-2 text-3xl font-black text-gray-900">{{ substr($setting->checkout_start, 0, 5) }} - {{ substr($setting->checkout_end, 0, 5) }}</p>
                        <p class="mt-2 text-sm text-gray-500">Scan pulang sebelum jam mulai checkout akan ditandai pulang cepat.</p>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('fingerprint.time-settings.update') }}" class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            @csrf
            @method('PUT')

            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Konfigurasi Waktu Absensi</h3>
                <p class="mt-1 text-sm text-gray-500">Gunakan format 24 jam agar hasil monitoring dan export Excel konsisten.</p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                    <p class="text-sm font-black text-gray-900">Jam Datang</p>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Mulai Datang</label>
                            <input type="time" name="checkin_start" value="{{ old('checkin_start', substr($setting->checkin_start, 0, 5)) }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            @error('checkin_start') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Batas Akhir Datang</label>
                            <input type="time" name="checkin_end" value="{{ old('checkin_end', substr($setting->checkin_end, 0, 5)) }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            @error('checkin_end') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                    <p class="text-sm font-black text-gray-900">Jam Checkout / Pulang</p>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Mulai Checkout</label>
                            <input type="time" name="checkout_start" value="{{ old('checkout_start', substr($setting->checkout_start, 0, 5)) }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            @error('checkout_start') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Akhir Checkout</label>
                            <input type="time" name="checkout_end" value="{{ old('checkout_end', substr($setting->checkout_end, 0, 5)) }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            @error('checkout_end') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('fingerprint.index') }}" class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-center text-sm font-bold text-gray-600 hover:bg-gray-50">Kembali</a>
                <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">Simpan Seting</button>
            </div>
        </form>

        <form method="POST" action="{{ route('fingerprint.security-shift-settings.update') }}" class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            @csrf
            @method('PUT')

            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Seting Waktu Kerja Security</h3>
                <p class="mt-1 text-sm text-gray-500">Atur jam shift security dan pilih shift untuk setiap pegawai berstatus Security.</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($securityShifts as $shift)
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-black text-gray-900">{{ $shift->name }}</p>
                                @if($shift->is_overnight)
                                    <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-indigo-700">Lintas Hari</span>
                                @endif
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Mulai</label>
                                    <input type="time" name="shifts[{{ $shift->id }}][starts_at]" value="{{ old("shifts.{$shift->id}.starts_at", substr($shift->starts_at, 0, 5)) }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Selesai</label>
                                    <input type="time" name="shifts[{{ $shift->id }}][ends_at]" value="{{ old("shifts.{$shift->id}.ends_at", substr($shift->ends_at, 0, 5)) }}" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                        <p class="text-sm font-black text-gray-900">Penugasan Shift Security</p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($securityEmployees as $employee)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-5 items-center">
                                <div class="md:col-span-2">
                                    <div class="font-bold text-gray-900">{{ $employee->masterGuru?->nama_lengkap ?? $employee->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $employee->email }}{{ $employee->masterGuru?->kode_guru ? ' | ' . $employee->masterGuru->kode_guru : '' }}</div>
                                </div>
                                <select name="assignments[{{ $employee->id }}]" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="">Belum diset</option>
                                    @foreach($securityShifts as $shift)
                                        <option value="{{ $shift->id }}" {{ (int) old("assignments.{$employee->id}", $employee->securityShiftAssignment?->fingerprint_security_shift_id) === (int) $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }} ({{ substr($shift->starts_at, 0, 5) }}-{{ substr($shift->ends_at, 0, 5) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @empty
                            <div class="p-8 text-center text-sm text-gray-500">
                                Belum ada pegawai Dapodik dengan status Security.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-600">Simpan Shift Security</button>
            </div>
        </form>
    </div>
</x-app-layout>
