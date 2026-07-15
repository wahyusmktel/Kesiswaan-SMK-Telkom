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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmApprove(button) { Swal.fire({title:'Setujui pendaftaran?',text:'Data siswa sementara akan dibuat.',icon:'question',showCancelButton:true,confirmButtonText:'Ya, setujui'}).then(r => { if(r.isConfirmed) button.closest('form').submit(); }); }
        function confirmReject(button) { Swal.fire({title:'Alasan penolakan',input:'textarea',inputPlaceholder:'Tuliskan data yang perlu diperbaiki',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Tolak pendaftaran',preConfirm:value => { if(!value) Swal.showValidationMessage('Alasan wajib diisi'); return value; }}).then(r => { if(r.isConfirmed){ const form=button.closest('form'); form.querySelector('[name=notes]').value=r.value; form.submit(); }}); }
        function dapodikMapping() { return { open:false, student:{}, query:'', results:[], selected:null, loading:false, openModal(data){ this.student=data; this.query=data.nisn || data.name; this.selected=null; this.open=true; this.search(); }, async search(){ this.loading=true; try { const url = @js(route('master-data.student-registration.dapodik.search')); const response=await fetch(`${url}?q=${encodeURIComponent(this.query)}`, {headers:{'Accept':'application/json'}}); this.results=await response.json(); } finally { this.loading=false; } } }; }
    </script>
    @endpush
</x-app-layout>

