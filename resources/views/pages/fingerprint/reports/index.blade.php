<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-black text-gray-900">Laporan Kehadiran</h2>
            <p class="mt-1 text-sm text-gray-500">Pratinjau dan cetak laporan fingerprint dengan format rekap GEISA.</p>
        </div>
    </x-slot>

    <div class="w-full px-4 py-6 sm:px-6 lg:px-8" x-data="attendanceReports()">
        @if($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ $errors->first() }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            <button type="button" @click="monthlyOpen = true" class="group flex min-h-[150px] items-start gap-4 rounded-lg border border-gray-200 bg-white p-5 text-left shadow-sm transition hover:border-red-300 hover:shadow-md">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600 group-hover:bg-red-600 group-hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/></svg>
                </span>
                <span>
                    <span class="block text-base font-black text-gray-900">Laporan Kehadiran Bulanan</span>
                    <span class="mt-1 block text-sm leading-6 text-gray-500">Rekap jumlah hari hadir seluruh pegawai dengan opsi lampiran detail masuk dan pulang per tanggal.</span>
                    <span class="mt-3 inline-flex items-center text-sm font-black text-red-600">Atur parameter <span class="ml-1">&rarr;</span></span>
                </span>
            </button>

            <button type="button" @click="dailyOpen = true" class="group flex min-h-[150px] items-start gap-4 rounded-lg border border-gray-200 bg-white p-5 text-left shadow-sm transition hover:border-blue-300 hover:shadow-md">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5-2V7a2 2 0 00-2-2h-1V3m-10 2H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-7M9 5V3"/></svg>
                </span>
                <span>
                    <span class="block text-base font-black text-gray-900">Laporan Kehadiran Harian</span>
                    <span class="mt-1 block text-sm leading-6 text-gray-500">Daftar seluruh pegawai beserta jam masuk dan pulang pada satu tanggal yang dipilih.</span>
                    <span class="mt-3 inline-flex items-center text-sm font-black text-blue-600">Pilih tanggal <span class="ml-1">&rarr;</span></span>
                </span>
            </button>
        </div>

        @if($previewUrl)
            <section class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-black text-gray-900">Pratinjau {{ $reportType === 'monthly' ? 'Laporan Bulanan' : 'Laporan Harian' }}</h3>
                        <p class="mt-0.5 text-xs text-gray-500">Dokumen siap diunduh atau dicetak.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ $downloadUrl }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-black text-gray-700 hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/></svg>
                            Unduh PDF
                        </a>
                        <button type="button" @click="printPreview()" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-black text-white hover:bg-red-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12-5h12v9H6v-9z"/></svg>
                            Cetak PDF
                        </button>
                    </div>
                </div>
                <div class="bg-gray-100 p-2 sm:p-4">
                    <iframe id="attendance-report-preview" src="{{ $previewUrl }}" title="Pratinjau laporan kehadiran" class="h-[72vh] min-h-[680px] w-full rounded border border-gray-300 bg-white"></iframe>
                </div>
            </section>
        @else
            <div class="mt-6 flex min-h-[360px] flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-6 text-center">
                <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-6m3 6V7m3 10v-3m4 7H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"/></svg>
                <p class="mt-3 font-black text-gray-700">Belum ada laporan yang dipratinjau</p>
                <p class="mt-1 max-w-md text-sm text-gray-500">Pilih laporan bulanan atau harian, kemudian terapkan parameternya.</p>
            </div>
        @endif

        <div x-show="monthlyOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto px-4 py-8" @keydown.escape.window="monthlyOpen = false">
            <div class="fixed inset-0 bg-gray-950/60" @click="monthlyOpen = false"></div>
            <form method="GET" class="relative mx-auto max-w-lg overflow-hidden rounded-lg bg-white shadow-2xl">
                <input type="hidden" name="report_type" value="monthly">
                <div class="flex items-start justify-between border-b border-gray-200 px-6 py-5">
                    <div><h3 class="text-lg font-black text-gray-900">Parameter Rekap Bulanan</h3><p class="mt-1 text-sm text-gray-500">Tentukan isi dokumen sebelum pratinjau.</p></div>
                    <button type="button" @click="monthlyOpen = false" title="Tutup" class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
                </div>
                <div class="space-y-5 px-6 py-6">
                    <label class="block"><span class="mb-1.5 block text-sm font-black text-gray-700">Bulan</span><input type="month" name="month" required value="{{ request('month', now()->format('Y-m')) }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"></label>
                    <div class="flex items-center justify-between gap-4 rounded-lg border border-gray-200 p-4">
                        <div><p class="text-sm font-black text-gray-800">Tampilkan pegawai tidak aktif</p><p class="mt-0.5 text-xs text-gray-500">Sertakan pegawai berstatus nonaktif, keluar, mutasi, atau pensiun.</p></div>
                        <label class="relative inline-flex cursor-pointer items-center"><input type="checkbox" name="include_inactive" value="1" x-model="includeInactive" class="peer sr-only"><span class="h-6 w-11 rounded-full bg-gray-300 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:bg-red-600 peer-checked:after:translate-x-5"></span><span class="ml-2 w-10 text-xs font-black text-gray-600" x-text="includeInactive ? 'Ya' : 'Tidak'"></span></label>
                    </div>
                    <div class="flex items-center justify-between gap-4 rounded-lg border border-gray-200 p-4">
                        <div><p class="text-sm font-black text-gray-800">Cetak lampiran</p><p class="mt-0.5 text-xs text-gray-500">Tambahkan daftar kehadiran harian pegawai di bawah rekap bulanan.</p></div>
                        <label class="relative inline-flex cursor-pointer items-center"><input type="checkbox" name="include_attachments" value="1" x-model="includeAttachments" class="peer sr-only"><span class="h-6 w-11 rounded-full bg-gray-300 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:bg-red-600 peer-checked:after:translate-x-5"></span><span class="ml-2 w-10 text-xs font-black text-gray-600" x-text="includeAttachments ? 'Ya' : 'Tidak'"></span></label>
                    </div>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-6 py-4"><button type="button" @click="monthlyOpen = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-black text-gray-700">Batal</button><button class="rounded-lg bg-red-600 px-5 py-2 text-sm font-black text-white hover:bg-red-700">Terapkan</button></div>
            </form>
        </div>

        <div x-show="dailyOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto px-4 py-8" @keydown.escape.window="dailyOpen = false">
            <div class="fixed inset-0 bg-gray-950/60" @click="dailyOpen = false"></div>
            <form method="GET" class="relative mx-auto max-w-md overflow-hidden rounded-lg bg-white shadow-2xl">
                <input type="hidden" name="report_type" value="daily">
                <div class="flex items-start justify-between border-b border-gray-200 px-6 py-5"><div><h3 class="text-lg font-black text-gray-900">Parameter Laporan Harian</h3><p class="mt-1 text-sm text-gray-500">Pilih tanggal yang akan dicetak.</p></div><button type="button" @click="dailyOpen = false" title="Tutup" class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button></div>
                <div class="px-6 py-6"><label class="block"><span class="mb-1.5 block text-sm font-black text-gray-700">Tanggal Kehadiran</span><input type="date" name="date" required value="{{ request('date', now()->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label></div>
                <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-6 py-4"><button type="button" @click="dailyOpen = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-black text-gray-700">Batal</button><button class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-black text-white hover:bg-blue-700">Terapkan</button></div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function attendanceReports() {
                return {
                    monthlyOpen: @js($errors->any() && request('report_type') === 'monthly'),
                    dailyOpen: @js($errors->any() && request('report_type') === 'daily'),
                    includeInactive: @js(request()->boolean('include_inactive')),
                    includeAttachments: @js(request()->boolean('include_attachments')),
                    printPreview() {
                        const frame = document.getElementById('attendance-report-preview');
                        if (frame && frame.contentWindow) {
                            frame.contentWindow.focus();
                            frame.contentWindow.print();
                        }
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
