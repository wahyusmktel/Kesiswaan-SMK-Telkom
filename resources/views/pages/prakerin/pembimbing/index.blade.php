<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Data Pembimbing Industri</h2></x-slot>
    <div class="py-8"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white border border-gray-100 shadow-sm sm:rounded-2xl p-6">
            <form method="POST" action="{{ route('prakerin.pembimbing.store') }}" class="grid gap-4 lg:grid-cols-4">
                @csrf
                <select name="tipe" class="rounded-xl border-gray-200" required>
                    <option value="internal">Pembimbing Internal</option>
                    <option value="external">Pembimbing External</option>
                </select>
                <select name="master_guru_id" class="rounded-xl border-gray-200"><option value="">Guru internal</option>@foreach($guru as $g)<option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>@endforeach</select>
                <select name="prakerin_industri_id" class="rounded-xl border-gray-200"><option value="">Asal industri</option>@foreach($industri as $i)<option value="{{ $i->id }}">{{ $i->nama_industri }}</option>@endforeach</select>
                <input name="nama" class="rounded-xl border-gray-200" placeholder="Nama pembimbing" required>
                <input name="jabatan" class="rounded-xl border-gray-200" placeholder="Jabatan">
                <input name="telepon" class="rounded-xl border-gray-200" placeholder="Telepon">
                <input name="email" type="email" class="rounded-xl border-gray-200" placeholder="Email">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
                <button class="rounded-xl bg-red-600 px-4 py-2 text-white font-semibold">Simpan Pembimbing</button>
            </form>
        </div>
        <div class="bg-white border border-gray-100 shadow-sm sm:rounded-2xl overflow-hidden">
            <div class="p-6 flex justify-between gap-4"><div><h3 class="font-bold text-gray-900">Daftar Pembimbing</h3><p class="text-sm text-gray-500">Internal berasal dari guru, external berasal dari industri.</p></div>
                <form class="flex gap-2"><input name="search" value="{{ request('search') }}" class="rounded-xl border-gray-200" placeholder="Cari nama..."><select name="tipe" class="rounded-xl border-gray-200"><option value="">Semua</option><option value="internal" @selected(request('tipe')==='internal')>Internal</option><option value="external" @selected(request('tipe')==='external')>External</option></select><button class="rounded-xl border px-4">Filter</button></form>
            </div>
            <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-100"><thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-bold text-gray-500">Nama</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-500">Tipe</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-500">Asal</th><th class="px-6 py-3 text-right text-xs font-bold text-gray-500">Aksi</th></tr></thead><tbody class="divide-y divide-gray-100">
                @forelse($pembimbings as $p)<tr><td class="px-6 py-4"><p class="font-semibold">{{ $p->nama }}</p><p class="text-xs text-gray-500">{{ $p->telepon ?? '-' }} {{ $p->email ? ' / '.$p->email : '' }}</p></td><td class="px-6 py-4 capitalize">{{ $p->tipe }}</td><td class="px-6 py-4">{{ $p->tipe === 'internal' ? ($p->guru?->nama_lengkap ?? '-') : ($p->industri?->nama_industri ?? '-') }}</td><td class="px-6 py-4 text-right"><form method="POST" action="{{ route('prakerin.pembimbing.destroy',$p) }}">@csrf @method('DELETE')<button class="text-red-600 text-sm font-semibold">Hapus</button></form></td></tr>@empty<tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada pembimbing.</td></tr>@endforelse
            </tbody></table></div><div class="p-6">{{ $pembimbings->links() }}</div>
        </div>
    </div></div>
</x-app-layout>
