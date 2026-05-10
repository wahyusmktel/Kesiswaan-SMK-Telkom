<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Manajemen Pegawai</h2>
        <p class="text-sm text-gray-500 mt-0.5">Kelola seluruh akun guru, staf, dan pegawai sekolah</p>
    </x-slot>

    @php
    $roleColors = [
        'Admin'           => 'bg-rose-100 text-rose-700 border border-rose-200',
        'Operator'        => 'bg-blue-100 text-blue-700 border border-blue-200',
        'KAUR SDM'        => 'bg-purple-100 text-purple-700 border border-purple-200',
        'Guru Piket'      => 'bg-amber-100 text-amber-700 border border-amber-200',
        'Guru Kelas'      => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
        'Wali Kelas'      => 'bg-violet-100 text-violet-700 border border-violet-200',
        'Guru BK'         => 'bg-teal-100 text-teal-700 border border-teal-200',
        'Waka Kesiswaan'  => 'bg-orange-100 text-orange-700 border border-orange-200',
        'Kepala Sekolah'  => 'bg-red-100 text-red-700 border border-red-200',
        'Security'        => 'bg-gray-100 text-gray-700 border border-gray-200',
        'Kurikulum'       => 'bg-cyan-100 text-cyan-700 border border-cyan-200',
    ];
    $roleColorDefault = 'bg-slate-100 text-slate-700 border border-slate-200';
    @endphp

    {{-- ─── MODALS ─── --}}
    <div
        x-data="{
            showAdd:    false,
            showEdit:   false,
            showDelete: false,
            showImport: false,
            eu: {},
            du: {},
            openEdit(u)   { this.eu = u; this.showEdit   = true; },
            openDelete(u) { this.du = u; this.showDelete = true; },
        }"
        @open-add-pegawai.window="showAdd = true"
        class="relative">

        {{-- ── ADD MODAL ── --}}
        <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div x-show="showAdd"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showAdd=false" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

            <div x-show="showAdd"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="h-1 bg-gradient-to-r from-indigo-500 to-violet-500"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Tambah Pegawai Baru</h3>
                                <p class="text-xs text-gray-400">Buat akun login untuk pegawai</p>
                            </div>
                        </div>
                        <button @click="showAdd=false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('manajemen-pegawai.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required placeholder="Nama lengkap pegawai"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required placeholder="email@sekolah.sch.id"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password" required placeholder="Min. 8 karakter"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jabatan / Role <span class="text-red-500">*</span></label>
                                <select name="role" required
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                                    <option value="">-- Pilih Role --</option>
                                    @foreach ($allRoles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Data Kepegawaian <span class="font-normal text-gray-300">(Opsional)</span></p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">NIK</label>
                                    <input type="text" name="nik" placeholder="16 digit NIK"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">NUPTK</label>
                                    <input type="text" name="nuptk" placeholder="16 digit NUPTK"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Kode Guru</label>
                                    <input type="text" name="kode_guru" placeholder="GR-XXX"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Jenis Kelamin</label>
                                    <select name="jenis_kelamin"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white focus:border-transparent">
                                        <option value="">--</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showAdd=false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-500 transition-all shadow-md">
                                Simpan Pegawai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── EDIT MODAL ── --}}
        <div x-show="showEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div x-show="showEdit"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showEdit=false" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

            <div x-show="showEdit"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="h-1 bg-gradient-to-r from-amber-400 to-orange-500"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Edit Data Pegawai</h3>
                                <p class="text-xs text-gray-400" x-text="eu.name"></p>
                            </div>
                        </div>
                        <button @click="showEdit=false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" :action="`/manajemen-pegawai/${eu.id}`" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" :value="eu.name" required
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" :value="eu.email" required
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Password Baru</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jabatan / Role <span class="text-red-500">*</span></label>
                                <select name="role" required
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent bg-white">
                                    @foreach ($allRoles as $role)
                                        <option :value="'{{ $role->name }}'" :selected="eu.role === '{{ $role->name }}'">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Data Kepegawaian <span class="font-normal text-gray-300">(Opsional)</span></p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">NIK</label>
                                    <input type="text" name="nik" :value="eu.nik" placeholder="16 digit NIK"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">NUPTK</label>
                                    <input type="text" name="nuptk" :value="eu.nuptk" placeholder="16 digit NUPTK"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Kode Guru</label>
                                    <input type="text" name="kode_guru" :value="eu.kode_guru" placeholder="GR-XXX"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Jenis Kelamin</label>
                                    <select name="jenis_kelamin"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white focus:border-transparent">
                                        <option value="">--</option>
                                        <option value="L" :selected="eu.jenis_kelamin === 'L'">Laki-laki</option>
                                        <option value="P" :selected="eu.jenis_kelamin === 'P'">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showEdit=false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-amber-500 text-white rounded-xl text-sm font-bold hover:bg-amber-400 transition-all shadow-md">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── DELETE MODAL ── --}}
        <div x-show="showDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div x-show="showDelete"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showDelete=false" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

            <div x-show="showDelete"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="h-1 bg-gradient-to-r from-red-500 to-rose-500"></div>
                <div class="p-6">
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Hapus Pegawai?</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Akun <strong class="text-gray-900" x-text="du.name"></strong> akan dihapus permanen beserta data kepegawaiannya. Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>

                    <form method="POST" :action="`/manajemen-pegawai/${du.id}`">
                        @csrf
                        @method('DELETE')
                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="showDelete=false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-500 transition-all shadow-md">
                                Ya, Hapus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── IMPORT UPDATE MODAL ── --}}
        <div x-show="showImport" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            {{-- No backdrop click close — intentional, user must use X or Batal --}}
            <div x-show="showImport"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

            <div x-show="showImport"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Import Update Massal</h3>
                                <p class="text-xs text-gray-400">Perbarui data pegawai via file Excel</p>
                            </div>
                        </div>
                        <button @click="showImport=false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Step 1: Download template --}}
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 mb-4">
                        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">Langkah 1 — Download Template</p>
                        <p class="text-sm text-emerald-700 mb-3">Download template Excel yang sudah berisi seluruh data pegawai saat ini. Edit data yang diinginkan, lalu upload kembali.</p>
                        <a href="{{ route('manajemen-pegawai.download-template') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-500 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download Template Excel
                        </a>
                    </div>

                    {{-- Warning --}}
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 mb-4 flex gap-3">
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div class="text-xs text-amber-700 leading-relaxed">
                            <strong class="font-bold">Jangan ubah format Excel!</strong> Kolom <code class="bg-amber-100 px-1 rounded font-mono">user_id</code> wajib tetap ada dan tidak boleh diubah nilainya. Hanya isi data pada kolom yang tersedia — jangan tambah, hapus, atau ubah urutan kolom.
                        </div>
                    </div>

                    {{-- Step 2: Upload --}}
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Langkah 2 — Upload File yang Sudah Diedit</p>
                    <form method="POST" action="{{ route('manajemen-pegawai.import-update') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div x-data="{ fileName: '' }">
                            <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/50 transition-all group">
                                <div x-show="!fileName" class="flex flex-col items-center gap-1.5 pointer-events-none">
                                    <svg class="w-8 h-8 text-gray-300 group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="text-sm text-gray-400">Klik untuk pilih file <strong>.xlsx</strong> atau <strong>.xls</strong></span>
                                    <span class="text-xs text-gray-300">Maks. 5 MB</span>
                                </div>
                                <div x-show="fileName" class="flex items-center gap-2 pointer-events-none">
                                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm font-semibold text-emerald-700" x-text="fileName"></span>
                                </div>
                                <input type="file" name="file_import" accept=".xlsx,.xls" required class="hidden"
                                    @change="fileName = $event.target.files[0]?.name ?? ''">
                            </label>
                        </div>

                        <div class="flex justify-end gap-3 pt-1">
                            <button type="button" @click="showImport=false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-500 transition-all shadow-md">
                                Proses Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ─── PAGE CONTENT ─── --}}
        <div class="py-6">
            <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

                {{-- Stats Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-gray-900">{{ $totalPegawai }}</div>
                            <div class="text-xs font-semibold text-gray-400">Total Pegawai</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-gray-900">{{ $totalDenganNuptk }}</div>
                            <div class="text-xs font-semibold text-gray-400">Terdaftar NUPTK</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-gray-900">{{ $totalRoles }}</div>
                            <div class="text-xs font-semibold text-gray-400">Jenis Role/Jabatan</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-gray-900">{{ $pegawaiBaru }}</div>
                            <div class="text-xs font-semibold text-gray-400">Baru Bulan Ini</div>
                        </div>
                    </div>
                </div>

                {{-- Import Errors --}}
                @if(session('import_errors'))
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-red-700 mb-2">Beberapa baris gagal diproses saat import:</p>
                                <ul class="space-y-1">
                                    @foreach(session('import_errors') as $err)
                                        <li class="text-xs text-red-600">• {{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Search & Filter --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <form method="GET" action="{{ route('manajemen-pegawai.index') }}" class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email pegawai..."
                                class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <select name="role" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent min-w-[180px]">
                            <option value="">Semua Role</option>
                            @foreach ($allRoles as $role)
                                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-500 transition-colors shrink-0">
                            Filter
                        </button>
                        @if(request('search') || request('role'))
                            <a href="{{ route('manajemen-pegawai.index') }}" class="px-4 py-2.5 border border-gray-200 text-gray-500 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors text-center shrink-0">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Table --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900">Daftar Pegawai</h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Menampilkan {{ $pegawai->count() }} dari {{ $pegawai->total() }} pegawai
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            @if(request('search') || request('role'))
                                <span class="text-xs bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full font-bold border border-indigo-100">
                                    Filter aktif
                                </span>
                            @endif
                            <button @click="showImport = true"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Import Update
                            </button>
                            <button @click="showAdd = true"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Pegawai
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider w-12">#</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Pegawai</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Role / Jabatan</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">NIK / NUPTK</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Bergabung</th>
                                    <th class="text-right px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse ($pegawai as $user)
                                    <tr class="hover:bg-gray-50/70 transition-colors group">
                                        <td class="px-6 py-4 text-gray-400 font-mono text-xs">{{ $pegawai->firstItem() + $loop->index }}</td>

                                        {{-- Pegawai info --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if ($user->avatar)
                                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                                                @else
                                                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center shrink-0 text-indigo-600 font-black text-sm border border-indigo-200">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <div class="font-semibold text-gray-900 leading-tight truncate">{{ $user->name }}</div>
                                                    <div class="text-xs text-gray-400 truncate">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Roles --}}
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse ($user->roles as $role)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold {{ $roleColors[$role->name] ?? $roleColorDefault }}">
                                                        {{ $role->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-gray-300 italic">Tanpa role</span>
                                                @endforelse
                                            </div>
                                        </td>

                                        {{-- NIK / NUPTK / Kode --}}
                                        <td class="px-6 py-4">
                                            @if ($user->masterGuru)
                                                <div class="space-y-1">
                                                    @if ($user->masterGuru->nik)
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-[10px] font-bold text-slate-400 uppercase">NIK</span>
                                                            <div class="font-mono text-xs text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">{{ $user->masterGuru->nik }}</div>
                                                        </div>
                                                    @endif
                                                    @if ($user->masterGuru->nuptk)
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-[10px] font-bold text-indigo-400 uppercase">NUPTK</span>
                                                            <div class="font-mono text-xs text-gray-700 bg-gray-100 px-2 py-0.5 rounded-md">{{ $user->masterGuru->nuptk }}</div>
                                                        </div>
                                                    @endif
                                                    @if ($user->masterGuru->kode_guru)
                                                        <div class="text-xs text-gray-500">{{ $user->masterGuru->kode_guru }}</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>

                                        {{-- Joined --}}
                                        <td class="px-6 py-4 text-xs text-gray-400">
                                            {{ $user->created_at->format('d M Y') }}
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                {{-- Edit --}}
                                                <button
                                                    @click="openEdit(@js([
                                                        'id'            => $user->id,
                                                        'name'          => $user->name,
                                                        'email'         => $user->email,
                                                        'role'          => $user->roles->first()?->name ?? '',
                                                        'nik'           => $user->masterGuru?->nik,
                                                        'nuptk'         => $user->masterGuru?->nuptk,
                                                        'kode_guru'     => $user->masterGuru?->kode_guru,
                                                        'jenis_kelamin' => $user->masterGuru?->jenis_kelamin,
                                                    ]))"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-amber-600 hover:bg-amber-50 transition-colors"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>

                                                {{-- Delete (hidden for self) --}}
                                                @if ($user->id !== Auth::id())
                                                    <button
                                                        @click="openDelete(@js(['id' => $user->id, 'name' => $user->name]))"
                                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors"
                                                        title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                @else
                                                    <div class="w-8 h-8 flex items-center justify-center" title="Tidak dapat menghapus akun sendiri">
                                                        <svg class="w-4 h-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900">Tidak ada pegawai ditemukan</p>
                                                    <p class="text-sm text-gray-400 mt-0.5">
                                                        @if(request('search') || request('role'))
                                                            Coba ubah filter pencarian
                                                        @else
                                                            Tambahkan pegawai pertama dengan tombol di atas
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($pegawai->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $pegawai->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>{{-- end x-data --}}

</x-app-layout>
