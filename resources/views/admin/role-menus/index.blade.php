<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-extrabold text-slate-800">Pengaturan Menu per Role</h2>
            <p class="mt-1 text-sm text-slate-500">Sembunyikan fitur yang belum digunakan dan atur urutannya tanpa menghapus akses atau data.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="roleMenuManager({{ Illuminate\Support\Js::from($menus) }})">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="grid gap-5 border-b border-slate-100 p-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                <div>
                    <label for="role-selector" class="mb-2 block text-xs font-extrabold uppercase tracking-[0.18em] text-slate-500">Role yang diatur</label>
                    <select id="role-selector"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 focus:border-red-500 focus:ring-red-500"
                        onchange="window.location.href = '{{ route('super-admin.role-menus.index') }}?role=' + this.value">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected($selectedRole?->is($role))>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="text-xl font-black text-slate-800" x-text="items.length">0</div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total</div>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 px-4 py-3">
                        <div class="text-xl font-black text-emerald-700" x-text="visibleCount">0</div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Tampil</div>
                    </div>
                    <div class="rounded-2xl bg-amber-50 px-4 py-3">
                        <div class="text-xl font-black text-amber-700" x-text="hiddenCount">0</div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Sembunyi</div>
                    </div>
                </div>
            </div>

            @if($selectedRole)
                <form method="POST" action="{{ route('super-admin.role-menus.update', $selectedRole) }}">
                    @csrf
                    @method('PUT')

                    <template x-for="(item, index) in items" :key="item.key">
                        <div>
                            <input type="hidden" :name="`menus[${index}][key]`" :value="item.key">
                            <input type="hidden" :name="`menus[${index}][visible]`" :value="item.visible ? 1 : 0">
                            <input type="hidden" :name="`menus[${index}][order]`" :value="index">
                        </div>
                    </template>

                    <div class="grid min-h-[560px] lg:grid-cols-[minmax(0,0.9fr)_minmax(380px,1.1fr)]">
                        <aside class="border-b border-slate-100 bg-slate-50/70 p-6 lg:border-b-0 lg:border-r">
                            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-red-700">Kontrol Menu</span>
                            <h3 class="mt-3 text-lg font-extrabold text-slate-800">Role {{ $selectedRole->name }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Perubahan hanya memengaruhi navigasi role ini. Permission dan alamat halaman tetap tidak berubah.</p>

                            <div class="mt-6">
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Cari menu</label>
                                <div class="relative mt-2">
                                    <svg class="absolute left-3 top-3.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" /></svg>
                                    <input x-model="search" type="search" placeholder="Nama fitur atau kelompok..."
                                        class="w-full rounded-xl border-slate-200 bg-white py-3 pl-10 pr-4 text-sm focus:border-red-500 focus:ring-red-500">
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <button type="button" @click="setAll(true)" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs font-extrabold text-emerald-700 hover:bg-emerald-100">Tampilkan semua</button>
                                <button type="button" @click="setAll(false)" class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs font-extrabold text-amber-700 hover:bg-amber-100">Sembunyikan semua</button>
                            </div>

                            <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-xs leading-5 text-blue-800">
                                <strong>Catatan:</strong> menu Pengaturan Menu milik Super Admin dikunci agar halaman ini tidak tersembunyi. Judul kelompok yang seluruh menunya disembunyikan ikut tidak ditampilkan.
                            </div>
                        </aside>

                        <div class="p-6">
                            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-400">Preview navigasi</div>
                                    <p class="mt-1 text-sm text-slate-500">Seret kartu dalam kelompok yang sama untuk mengubah urutan.</p>
                                </div>
                                <div class="flex items-center gap-2 rounded-full bg-slate-100 px-3 py-2 text-xs font-bold text-slate-500">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Preview langsung
                                </div>
                            </div>

                            <div class="max-h-[650px] space-y-6 overflow-y-auto pr-2">
                                <template x-for="section in sections" :key="section">
                                    <section x-show="sectionItems(section).some(item => matchesSearch(item))">
                                        <div class="mb-2 flex items-center gap-3">
                                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400" x-text="section"></span>
                                            <span class="h-px flex-1 bg-slate-100"></span>
                                        </div>

                                        <div class="space-y-2">
                                            <template x-for="item in sectionItems(section)" :key="item.key">
                                                <article x-show="matchesSearch(item)" draggable="true"
                                                    @dragstart="draggedKey = item.key"
                                                    @dragend="draggedKey = null"
                                                    @dragover.prevent
                                                    @drop.prevent="dropBefore(item)"
                                                    :class="item.visible ? 'border-slate-200 bg-white' : 'border-dashed border-slate-200 bg-slate-50 opacity-60'"
                                                    class="group flex items-center gap-3 rounded-2xl border p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                                    <div class="cursor-grab rounded-xl bg-slate-100 p-2 text-slate-400 active:cursor-grabbing">
                                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm-1.5 7.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM16 4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm-1.5 7.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm1.5 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" /></svg>
                                                    </div>
                                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-red-700 text-white shadow-sm">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10" /></svg>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="truncate text-sm font-extrabold text-slate-800" x-text="item.label"></div>
                                                        <div class="mt-0.5 text-[10px] font-bold uppercase tracking-wider" :class="item.visible ? 'text-emerald-600' : 'text-amber-600'" x-text="item.visible ? 'Ditampilkan' : 'Disembunyikan'"></div>
                                                    </div>
                                                    <button type="button" @click="toggle(item)" :disabled="item.locked"
                                                        :title="item.locked ? 'Menu ini wajib tetap tampil' : (item.visible ? 'Sembunyikan menu' : 'Tampilkan menu')"
                                                        :class="item.visible ? 'bg-emerald-500' : 'bg-slate-300'"
                                                        class="relative h-7 w-12 rounded-full transition disabled:cursor-not-allowed disabled:opacity-60">
                                                        <span :class="item.visible ? 'translate-x-5' : 'translate-x-1'" class="absolute left-0 top-1 h-5 w-5 rounded-full bg-white shadow transition"></span>
                                                    </button>
                                                </article>
                                            </template>
                                        </div>
                                    </section>
                                </template>

                                <div x-show="!items.some(item => matchesSearch(item))" class="rounded-2xl border border-dashed border-slate-200 py-12 text-center text-sm text-slate-500">
                                    Menu yang dicari tidak ditemukan.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-white p-5 sm:flex-row sm:items-center sm:justify-end">
                        <button type="submit" form="reset-role-menu-form" onclick="return confirm('Kembalikan semua menu role ini ke tampilan dan urutan bawaan?')"
                            class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-extrabold text-slate-600 hover:bg-slate-50">Kembalikan bawaan</button>
                        <button type="submit" class="rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-red-200 hover:from-red-700 hover:to-red-800">Simpan pengaturan</button>
                    </div>
                </form>

                <form id="reset-role-menu-form" method="POST" action="{{ route('super-admin.role-menus.reset', $selectedRole) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @else
                <div class="p-12 text-center text-slate-500">Belum ada role yang dapat diatur.</div>
            @endif
        </section>
    </div>

    @push('scripts')
        <script>
            window.roleMenuManager = initialItems => ({
                items: initialItems,
                search: '',
                draggedKey: null,

                get visibleCount() {
                    return this.items.filter(item => item.visible).length;
                },

                get hiddenCount() {
                    return this.items.length - this.visibleCount;
                },

                get sections() {
                    return [...new Set(this.items.map(item => item.section))];
                },

                sectionItems(section) {
                    return this.items.filter(item => item.section === section);
                },

                matchesSearch(item) {
                    const query = this.search.trim().toLowerCase();
                    return !query || `${item.label} ${item.section}`.toLowerCase().includes(query);
                },

                toggle(item) {
                    if (!item.locked) item.visible = !item.visible;
                },

                setAll(visible) {
                    this.items.forEach(item => {
                        if (!item.locked) item.visible = visible;
                    });
                },

                dropBefore(target) {
                    const sourceIndex = this.items.findIndex(item => item.key === this.draggedKey);
                    const targetIndex = this.items.findIndex(item => item.key === target.key);
                    if (sourceIndex < 0 || targetIndex < 0 || this.items[sourceIndex].section !== target.section) return;

                    const [source] = this.items.splice(sourceIndex, 1);
                    const refreshedTarget = this.items.findIndex(item => item.key === target.key);
                    this.items.splice(refreshedTarget, 0, source);
                    this.draggedKey = null;
                },
            });
        </script>
    @endpush
</x-app-layout>
