<x-app-layout>
    <div x-data="cctvManager()" class="mx-auto max-w-[1600px] space-y-6">
        <section class="overflow-hidden rounded-lg bg-slate-950 text-white shadow-xl">
            <div class="grid gap-6 p-6 lg:grid-cols-[1fr_auto] lg:items-center lg:p-8">
                <div>
                    <div class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-red-300">
                        <span class="h-2 w-2 rounded-full {{ $gateway['online'] ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                        Gateway {{ $gateway['online'] ? 'Online' : 'Belum Terhubung' }}
                    </div>
                    <h1 class="text-2xl font-extrabold sm:text-3xl">Manajemen CCTV Sekolah</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">
                        Kelola sumber RTSP, lokasi kamera, dan pengguna yang boleh melihat siaran internal. Kredensial RTSP disimpan terenkripsi dan tidak dikirim ke browser.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('super-admin.cctv.sync-all') }}">
                        @csrf
                        <button class="inline-flex h-11 items-center gap-2 rounded-md border border-white/20 px-4 text-sm font-bold hover:bg-white/10">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M5.6 15A7 7 0 0018 17.4M18.4 9A7 7 0 006 6.6"/></svg>
                            Sinkronkan Semua
                        </button>
                    </form>
                    <button type="button" @click="openCreate()" class="inline-flex h-11 items-center gap-2 rounded-md bg-red-600 px-4 text-sm font-bold hover:bg-red-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Kamera
                    </button>
                </div>
            </div>
        </section>

        @if(session('success') || session('error'))
            <div class="rounded-md border px-4 py-3 text-sm font-semibold {{ session('error') ? 'border-red-200 bg-red-50 text-red-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5">
                <p class="text-xs font-bold uppercase text-slate-500">Total Kamera</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $cameras->total() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5">
                <p class="text-xs font-bold uppercase text-slate-500">Kamera Aktif</p>
                <p class="mt-2 text-3xl font-black text-emerald-600">{{ $stats['active_cameras'] }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5">
                <p class="text-xs font-bold uppercase text-slate-500">Pengguna Berizin</p>
                <p class="mt-2 text-3xl font-black text-blue-600">{{ $stats['authorized_users'] }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5">
                <p class="text-xs font-bold uppercase text-slate-500">Media Gateway</p>
                <p class="mt-2 text-lg font-black {{ $gateway['online'] ? 'text-emerald-600' : 'text-amber-600' }}">{{ $gateway['message'] }}</p>
            </div>
        </section>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-extrabold text-slate-900">Daftar Kamera</h2>
                <p class="mt-1 text-xs text-slate-500">Stream aktif dibuka sesuai permintaan untuk menghemat bandwidth jaringan.</p>
            </div>

            @if($cameras->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.5-2.5v9L15 14m-9 4h7a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="mt-4 font-bold text-slate-800">Belum ada kamera</h3>
                    <p class="mt-1 text-sm text-slate-500">Tambahkan URL RTSP pertama untuk memulai.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Kamera</th>
                                <th class="px-5 py-3">Sumber Aman</th>
                                <th class="px-5 py-3">Akses</th>
                                <th class="px-5 py-3">Sinkronisasi</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($cameras as $camera)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-10 w-10 items-center justify-center rounded-md {{ $camera->is_active ? 'bg-red-50 text-red-600' : 'bg-slate-100 text-slate-400' }}">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.5-2.5v9L15 14m-9 4h7a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </span>
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $camera->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $camera->location ?: 'Lokasi belum diisi' }} · {{ $camera->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <code class="block max-w-[260px] truncate rounded bg-slate-100 px-2 py-1 text-[11px] text-slate-600">{{ $camera->masked_rtsp_url }}</code>
                                        <span class="mt-1 block text-[11px] text-slate-400">{{ $camera->stream_path }}</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="font-bold text-slate-800">{{ $camera->users_count }} pengguna</span>
                                        <p class="mt-1 max-w-[220px] truncate text-xs text-slate-500">{{ $camera->users->pluck('name')->join(', ') ?: 'Hanya Super Admin' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        @php
                                            $syncClass = match($camera->last_sync_status) {
                                                'synced' => 'bg-emerald-50 text-emerald-700',
                                                'failed' => 'bg-red-50 text-red-700',
                                                'disabled' => 'bg-slate-100 text-slate-600',
                                                default => 'bg-amber-50 text-amber-700',
                                            };
                                        @endphp
                                        <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $syncClass }}">{{ ucfirst($camera->last_sync_status ?: 'pending') }}</span>
                                        <p class="mt-2 text-[11px] text-slate-400">{{ $camera->last_synced_at?->diffForHumans() ?: 'Belum pernah' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-1">
                                            <form method="POST" action="{{ route('super-admin.cctv.sync', $camera) }}">
                                                @csrf
                                                <button title="Sinkronkan" class="rounded-md p-2 text-slate-500 hover:bg-blue-50 hover:text-blue-600">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M5.6 15A7 7 0 0018 17.4M18.4 9A7 7 0 006 6.6"/></svg>
                                                </button>
                                            </form>
                                            <button type="button" title="Edit" @click="openEdit({{ \Illuminate\Support\Js::from([
                                                'id' => $camera->id,
                                                'name' => $camera->name,
                                                'location' => $camera->location,
                                                'description' => $camera->description,
                                                'sort_order' => $camera->sort_order,
                                                'is_active' => $camera->is_active,
                                                'user_ids' => $camera->users->pluck('id')->values(),
                                                'update_url' => route('super-admin.cctv.update', $camera),
                                            ]) }})" class="rounded-md p-2 text-slate-500 hover:bg-amber-50 hover:text-amber-600">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7m-1.5-9.5a2.1 2.1 0 013 3L12 14l-4 1 1-4 7.5-7.5z"/></svg>
                                            </button>
                                            <form method="POST" action="{{ route('super-admin.cctv.destroy', $camera) }}" onsubmit="return confirm('Hapus kamera dan seluruh hak aksesnya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button title="Hapus" class="rounded-md p-2 text-slate-500 hover:bg-red-50 hover:text-red-600">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-5 py-4">{{ $cameras->links() }}</div>
            @endif
        </section>

        <div x-cloak x-show="modalOpen" class="fixed inset-0 z-[70] flex items-center justify-center p-4" @keydown.escape.window="modalOpen = false">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-sm" @click="modalOpen = false"></div>
            <form method="POST" :action="formAction" class="relative max-h-[92vh] w-full max-w-4xl overflow-y-auto rounded-lg bg-white shadow-2xl">
                @csrf
                <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="_cctv_form_mode" :value="editing ? 'edit' : 'create'">
                <input type="hidden" name="_cctv_update_url" :value="formAction">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900" x-text="editing ? 'Edit Kamera CCTV' : 'Tambah Kamera CCTV'"></h2>
                        <p class="text-xs text-slate-500">Gunakan sub-stream kamera agar tayangan lebih ringan.</p>
                    </div>
                    <button type="button" @click="modalOpen = false" class="rounded-md p-2 text-slate-500 hover:bg-slate-100" aria-label="Tutup">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid gap-6 p-6 lg:grid-cols-2">
                    @if($errors->any())
                        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 lg:col-span-2">
                            <p class="font-bold">Periksa kembali data kamera:</p>
                            <ul class="mt-1 list-inside list-disc">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-bold text-slate-700">Nama Kamera *</label>
                            <input name="name" x-model="form.name" required maxlength="120" class="mt-1 w-full rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500" placeholder="CCTV Gerbang Utama">
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">Lokasi</label>
                            <input name="location" x-model="form.location" maxlength="160" class="mt-1 w-full rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500" placeholder="Gerbang depan">
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">URL RTSP <span x-show="!editing">*</span></label>
                            <div class="relative mt-1">
                                <input name="rtsp_url" :required="!editing" :type="showRtsp ? 'text' : 'password'" class="w-full rounded-md border-slate-300 pr-12 font-mono text-sm focus:border-red-500 focus:ring-red-500" placeholder="rtsp://user:password@192.168.1.10:554/stream">
                                <button type="button" @click="showRtsp = !showRtsp" class="absolute inset-y-0 right-0 px-3 text-xs font-bold text-slate-500" x-text="showRtsp ? 'Tutup' : 'Lihat'"></button>
                            </div>
                            <p class="mt-1 text-xs text-slate-500" x-text="editing ? 'Kosongkan jika URL RTSP tidak berubah.' : 'Gunakan URL sub-stream H.264 bila tersedia.'"></p>
                        </div>
                        <div class="grid grid-cols-[1fr_auto] gap-4">
                            <div>
                                <label class="text-sm font-bold text-slate-700">Urutan</label>
                                <input name="sort_order" x-model="form.sort_order" type="number" min="0" max="9999" class="mt-1 w-full rounded-md border-slate-300">
                            </div>
                            <label class="mt-7 flex items-center gap-2 rounded-md bg-slate-50 px-4">
                                <input name="is_active" value="1" x-model="form.is_active" type="checkbox" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                <span class="text-sm font-bold text-slate-700">Aktif</span>
                            </label>
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">Keterangan</label>
                            <textarea name="description" x-model="form.description" rows="3" maxlength="500" class="mt-1 w-full rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500" placeholder="Area yang dipantau dan catatan penggunaan."></textarea>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-end justify-between gap-3">
                            <div>
                                <label class="text-sm font-bold text-slate-700">Pengguna yang Diberi Akses</label>
                                <p class="text-xs text-slate-500">Semua role non-siswa dapat dipilih.</p>
                            </div>
                            <span class="text-xs font-bold text-red-600" x-text="selectedUsers.length + ' dipilih'"></span>
                        </div>
                        <div class="mt-3 rounded-md border border-slate-200">
                            <div class="border-b border-slate-200 p-3">
                                <input x-model="userSearch" type="search" class="w-full rounded-md border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Cari nama, email, atau role...">
                            </div>
                            <div class="max-h-80 divide-y divide-slate-100 overflow-y-auto">
                                @foreach($users as $user)
                                    @php $searchable = \Illuminate\Support\Str::lower($user->name.' '.$user->email.' '.$user->roles->pluck('name')->join(' ')); @endphp
                                    <label x-show="{{ \Illuminate\Support\Js::from($searchable) }}.includes(userSearch.toLowerCase())" class="flex cursor-pointer items-start gap-3 px-4 py-3 hover:bg-slate-50">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" x-model.number="selectedUsers" class="mt-1 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-bold text-slate-800">{{ $user->name }}</span>
                                            <span class="block truncate text-xs text-slate-500">{{ $user->email }} · {{ $user->roles->pluck('name')->join(', ') ?: 'Tanpa role' }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sticky bottom-0 flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button type="button" @click="modalOpen = false" class="h-10 rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-700 hover:bg-white">Batal</button>
                    <button class="h-10 rounded-md bg-red-600 px-5 text-sm font-bold text-white hover:bg-red-500" x-text="editing ? 'Simpan Perubahan' : 'Tambah Kamera'"></button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function cctvManager() {
                return {
                    modalOpen: {{ $errors->any() ? 'true' : 'false' }},
                    editing: {{ old('_cctv_form_mode') === 'edit' ? 'true' : 'false' }},
                    showRtsp: false,
                    userSearch: '',
                    selectedUsers: @js(collect(old('user_ids', []))->map(fn ($id) => (int) $id)->values()),
                    formAction: @js(old('_cctv_update_url', route('super-admin.cctv.store'))),
                    form: {
                        name: @js(old('name', '')),
                        location: @js(old('location', '')),
                        description: @js(old('description', '')),
                        sort_order: @js(old('sort_order', 0)),
                        is_active: {{ old('is_active', true) ? 'true' : 'false' }},
                    },
                    openCreate() {
                        this.editing = false;
                        this.showRtsp = false;
                        this.userSearch = '';
                        this.selectedUsers = [];
                        this.formAction = @js(route('super-admin.cctv.store'));
                        this.form = { name: '', location: '', description: '', sort_order: 0, is_active: true };
                        this.modalOpen = true;
                    },
                    openEdit(camera) {
                        this.editing = true;
                        this.showRtsp = false;
                        this.userSearch = '';
                        this.selectedUsers = camera.user_ids.map(Number);
                        this.formAction = camera.update_url;
                        this.form = {
                            name: camera.name,
                            location: camera.location || '',
                            description: camera.description || '',
                            sort_order: camera.sort_order || 0,
                            is_active: Boolean(camera.is_active),
                        };
                        this.modalOpen = true;
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
