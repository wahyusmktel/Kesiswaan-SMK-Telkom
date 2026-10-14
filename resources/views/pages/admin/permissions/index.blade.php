<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-100 text-red-600 font-black text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </span>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                        {{ __('Manajemen Hak Akses & Kewenangan') }}
                    </h2>
                </div>
                <p class="text-sm text-slate-500 mt-1 ml-12">
                    Kelola izin dan kewenangan setiap peran pengguna (role) secara terpusat dan interaktif.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openNewPermissionModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Hak Akses Baru
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="permissionManager()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- SIDEBAR: DAFTAR ROLE --}}
                <div class="lg:col-span-4 xl:col-span-3">
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden sticky top-6">
                        <div class="p-4 border-b border-slate-100 bg-slate-50/70">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xs font-black text-slate-600 uppercase tracking-wider">Daftar Role</h3>
                                <span class="text-[11px] font-bold text-slate-400 bg-white border border-slate-200 px-2 py-0.5 rounded-full">{{ $roles->count() }} Role</span>
                            </div>
                            <div class="relative">
                                <input type="text" x-model="roleSearch" placeholder="Cari role..."
                                    class="w-full text-xs rounded-xl border-slate-200 bg-white py-2 pl-8 pr-3 text-slate-700 placeholder-slate-400 focus:border-red-500 focus:ring-red-500 shadow-sm transition">
                                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="divide-y divide-slate-100 max-h-[calc(100vh-280px)] overflow-y-auto">
                            @foreach($roles as $role)
                                <button type="button"
                                    @click="selectRole({{ $role->id }}, '{{ addslashes($role->name) }}')"
                                    x-show="matchesRoleSearch('{{ strtolower($role->name) }}')"
                                    :class="selectedRoleId === {{ $role->id }} ? 'bg-red-50/80 border-l-4 border-red-600 pl-4 text-red-900 font-bold' : 'hover:bg-slate-50 text-slate-700'"
                                    class="w-full text-left px-5 py-3.5 transition-all flex items-center justify-between group">
                                    <div class="flex flex-col min-w-0 pr-2">
                                        <span class="text-sm truncate" :class="selectedRoleId === {{ $role->id }} ? 'font-black text-red-700' : 'font-bold text-slate-700 group-hover:text-red-700'">
                                            {{ $role->name }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full" :class="selectedRoleId === {{ $role->id }} ? 'bg-red-500' : 'bg-slate-300'"></span>
                                            <span id="role-badge-{{ $role->id }}">{{ $role->permissions->count() }}</span> dari {{ $allPermissions->count() }} Hak Akses
                                        </span>
                                    </div>
                                    <svg class="w-4 h-4 flex-shrink-0 transition-transform" 
                                        :class="selectedRoleId === {{ $role->id }} ? 'text-red-600 translate-x-1' : 'text-slate-300 group-hover:text-red-400 group-hover:translate-x-1'" 
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- MAIN CONTENT: CHECKLIST HAK AKSES --}}
                <div class="lg:col-span-8 xl:col-span-9">
                    
                    {{-- EMPTY STATE (Jika belum ada role dipilih) --}}
                    <div x-show="!selectedRoleId" class="bg-white rounded-3xl border border-slate-200/80 p-16 text-center shadow-sm">
                        <div class="w-20 h-20 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-800">Pilih Role di Kolom Kiri</h3>
                        <p class="text-sm text-slate-500 max-w-md mx-auto mt-1">
                            Pilih salah satu peran pengguna untuk melihat, mencentang, atau mencabut kewenangan hak akses dalam sistem.
                        </p>
                    </div>

                    {{-- ACTIVE ROLE PERMISSIONS CONTAINER --}}
                    <div x-show="selectedRoleId" x-cloak class="space-y-6">

                        {{-- STICKY CONTROL & ACTION BAR --}}
                        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm sticky top-6 z-20">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-400">Pengaturan Hak Akses Untuk:</span>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-800 tracking-tight mt-0.5" x-text="selectedRoleName">Role Name</h3>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" @click="selectAll()"
                                        class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                                        Pilih Semua
                                    </button>
                                    <button type="button" @click="deselectAll()"
                                        class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                                        Batal Semua
                                    </button>
                                    <button type="button" @click="savePermissions()" :disabled="saving"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-black rounded-xl transition-all shadow-md shadow-red-200 disabled:opacity-50">
                                        <template x-if="saving">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                        </template>
                                        <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span x-text="saving ? 'Menyimpan...' : 'Simpan Perubahan'">Simpan Perubahan</span>
                                    </button>
                                </div>
                            </div>

                            {{-- SUB-BAR: SEARCH & LIVE STATS --}}
                            <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                                <div class="md:col-span-7">
                                    <div class="relative">
                                        <input type="text" x-model="searchQuery" placeholder="Cari izin / modul... (contoh: izin, erapor, guru, rab)"
                                            class="w-full text-xs rounded-xl border-slate-200 bg-slate-50/50 py-2.5 pl-9 pr-8 text-slate-700 placeholder-slate-400 focus:bg-white focus:border-red-500 focus:ring-red-500 transition">
                                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="md:col-span-5 flex items-center justify-end gap-3 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-slate-500 font-medium">Aktif:</span>
                                        <span class="font-black text-emerald-700" x-text="activeCount">0</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                                        <span class="text-slate-500 font-medium">Total:</span>
                                        <span class="font-black text-slate-700">{{ $allPermissions->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PERMISSION GROUPS GRID --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach($permissions as $category => $categoryPermissions)
                                <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm transition-all hover:shadow-md flex flex-col"
                                    x-show="categoryHasMatches('{{ addslashes($category) }}')"
                                    x-data="{
                                        catName: '{{ addslashes($category) }}',
                                        get items() {
                                            return Array.from(this.$el.querySelectorAll('.permission-item-box'));
                                        },
                                        get allChecked() {
                                            const checkboxes = Array.from(this.$el.querySelectorAll('.perm-checkbox'));
                                            return checkboxes.length > 0 && checkboxes.every(cb => cb.checked);
                                        },
                                        toggleCategory() {
                                            const checkboxes = Array.from(this.$el.querySelectorAll('.perm-checkbox'));
                                            const newState = !this.allChecked;
                                            checkboxes.forEach(cb => {
                                                cb.checked = newState;
                                                updatePermissionState(cb.value, newState);
                                            });
                                        }
                                    }">
                                    
                                    {{-- CATEGORY HEADER --}}
                                    <div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-slate-100">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-black text-xs shadow-sm">
                                                @switch($category)
                                                    @case('Manajemen Pengguna') 👤 @break
                                                    @case('Role & Hak Akses') 🛡️ @break
                                                    @case('Master Data') 🗄️ @break
                                                    @case('Kesiswaan & Kedisiplinan') 🎓 @break
                                                    @case('Bimbingan Konseling (BK)') 💬 @break
                                                    @case('Wali Kelas') 📋 @break
                                                    @case('Kurikulum & Pembelajaran') 📚 @break
                                                    @case('Piket') ⏱️ @break
                                                    @case('Operator') ⚙️ @break
                                                    @case('Tata Usaha & Persuratan') ✉️ @break
                                                    @case('e-Rapor SISFO') 📊 @break
                                                    @case('KAUR SDM (Kepegawaian)') 👔 @break
                                                    @case('Security & Gerbang') 🚨 @break
                                                    @case('UKS & Kesehatan Siswa') 🩺 @break
                                                    @case('Prakerin, Hubin & Unit Produksi') 🏭 @break
                                                    @case('Kantin Sekolah') 🍲 @break
                                                    @case('Anggaran (RAB) & Keuangan') 💰 @break
                                                    @case('OKR & Kinerja Sekolah') 🎯 @break
                                                    @case('Sarana, Prasarana & CCTV') 🏢 @break
                                                    @case('UKK / Kaprodi') 📝 @break
                                                    @case('Survei & Evaluasi') 📈 @break
                                                    @case('LMS & E-Learning') 💻 @break
                                                    @case('Eksekutif & Analytics') 📈 @break
                                                    @default 🔐
                                                @endswitch
                                            </span>
                                            <div>
                                                <h4 class="font-black text-slate-800 text-xs uppercase tracking-wider">{{ $category }}</h4>
                                                <span class="text-[10px] text-slate-400 font-bold">
                                                    {{ $categoryPermissions->count() }} Hak Akses
                                                </span>
                                            </div>
                                        </div>

                                        <button type="button" @click="toggleCategory()"
                                            class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-700 border border-slate-200/60 transition">
                                            <span x-text="allChecked ? 'Batal' : 'Pilih Semua'">Pilih Semua</span>
                                        </button>
                                    </div>

                                    {{-- PERMISSIONS CHECKBOX LIST --}}
                                    <div class="space-y-2 flex-1">
                                        @foreach($categoryPermissions as $permission)
                                            <label class="permission-item-box flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer group border border-transparent hover:border-slate-100"
                                                x-show="matchesSearch('{{ strtolower($permission->name) }}', '{{ strtolower($category) }}')"
                                                data-name="{{ $permission->name }}"
                                                data-cat="{{ $category }}">
                                                <div class="relative flex items-center mt-0.5">
                                                    <input type="checkbox" value="{{ $permission->name }}"
                                                        @change="handleCheckChange($event)"
                                                        class="perm-checkbox w-4 h-4 rounded-md border-slate-300 text-red-600 focus:ring-red-500 transition cursor-pointer">
                                                </div>
                                                <div class="flex flex-col min-w-0">
                                                    <span class="text-xs font-bold text-slate-700 group-hover:text-slate-900 transition-colors font-mono">
                                                        {{ $permission->name }}
                                                    </span>
                                                    <span class="text-[10px] text-slate-400 capitalize mt-0.5">
                                                        {{ str_replace(['manage ', 'view ', 'configure '], ['Kelola ', 'Lihat ', 'Konfigurasi '], $permission->name) }}
                                                    </span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- MODAL: TAMBAH HAK AKSES BARU --}}
        <div id="new-perm-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" onclick="closeNewPermissionModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-slate-100 sm:align-middle">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center font-bold text-sm">
                                ＋
                            </span>
                            <h3 class="text-base font-black text-slate-800">Tambah Hak Akses Baru</h3>
                        </div>
                        <button type="button" onclick="closeNewPermissionModal()" class="text-slate-400 hover:text-slate-600">
                            ✕
                        </button>
                    </div>

                    <form id="new-permission-form" onsubmit="submitNewPermission(event)" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Nama Permission / Hak Akses:
                            </label>
                            <input type="text" name="name" id="new-perm-name" required placeholder="contoh: manage inventaris aset"
                                class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-800 focus:bg-white focus:border-red-500 focus:ring-red-500 transition">
                            <p class="text-[11px] text-slate-400 mt-1.5 leading-relaxed">
                                Gunakan format huruf kecil (snake/kebab atau spasi), diawali kata kerja seperti <code class="bg-slate-100 px-1 py-0.5 rounded text-red-600 font-bold">view</code> atau <code class="bg-slate-100 px-1 py-0.5 rounded text-red-600 font-bold">manage</code>.
                            </p>
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-2">
                            <button type="button" onclick="closeNewPermissionModal()"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" id="new-perm-submit-btn"
                                class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-md shadow-red-200 transition">
                                Tambahkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function permissionManager() {
            return {
                selectedRoleId: {{ $selectedRoleId ?? 'null' }},
                selectedRoleName: '{{ addslashes($roles->firstWhere('id', $selectedRoleId)?->name ?? '') }}',
                activePermissions: new Set(),
                searchQuery: '',
                roleSearch: '',
                saving: false,

                init() {
                    if (this.selectedRoleId) {
                        this.loadRolePermissions(this.selectedRoleId, this.selectedRoleName);
                    }
                },

                get activeCount() {
                    return this.activePermissions.size;
                },

                matchesRoleSearch(roleName) {
                    if (!this.roleSearch.trim()) return true;
                    return roleName.includes(this.roleSearch.trim().toLowerCase());
                },

                matchesSearch(permName, categoryName) {
                    if (!this.searchQuery.trim()) return true;
                    const q = this.searchQuery.trim().toLowerCase();
                    return permName.includes(q) || categoryName.includes(q);
                },

                categoryHasMatches(categoryName) {
                    if (!this.searchQuery.trim()) return true;
                    const q = this.searchQuery.trim().toLowerCase();
                    if (categoryName.toLowerCase().includes(q)) return true;
                    
                    const items = document.querySelectorAll(`.permission-item-box[data-cat="${categoryName}"]`);
                    for (let el of items) {
                        if (el.dataset.name.toLowerCase().includes(q)) return true;
                    }
                    return false;
                },

                selectRole(roleId, roleName) {
                    this.selectedRoleId = roleId;
                    this.selectedRoleName = roleName;
                    this.loadRolePermissions(roleId, roleName);
                },

                loadRolePermissions(roleId, roleName) {
                    fetch(`/super-admin/permissions/${roleId}`)
                        .then(res => res.json())
                        .then(data => {
                            this.activePermissions = new Set(data.permissions || []);
                            
                            // Update checkboxes in DOM
                            document.querySelectorAll('.perm-checkbox').forEach(cb => {
                                cb.checked = this.activePermissions.has(cb.value);
                            });
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Gagal memuat hak akses role.', 'error');
                        });
                },

                handleCheckChange(e) {
                    const perm = e.target.value;
                    if (e.target.checked) {
                        this.activePermissions.add(perm);
                    } else {
                        this.activePermissions.delete(perm);
                    }
                },

                selectAll() {
                    document.querySelectorAll('.perm-checkbox').forEach(cb => {
                        cb.checked = true;
                        this.activePermissions.add(cb.value);
                    });
                },

                deselectAll() {
                    document.querySelectorAll('.perm-checkbox').forEach(cb => {
                        cb.checked = false;
                    });
                    this.activePermissions.clear();
                },

                savePermissions() {
                    if (!this.selectedRoleId) return;
                    this.saving = true;

                    const payload = Array.from(this.activePermissions);

                    fetch(`/super-admin/permissions/${this.selectedRoleId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ permissions: payload })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.saving = false;
                        if (data.success) {
                            // Update sidebar badge
                            const badge = document.getElementById(`role-badge-${this.selectedRoleId}`);
                            if (badge) badge.innerText = data.total_active;

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    })
                    .catch(err => {
                        this.saving = false;
                        console.error(err);
                        Swal.fire('Error', 'Gagal menyimpan perubahan hak akses.', 'error');
                    });
                }
            };
        }

        function updatePermissionState(permName, isChecked) {
            // Helper dispatched from category toggle
            const root = Alpine.$data(document.querySelector('[x-data="permissionManager()"]'));
            if (root) {
                if (isChecked) {
                    root.activePermissions.add(permName);
                } else {
                    root.activePermissions.delete(permName);
                }
            }
        }

        function openNewPermissionModal() {
            document.getElementById('new-perm-modal').classList.remove('hidden');
            setTimeout(() => document.getElementById('new-perm-name').focus(), 100);
        }

        function closeNewPermissionModal() {
            document.getElementById('new-perm-modal').classList.add('hidden');
            document.getElementById('new-permission-form').reset();
        }

        function submitNewPermission(e) {
            e.preventDefault();
            const btn = document.getElementById('new-perm-submit-btn');
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            const name = document.getElementById('new-perm-name').value;

            fetch('{{ route("super-admin.permissions.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name: name })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = 'Tambahkan';
                if (data.success) {
                    closeNewPermissionModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#dc2626'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Perhatian', data.message || 'Gagal menambahkan hak akses.', 'warning');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerText = 'Tambahkan';
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan saat menambahkan hak akses.', 'error');
            });
        }
    </script>
    @endpush
</x-app-layout>
