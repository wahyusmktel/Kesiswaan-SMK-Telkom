<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Registrasi Siswa Baru</h2>
                <p class="text-sm text-gray-500">Verifikasi pendaftaran cepat dan cocokkan dengan data resmi Dapodik.</p>
            </div>
            
        </div>
    </x-slot>

    <div class="w-full py-6">
        <div class="w-full space-y-5 px-4 sm:px-6 lg:px-8">
            @if (session('success'))<div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4"><p class="text-xs font-bold uppercase text-amber-700">Menunggu Verifikasi</p><p class="mt-1 text-2xl font-black text-amber-900">{{ $counts['pending'] ?? 0 }}</p></div>
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4"><p class="text-xs font-bold uppercase text-blue-700">Menunggu Pemetaan</p><p class="mt-1 text-2xl font-black text-blue-900">{{ $counts['approved'] ?? 0 }}</p></div>
                <div class="rounded-lg border border-gray-200 bg-white p-4"><p class="text-xs font-bold uppercase text-gray-500">Dapodik Belum Terhubung</p><p class="mt-1 text-2xl font-black text-gray-900">{{ $unmappedDapodikCount }}</p></div>
                            </div>

                <div class="rounded-lg border border-gray-200 bg-white p-6 mt-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Aksi Cepat</h3>
                            <p class="text-sm text-gray-500">Kelola registrasi siswa baru dengan mudah</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('student-registration.create') }}" target="_blank" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">Buka Form Publik</a>
                            <button @click="$dispatch('open-student-registration')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-500">Tambah Langsung</button>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>� <strong>Buka Form Publik</strong>: Link formulir pendaftaran publik untuk siswa baru</p>
                        <p class="mt-1">� <strong>Tambah Langsung</strong>: Tambahkan data siswa baru secara manual di sistem</p>
                    </div>
                </div>

                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-900">
                Setelah import Dapodik, lakukan pemetaan siswa sementara pada halaman ini <strong>sebelum</strong> menekan Sinkronisasi ke Master Siswa. Sistem akan menahan data yang memiliki calon pasangan agar tidak dibuat menjadi siswa duplikat.
            </div>

            <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-5 pt-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                        <nav class="flex gap-1 overflow-x-auto">
                            @foreach (['pending' => 'Menunggu', 'approved' => 'Siap Dipetakan', 'mapped' => 'Sudah Dipetakan', 'rejected' => 'Ditolak'] as $key => $label)
                                <a href="{{ route('master-data.student-registration.index', ['status' => $key]) }}" class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-bold {{ $status === $key ? 'border-red-600 text-red-700' : 'border-transparent text-gray-500 hover:text-gray-800' }}">{{ $label }} <span class="ml-1 rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700">{{ $counts[$key] ?? 0 }}</span></a>
                            @endforeach
                        </nav>
                        <form method="GET" class="mb-3 flex gap-2">
                            <input type="hidden" name="status" value="{{ $status }}">
                            <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, NISN, nomor registrasi" class="w-72 rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            <button class="rounded-lg bg-gray-900 px-4 text-sm font-bold text-white">Cari</button>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-600"><tr><th class="px-6 py-4">Registrasi</th><th class="px-6 py-4">Identitas</th><th class="px-6 py-4">Kontak</th><th class="px-6 py-4">Sumber / Status</th><th class="px-6 py-4 text-right">Aksi</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($registrations as $item)
                                <tr class="align-top hover:bg-gray-50">
                                    <td class="px-6 py-4"><div class="font-mono text-xs font-bold text-gray-900">{{ $item->registration_number }}</div><div class="mt-1 text-xs text-gray-500">{{ $item->created_at->translatedFormat('d M Y H:i') }}</div></td>
                                    <td class="px-6 py-4"><div class="font-bold text-gray-900">{{ $item->nama_lengkap }}</div><div class="mt-1 text-xs text-gray-500">NISN {{ $item->nisn ?: '-' }} · {{ $item->tempat_lahir ?: '-' }}, {{ $item->tanggal_lahir->format('d-m-Y') }}</div><div class="text-xs text-gray-500">{{ $item->sekolah_asal ?: 'Sekolah asal belum diisi' }}</div></td>
                                    <td class="px-6 py-4"><div class="text-gray-700">{{ $item->nomor_hp }}</div><div class="text-xs text-gray-500">{{ $item->email ?: '-' }}</div></td>
                                    <td class="px-6 py-4"><span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-bold text-gray-700">{{ $item->source === 'public' ? 'Form Publik' : 'Input Petugas' }}</span>@if($item->masterSiswa)<div class="mt-2 text-xs text-gray-500">NIS sementara: <span class="font-mono">{{ $item->masterSiswa->nis }}</span></div>@endif</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            @if ($item->status === 'pending')
                                                <form method="POST" action="{{ route('master-data.student-registration.approve', $item) }}" class="approve-form">@csrf @method('PATCH')<button type="button" onclick="confirmApprove(this)" class="rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700 hover:bg-green-100">Setujui</button></form>
                                                <form method="POST" action="{{ route('master-data.student-registration.reject', $item) }}" class="reject-form">@csrf @method('PATCH')<input type="hidden" name="notes"><button type="button" onclick="confirmReject(this)" class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50">Tolak</button></form>
                                            @elseif ($item->status === 'approved')
                                                <button type="button" @click="$dispatch('open-dapodik-mapping', {{ Illuminate\Support\Js::from(['id' => $item->id, 'name' => $item->nama_lengkap, 'nisn' => $item->nisn, 'birth' => $item->tanggal_lahir->format('d-m-Y'), 'url' => route('master-data.student-registration.map', $item)]) }})" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-500">Cocokkan Dapodik</button>
                                            @elseif ($item->status === 'mapped')
                                                <span class="inline-flex items-center gap-1 text-xs font-bold text-green-700"><span class="h-2 w-2 rounded-full bg-green-500"></span>{{ $item->dapodikSiswa?->nipd }}</span>
                                            @else
                                                <span class="text-xs text-red-600" title="{{ $item->notes }}">{{ Str::limit($item->notes, 40) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-14 text-center text-gray-500">Tidak ada data pada status ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-6 py-3">{{ $registrations->withQueryString()->links() }}</div>
            </section>
        </div>
    </div>

    <div x-data="{ open: false }" @open-student-registration.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4"><div class="relative w-full max-w-3xl rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b px-6 py-4"><div><h3 class="font-bold">Tambah Siswa Baru Sementara</h3><p class="text-sm text-gray-500">Data langsung disetujui dan muncul pada Data Siswa.</p></div><button @click="open = false" class="text-2xl text-gray-400">&times;</button></div>
            <form method="POST" action="{{ route('master-data.student-registration.store') }}">@csrf
                <div class="grid max-h-[65vh] gap-4 overflow-y-auto p-6 md:grid-cols-2">
                    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Nama lengkap *</span><input name="nama_lengkap" required class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">NISN</span><input name="nisn" inputmode="numeric" maxlength="10" class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">NIK</span><input name="nik" inputmode="numeric" maxlength="16" class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">Tempat lahir</span><input name="tempat_lahir" class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">Tanggal lahir *</span><input type="date" name="tanggal_lahir" required class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">Jenis kelamin *</span><select name="jenis_kelamin" required class="w-full rounded-lg border-gray-300"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></label>
                    <label><span class="mb-1 block text-sm font-semibold">Nomor HP *</span><input type="number" name="nomor_hp" inputmode="numeric" required class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">Email</span><input type="email" name="email" class="w-full rounded-lg border-gray-300"></label>
                    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Alamat *</span><textarea name="alamat" required rows="2" class="w-full rounded-lg border-gray-300"></textarea></label>
                    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Sekolah asal</span><input name="sekolah_asal" class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">Nama orang tua/wali</span><input name="nama_orang_tua" class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">HP orang tua/wali</span><input type="number" name="nomor_hp_orang_tua" inputmode="numeric" class="w-full rounded-lg border-gray-300"></label>
                </div>
                <div class="flex justify-end gap-2 border-t bg-gray-50 px-6 py-4"><button type="button" @click="open = false" class="rounded-lg border px-4 py-2 text-sm font-bold">Batal</button><button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white">Simpan Siswa Sementara</button></div>
            </form>
        </div></div>
    </div>

    <div x-data="dapodikMapping()" @open-dapodik-mapping.window="openModal($event.detail)" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4"><div class="relative w-full max-w-2xl rounded-lg bg-white shadow-xl">
            <div class="border-b px-6 py-4"><h3 class="font-bold">Cocokkan dengan Data Dapodik</h3><p class="mt-1 text-sm text-gray-500"><span x-text="student.name"></span> · NISN <span x-text="student.nisn || '-'"></span> · Lahir <span x-text="student.birth"></span></p></div>
            <form :action="student.url" method="POST">@csrf
                <div class="p-6"><div class="flex gap-2"><input type="search" x-model="query" @keydown.enter.prevent="search()" class="min-w-0 flex-1 rounded-lg border-gray-300" placeholder="Cari nama, NIPD, atau NISN"><button type="button" @click="search()" class="rounded-lg bg-gray-900 px-4 text-sm font-bold text-white">Cari</button></div>
                    <div class="mt-4 max-h-72 space-y-2 overflow-y-auto"><template x-if="loading"><p class="py-8 text-center text-sm text-gray-500">Mencari data...</p></template><template x-if="!loading && results.length === 0"><p class="py-8 text-center text-sm text-gray-500">Data Dapodik belum ditemukan.</p></template><template x-for="record in results" :key="record.id"><label class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 hover:bg-blue-50" :class="selected == record.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"><input type="radio" name="dapodik_siswa_id" :value="record.id" x-model="selected" required class="mt-1 text-blue-600"><span><span class="block font-bold text-gray-900" x-text="record.nama || '-'"></span><span class="block text-xs text-gray-500" x-text="`NIPD ${record.nipd || '-'} · NISN ${record.nisn || '-'} · ${record.tempat_lahir || '-'}, ${record.tanggal_lahir || '-'}`"></span></span></label></template></div>
                </div>
                <div class="flex justify-end gap-2 border-t bg-gray-50 px-6 py-4"><button type="button" @click="open = false" class="rounded-lg border px-4 py-2 text-sm font-bold">Batal</button><button :disabled="!selected" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white disabled:bg-gray-300">Konfirmasi Pemetaan</button></div>
            </form>
        </div></div>
    </div>

    {{-- MODAL DETAIL BIODATA SISWA BARU --}}
    <div x-data="biodataDetailModal()" @open-biodata-detail.window="openModal($event.detail)" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-4xl rounded-2xl bg-white shadow-2xl">
                {{-- Header Modal --}}
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-8 py-6 rounded-t-2xl">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900" x-text="student.name"></h3>
                        <p class="mt-1 text-sm text-gray-500">Detail Biodata Calon Siswa</p>
                    </div>
                    <button @click="open = false" class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Content Area --}}
                <div class="max-h-[70vh] overflow-y-auto px-8 py-6">
                    <template x-if="loading">
                        <div class="flex items-center justify-center py-12">
                            <div class="flex flex-col items-center">
                                <div class="h-12 w-12 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600"></div>
                                <p class="mt-4 text-sm font-medium text-gray-600">Memuat data biodata...</p>
                            </div>
                        </div>
                    </template>

                    <template x-if="!loading && student.data">
                        <div class="space-y-8">
                            {{-- Section 1: Identitas Dasar --}}
                            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-blue-50/50 to-white p-6">
                                <h4 class="mb-4 text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    Identitas Dasar
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Lengkap</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.nama_lengkap || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">NISN</label>
                                        <p class="mt-1 font-mono text-lg font-medium text-gray-900" x-text="student.data.nisn || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tempat, Tanggal Lahir</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900">
                                            <span x-text="student.data.tempat_lahir || '-'"></span>, 
                                            <span x-text="student.data.tanggal_lahir ? new Date(student.data.tanggal_lahir).toLocaleDateString('id-ID') : '-'"></span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Kelamin</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900">
                                            <span x-text="student.data.jenis_kelamin === 'L' ? 'Laki-laki' : (student.data.jenis_kelamin === 'P' ? 'Perempuan' : '-')"></span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Agama</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.agama || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nomor Registrasi</label>
                                        <p class="mt-1 font-mono text-lg font-medium text-gray-900" x-text="student.data.registration_number || '-'"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Kontak & Alamat --}}
                            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-green-50/50 to-white p-6">
                                <h4 class="mb-4 text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Kontak & Alamat
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nomor HP</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.nomor_hp || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Email</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.email || '-'"></p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Alamat Lengkap</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.alamat || 'Alamat belum diisi'"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 3: Sekolah & Keluarga --}}
                            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-amber-50/50 to-white p-6">
                                <h4 class="mb-4 text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                                    </svg>
                                    Sekolah & Keluarga
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sekolah Asal</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.sekolah_asal || 'Sekolah asal belum diisi'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Orang Tua/Wali</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.nama_orang_tua || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">HP Orang Tua/Wali</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.nomor_hp_orang_tua || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Pendaftaran</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.created_at ? new Date(student.data.created_at).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-'"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 4: Status & Informasi Tambahan --}}
                            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-purple-50/50 to-white p-6">
                                <h4 class="mb-4 text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Status & Informasi
                                </h4>
                                <div class="flex flex-wrap gap-4">
                                    <div class="rounded-lg bg-gray-100 px-4 py-3">
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</label>
                                        <p class="mt-1">
                                            <span x-show="student.data.status === 'pending'" class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800">
                                                <span class="mr-1.5 h-2 w-2 rounded-full bg-amber-500"></span>
                                                Menunggu Verifikasi
                                            </span>
                                            <span x-show="student.data.status === 'approved'" class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                                <span class="mr-1.5 h-2 w-2 rounded-full bg-blue-500"></span>
                                                Disetujui
                                            </span>
                                            <span x-show="student.data.status === 'rejected'" class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">
                                                <span class="mr-1.5 h-2 w-2 rounded-full bg-red-500"></span>
                                                Ditolak
                                            </span>
                                            <span x-show="student.data.status === 'mapped'" class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                                <span class="mr-1.5 h-2 w-2 rounded-full bg-green-500"></span>
                                                Sudah Dipetakan
                                            </span>
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-gray-100 px-4 py-3">
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sumber Data</label>
                                        <p class="mt-1">
                                            <span x-show="student.data.source === 'public'" class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                                Form Publik
                                            </span>
                                            <span x-show="student.data.source === 'manual'" class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                                Input Petugas
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="!loading && !student.data">
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-4 text-lg font-medium text-gray-900">Data tidak ditemukan</p>
                            <p class="mt-1 text-sm text-gray-500">Biodata siswa tidak dapat dimuat</p>
                        </div>
                    </template>
                </div>

                {{-- Footer dengan Tombol Aksi --}}
                <div class="sticky bottom-0 z-10 border-t border-gray-200 bg-white px-8 py-6 rounded-b-2xl">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-500">
                            <p>ID Registrasi: <span class="font-mono font-medium" x-text="student.id || '-'"></span></p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button @click="open = false" class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50">
                                Tutup
                            </button>
                            <template x-if="student.data && student.data.status === 'pending'">
                                <div class="flex gap-3">
                                    <form :action="student.data.reject_url" method="POST" class="reject-form">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="notes" :value="rejectReason">
                                        <button type="button" @click="showRejectDialog()" class="rounded-lg border border-red-300 bg-white px-6 py-3 text-sm font-bold text-red-700 hover:bg-red-50">
                                            Tolak
                                        </button>
                                    </form>
                                    <form :action="student.data.approve_url" method="POST" class="approve-form">
                                        @csrf @method('PATCH')
                                        <button type="button" @click="confirmApprove()" class="rounded-lg bg-green-600 px-6 py-3 text-sm font-bold text-white hover:bg-green-700">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>


        // Fungsi untuk modal detail biodata
        function biodataDetailModal() {
            return {
                open: false,
                student: { id: null, name: '', data: null },
                loading: false,
                rejectReason: '',
                openModal(data) {
                    this.student = {
                        id: data.id,
                        name: data.name,
                        data: null
                    };
                    this.open = true;
                    this.loading = true;
                    this.loadBiodata(data.id);
                },
                async loadBiodata(id) {
                    try {
                        const url = `/master-data/student-registration/${id}/biodata`;
                        const response = await fetch(url, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.student.data = data;
                            // Tambahkan URL untuk action
                            if (this.student.data) {
                                this.student.data.approve_url = `/master-data/student-registration/${id}/approve`;
                                this.student.data.reject_url = `/master-data/student-registration/${id}/reject`;
                            }
                        }
                    } catch (error) {
                        console.error('Error loading biodata:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                confirmApprove() {
                    Swal.fire({
                        title: 'Setujui pendaftaran?',
                        text: 'Data siswa sementara akan dibuat menjadi data master.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, setujui',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#10b981',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submitApprove();
                        }
                    });
                },
                submitApprove() {
                    const form = document.querySelector('.approve-form');
                    if (form) {
                        form.submit();
                    } else {
                        // Fallback jika form tidak ditemukan
                        window.location.href = this.student.data.approve_url;
                    }
                },
                showRejectDialog() {
                    Swal.fire({
                        title: 'Alasan Penolakan',
                        input: 'textarea',
                        inputLabel: 'Tuliskan alasan penolakan atau data yang perlu diperbaiki',
                        inputPlaceholder: 'Contoh: Data NISN tidak valid, alamat tidak lengkap, dll.',
                        inputAttributes: {
                            'aria-label': 'Alasan penolakan'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Konfirmasi Penolakan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#ef4444',
                        preConfirm: (value) => {
                            if (!value || value.trim() === '') {
                                Swal.showValidationMessage('Alasan penolakan wajib diisi');
                                return false;
                            }
                            return value;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.rejectReason = result.value;
                            this.submitReject();
                        }
                    });
                },
                submitReject() {
                    const form = document.querySelector('.reject-form');
                    if (form) {
                        const notesInput = form.querySelector('[name="notes"]');
                        if (notesInput) {
                            notesInput.value = this.rejectReason;
                        }
                        form.submit();
                    } else {
                        // Fallback jika form tidak ditemukan
                        fetch(this.student.data.reject_url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ notes: this.rejectReason })
                        }).then(response => {
                            if (response.ok) {
                                location.reload();
                            }
                        });
                    }
                }
            };
        }
        function confirmApprove(button) { Swal.fire({title:'Setujui pendaftaran?',text:'Data siswa sementara akan dibuat.',icon:'question',showCancelButton:true,confirmButtonText:'Ya, setujui'}).then(r => { if(r.isConfirmed) button.closest('form').submit(); }); }
        function confirmReject(button) { Swal.fire({title:'Alasan penolakan',input:'textarea',inputPlaceholder:'Tuliskan data yang perlu diperbaiki',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Tolak pendaftaran',preConfirm:value => { if(!value) Swal.showValidationMessage('Alasan wajib diisi'); return value; }}).then(r => { if(r.isConfirmed){ const form=button.closest('form'); form.querySelector('[name=notes]').value=r.value; form.submit(); }}); }
        function dapodikMapping() { return { open:false, student:{}, query:'', results:[], selected:null, loading:false, openModal(data){ this.student=data; this.query=data.nisn || data.name; this.selected=null; this.open=true; this.search(); }, async search(){ this.loading=true; try { const url = @js(route('master-data.student-registration.dapodik.search')); const response=await fetch(`${url}?q=${encodeURIComponent(this.query)}`, {headers:{'Accept':'application/json'}}); this.results=await response.json(); } finally { this.loading=false; } } }; }
    </script>
    

@endpush
</x-app-layout>



