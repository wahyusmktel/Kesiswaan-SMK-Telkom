<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold text-gray-800">Status Keaktifan Guru</h2></x-slot>
    <div class="space-y-4 px-4 py-6 sm:px-6 lg:px-8">
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-600">Nonaktifkan guru yang sudah resign atau tidak lagi bertugas. Guru nonaktif tidak masuk daftar, chart, dan persentase absensi fingerprint Kepala Sekolah. Riwayat data dan akun login tidak dihapus.</p>
            <p class="mt-2 text-sm text-gray-600">Rekap menggunakan status keaktifan terbaru, termasuk untuk tanggal lampau. Status ini berbeda dari status kepegawaian Tetap / Full Time / Part Time.</p>
            <p class="mt-2 text-sm text-gray-600">Ubah status kepegawaian langsung melalui pilihan di tabel lalu klik Simpan. Perubahan tersimpan pada data Dapodik dan digunakan dalam perhitungan absensi; Part Time mengikuti jadwal mengajar.</p>
        </div>
        @if(session('success'))<div role="status" class="rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div role="alert" class="rounded-lg bg-red-50 p-4 text-red-800">{{ $errors->first() }}</div>@endif
        <form method="GET" action="{{ route('teacher-activity.index') }}" class="flex flex-wrap items-end gap-3">
            <label class="text-sm">Nama guru<input name="search" value="{{ request('search') }}" maxlength="255" class="mt-1 block rounded-lg border-gray-300" placeholder="Cari nama guru"></label>
            <label class="text-sm">Status<select name="status" class="mt-1 block rounded-lg border-gray-300">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select></label>
            <button class="rounded-lg bg-red-600 px-4 py-2 text-white">Tampilkan</button>
        </form>
        <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50"><tr><th class="p-4">Guru</th><th class="p-4">Status Kepegawaian</th><th class="p-4">Keaktifan</th><th class="p-4">Tindakan</th></tr></thead>
                <tbody class="divide-y">
                    @forelse($teachers as $teacher)
                        <tr>
                            <td class="p-4 font-medium">{{ $teacher->nama_lengkap }}</td>
                            <td class="p-4">
                                @if($teacher->dapodikGuru)
                                    @php($currentEmployment = \App\Support\EmploymentStatus::normalize($teacher->dapodikGuru->status_kepegawaian))
                                    <form method="POST" action="{{ route('teacher-activity.employment.update', $teacher) }}" class="flex flex-wrap items-center gap-2" onsubmit="return confirm('Simpan perubahan status kepegawaian guru ini? Perubahan memengaruhi perhitungan absensi.');">
                                        @csrf @method('PATCH')
                                        <label class="sr-only" for="employment-{{ $teacher->id }}">Status kepegawaian {{ $teacher->nama_lengkap }}</label>
                                        <select id="employment-{{ $teacher->id }}" name="status_kepegawaian" required class="rounded-lg border-gray-300 text-sm">
                                            @if(!in_array($currentEmployment, $employmentOptions, true))
                                                <option value="" selected disabled>{{ $currentEmployment ?: 'Pilih status kepegawaian' }}</option>
                                            @endif
                                            @foreach($employmentOptions as $option)
                                                <option value="{{ $option }}" @selected($currentEmployment === $option)>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        <button class="rounded-lg bg-red-600 px-3 py-2 text-white">Simpan</button>
                                    </form>
                                @else
                                    <span class="text-amber-700">Data Dapodik belum terhubung. Hubungkan melalui halaman Dapodik Guru terlebih dahulu.</span>
                                @endif
                            </td>
                            <td class="p-4"><span class="{{ $teacher->is_active ? 'text-green-700' : 'text-gray-500' }}">{{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="p-4">
                                <form method="POST" action="{{ route('teacher-activity.update', $teacher) }}" onsubmit="return confirm('Ubah status keaktifan guru ini? Perubahan langsung memengaruhi rekap absensi Kepala Sekolah.');">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $teacher->is_active ? '0' : '1' }}">
                                    <button class="rounded-lg border border-gray-300 px-3 py-2">{{ $teacher->is_active ? 'Nonaktifkan' : 'Aktifkan kembali' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-6 text-center text-gray-500">Tidak ada guru yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $teachers->links() }}
    </div>
</x-app-layout>
