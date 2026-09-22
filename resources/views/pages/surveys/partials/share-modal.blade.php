<div x-data="surveyShareModal()"
    x-on:open-survey-share.window="open($event.detail.surveyId)"
    x-cloak
    x-show="isOpen"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true">

    <!-- Backdrop -->
    <div x-show="isOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        @click="close()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="isOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-100">

            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900" id="modal-title">Bagikan Akses Survei</h3>
                        <p class="text-xs text-slate-500 font-medium line-clamp-1" x-text="surveyData?.title ?? 'Memuat survei...'"></p>
                    </div>
                </div>
                <button type="button" @click="close()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                <!-- Alerts -->
                <template x-if="errorMessage">
                    <div class="p-3.5 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl flex items-center justify-between">
                        <span x-text="errorMessage"></span>
                        <button type="button" @click="errorMessage = ''" class="text-red-500 hover:text-red-700 ml-2">&times;</button>
                    </div>
                </template>

                <template x-if="successMessage">
                    <div class="p-3.5 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-xl flex items-center justify-between">
                        <span x-text="successMessage"></span>
                        <button type="button" @click="successMessage = ''" class="text-green-500 hover:text-green-700 ml-2">&times;</button>
                    </div>
                </template>

                <!-- Loading Spinner -->
                <div x-show="loading" class="py-12 flex flex-col items-center justify-center space-y-3">
                    <div class="w-8 h-8 border-3 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-xs text-slate-500 font-medium">Memuat data akses...</p>
                </div>

                <div x-show="!loading" class="space-y-6">
                    <!-- Form Tambah Kolaborator (Hanya jika can_manage) -->
                    <div x-show="canManage" class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100 space-y-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tambah Kolaborator Baru</label>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
                            <!-- Select User -->
                            <div class="sm:col-span-6">
                                <select x-model="newUserId" class="w-full text-xs font-medium rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2">
                                    <option value="">-- Pilih Pengguna SISFO --</option>
                                    <template x-for="user in availableUsers" :key="user.id">
                                        <option :value="user.id" x-text="user.name + ' (' + user.email + ')'"></option>
                                    </template>
                                </select>
                            </div>
                            <!-- Role Dropdown -->
                            <div class="sm:col-span-3">
                                <select x-model="newRole" class="w-full text-xs font-medium rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2">
                                    <option value="editor">Editor</option>
                                    <option value="viewer">Viewer</option>
                                </select>
                            </div>
                            <!-- Button Tambah -->
                            <div class="sm:col-span-3">
                                <button type="button"
                                    @click="addCollaborator()"
                                    :disabled="saving || !newUserId"
                                    class="w-full py-2 px-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all flex items-center justify-center shadow-sm">
                                    <span x-show="!saving">Bagikan</span>
                                    <span x-show="saving">Menyimpan...</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 text-[11px] text-slate-500">
                            <span class="flex items-center">
                                <strong class="text-indigo-700 mr-1">Editor:</strong> Edit soal, target, & lihat hasil
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="flex items-center">
                                <strong class="text-indigo-700 mr-1">Viewer:</strong> Lihat & unduh hasil
                            </span>
                        </div>
                    </div>

                    <!-- Daftar Akses Pengguna -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Pengguna yang Memiliki Akses</label>
                            <span class="text-xs font-medium text-slate-400" x-text="(shares.length + 1) + ' orang'"></span>
                        </div>

                        <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden bg-white shadow-sm">
                            <!-- Pemilik Survei (Owner) -->
                            <div class="p-3.5 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center border border-purple-200">
                                        <span x-text="surveyData?.owner?.name ? surveyData.owner.name.substring(0, 2).toUpperCase() : 'OW'"></span>
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <p class="text-xs font-bold text-slate-800" x-text="surveyData?.owner?.name ?? 'Pembuat Survei'"></p>
                                            <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wider rounded-full bg-purple-100 text-purple-700 border border-purple-200">Pemilik</span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 font-medium" x-text="surveyData?.owner?.email ?? '-'"></p>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-400 font-medium pr-2">Akses Penuh</span>
                            </div>

                            <!-- Daftar Kolaborator -->
                            <template x-for="share in shares" :key="share.id">
                                <div class="p-3.5 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-700 font-bold text-xs flex items-center justify-center border border-blue-100">
                                            <span x-text="share.name ? share.name.substring(0, 2).toUpperCase() : 'KB'"></span>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800" x-text="share.name"></p>
                                            <p class="text-[11px] text-slate-400 font-medium" x-text="share.email"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <!-- Role Selector / Badge -->
                                        <template x-if="canManage">
                                            <select :value="share.role"
                                                @change="updateRole(share.user_id, $event.target.value)"
                                                class="text-xs font-bold rounded-lg border-slate-200 py-1 px-2.5 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="editor">Editor</option>
                                                <option value="viewer">Viewer</option>
                                            </select>
                                        </template>
                                        <template x-if="!canManage">
                                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full"
                                                :class="share.role === 'editor' ? 'bg-teal-100 text-teal-700' : 'bg-cyan-100 text-cyan-700'"
                                                x-text="share.role === 'editor' ? 'Editor' : 'Viewer'"></span>
                                        </template>

                                        <!-- Tombol Hapus Akses -->
                                        <template x-if="canManage">
                                            <button type="button"
                                                @click="removeCollaborator(share.user_id, share.name)"
                                                title="Cabut Akses"
                                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="shares.length === 0">
                                <div class="p-6 text-center text-slate-400 text-xs">
                                    Belum ada kolaborator yang ditambahkan ke survei ini.
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Salin Link Survei -->
                    <div class="pt-2 border-t border-slate-100">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Tautan Pengisian Survei</label>
                        <div class="flex items-center space-x-2">
                            <input type="text"
                                readonly
                                :value="surveyData?.share_url ?? ''"
                                class="flex-1 text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-600 font-mono select-all focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <button type="button"
                                @click="copyShareLink()"
                                class="px-3.5 py-2 text-xs font-bold rounded-xl transition-all flex items-center space-x-1.5 shadow-sm"
                                :class="copied ? 'bg-green-600 text-white' : 'bg-slate-800 hover:bg-slate-900 text-white'">
                                <template x-if="!copied">
                                    <span class="flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Salin
                                    </span>
                                </template>
                                <template x-if="copied">
                                    <span class="flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Tersalin!
                                    </span>
                                </template>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="button"
                    @click="close()"
                    class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-colors shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function surveyShareModal() {
    return {
        isOpen: false,
        loading: false,
        saving: false,
        copied: false,
        surveyId: null,
        surveyData: null,
        shares: [],
        availableUsers: [],
        canManage: false,
        newUserId: '',
        newRole: 'editor',
        errorMessage: '',
        successMessage: '',

        open(surveyId) {
            this.surveyId = surveyId;
            this.isOpen = true;
            this.resetMessages();
            this.fetchShares();
        },

        close() {
            this.isOpen = false;
            this.surveyId = null;
            this.surveyData = null;
            this.shares = [];
            this.availableUsers = [];
            this.resetMessages();
        },

        resetMessages() {
            this.errorMessage = '';
            this.successMessage = '';
        },

        async fetchShares() {
            this.loading = true;
            try {
                const res = await fetch(`/surveys/${this.surveyId}/shares`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    throw new Error(data.message || 'Gagal memuat data kolaborator');
                }
                const data = await res.json();
                this.surveyData = data.survey;
                this.shares = data.shares || [];
                this.availableUsers = data.available_users || [];
                this.canManage = data.can_manage;
                this.newUserId = '';
            } catch (err) {
                this.errorMessage = err.message || 'Terjadi kesalahan saat memuat data.';
            } finally {
                this.loading = false;
            }
        },

        async addCollaborator() {
            if (!this.newUserId) {
                this.errorMessage = 'Pilih pengguna terlebih dahulu.';
                return;
            }
            this.saving = true;
            this.resetMessages();
            try {
                const res = await fetch(`/surveys/${this.surveyId}/shares`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        user_id: this.newUserId,
                        role: this.newRole
                    })
                });
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Gagal menambahkan kolaborator');
                }
                this.successMessage = data.message;
                await this.fetchShares();
            } catch (err) {
                this.errorMessage = err.message || 'Gagal menambahkan kolaborator';
            } finally {
                this.saving = false;
            }
        },

        async updateRole(userId, newRole) {
            this.resetMessages();
            try {
                const res = await fetch(`/surveys/${this.surveyId}/shares/${userId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ role: newRole })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal memperbarui peran');
                this.successMessage = data.message;
                const idx = this.shares.findIndex(s => s.user_id === userId);
                if (idx !== -1) {
                    this.shares[idx].role = newRole;
                }
            } catch (err) {
                this.errorMessage = err.message || 'Gagal memperbarui peran';
                this.fetchShares();
            }
        },

        async removeCollaborator(userId, userName) {
            if (!confirm(`Apakah Anda yakin ingin mencabut akses kolaborasi untuk "${userName}"?`)) return;
            this.resetMessages();
            try {
                const res = await fetch(`/surveys/${this.surveyId}/shares/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal mencabut akses');
                this.successMessage = data.message;
                await this.fetchShares();
            } catch (err) {
                this.errorMessage = err.message || 'Gagal mencabut akses';
            }
        },

        copyShareLink() {
            if (!this.surveyData?.share_url) return;
            navigator.clipboard.writeText(this.surveyData.share_url).then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2500);
            });
        }
    };
}
</script>
