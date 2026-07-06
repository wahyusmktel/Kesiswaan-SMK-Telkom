<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-black text-slate-900">Mapel Transkrip</h2>
            <p class="text-sm font-medium text-slate-500">Atur mata pelajaran, urutan, dan kelompok yang akan muncul di dokumen transkrip nilai.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" x-data="{ createOpen:false, editSubject:null }">
            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
            @endif

            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="mb-2 inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-black uppercase tracking-widest text-red-700">Transkrip Nilai</div>
                        <h3 class="text-2xl font-black text-slate-900">Manajemen Mata Pelajaran</h3>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <form class="flex gap-2">
                            <input name="search" value="{{ request('search') }}" class="rounded-2xl border-slate-200 text-sm" placeholder="Cari mapel...">
                            <select name="group" class="rounded-2xl border-slate-200 text-sm">
                                <option value="">Semua kelompok</option>
                                @foreach($groups as $key => $label)<option value="{{ $key }}" @selected(request('group')===$key)>{{ $label }}</option>@endforeach
                            </select>
                            <button class="rounded-2xl border border-slate-200 px-4 text-sm font-bold">Filter</button>
                        </form>
                        <button @click="createOpen=true" class="rounded-2xl bg-red-600 px-5 py-2 text-sm font-black text-white shadow-lg shadow-red-100">+ Tambah Mapel</button>
                    </div>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr><th class="px-6 py-4 text-left text-xs font-black uppercase text-slate-500">Urut</th><th class="px-6 py-4 text-left text-xs font-black uppercase text-slate-500">Mata Pelajaran</th><th class="px-6 py-4 text-left text-xs font-black uppercase text-slate-500">Kelompok</th><th class="px-6 py-4 text-left text-xs font-black uppercase text-slate-500">Status</th><th class="px-6 py-4 text-right text-xs font-black uppercase text-slate-500">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($subjects as $subject)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-6 py-4 text-sm font-black text-slate-700">{{ $subject->sort_order }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-900">{{ $subject->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $groups[$subject->group] ?? $subject->group }}</td>
                                <td class="px-6 py-4"><span class="rounded-full px-3 py-1 text-xs font-black {{ $subject->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $subject->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="px-6 py-4 text-right">
                                    <button @click='editSubject=@json($subject)' class="text-sm font-bold text-blue-600">Edit</button>
                                    <form method="POST" action="{{ route('operator.transcript.mapel.destroy', $subject) }}" class="inline">@csrf @method('DELETE')<button class="ml-3 text-sm font-bold text-red-600">Hapus</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada mapel transkrip.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4">{{ $subjects->links() }}</div>
            </div>

            <div x-show="createOpen || editSubject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
                <div class="w-full max-w-xl rounded-[28px] bg-white p-6 shadow-2xl">
                    <h3 class="text-lg font-black text-slate-900" x-text="editSubject ? 'Edit Mapel Transkrip' : 'Tambah Mapel Transkrip'"></h3>
                    <form method="POST" :action="editSubject ? '{{ url('operator/transkrip/mapel') }}/' + editSubject.id : '{{ route('operator.transcript.mapel.store') }}'" class="mt-5 grid gap-4">
                        @csrf
                        <template x-if="editSubject"><input type="hidden" name="_method" value="PUT"></template>
                        <input name="name" :value="editSubject?.name || ''" class="rounded-2xl border-slate-200" placeholder="Nama mata pelajaran" required>
                        <input name="sort_order" type="number" min="1" :value="editSubject?.sort_order || 1" class="rounded-2xl border-slate-200" placeholder="Nomor urut" required>
                        <select name="group" class="rounded-2xl border-slate-200" required>
                            @foreach($groups as $key => $label)<option value="{{ $key }}" x-bind:selected="editSubject?.group === '{{ $key }}'">{{ $label }}</option>@endforeach
                        </select>
                        <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="is_active" value="1" :checked="!editSubject || editSubject.is_active"> Aktif</label>
                        <div class="flex justify-end gap-2"><button type="button" @click="createOpen=false;editSubject=null" class="rounded-2xl border px-5 py-2 font-bold">Batal</button><button class="rounded-2xl bg-red-600 px-5 py-2 font-black text-white">Simpan</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
