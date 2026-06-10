<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Mapping Siswa - {{ $rombel->nama_rombel }}</h2></x-slot>
    <div class="py-8"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6 lg:grid-cols-[1fr_1.2fr]">
        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6"><h3 class="font-bold mb-4">Anggota Rombel PKL</h3>@forelse($rombel->penempatans as $p)<div class="flex justify-between border-b py-3"><div><p class="font-semibold">{{ $p->siswa?->nama_lengkap }}</p><p class="text-xs text-gray-500">{{ $p->siswa?->nis }}</p></div><form method="POST" action="{{ route('prakerin.rombel-pkl.mapping.destroy',[$rombel,$p]) }}">@csrf @method('DELETE')<button class="text-red-600 text-sm">Lepas</button></form></div>@empty<p class="text-gray-500">Belum ada anggota.</p>@endforelse</div>
        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6">
            <form class="flex flex-col sm:flex-row gap-2 mb-4"><select name="kelas_id" class="rounded-xl border-gray-200"><option value="">Semua kelas</option>@foreach($kelas as $k)<option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama_kelas }}</option>@endforeach</select><input name="search" value="{{ request('search') }}" class="rounded-xl border-gray-200 flex-1" placeholder="Cari nama/NIS"><button class="rounded-xl border px-4">Filter</button></form>
            <form method="POST" action="{{ route('prakerin.rombel-pkl.mapping.store',$rombel) }}">@csrf
                <div class="space-y-2 max-h-[520px] overflow-y-auto">@forelse($siswa as $s)<label class="flex items-center gap-3 rounded-xl border border-gray-100 p-3 hover:bg-gray-50"><input type="checkbox" name="master_siswa_ids[]" value="{{ $s->id }}"><div><p class="font-semibold">{{ $s->nama_lengkap }}</p><p class="text-xs text-gray-500">{{ $s->nis }} - {{ $s->rombels->first()?->kelas?->nama_kelas ?? '-' }}</p></div></label>@empty<p class="text-gray-500 p-6 text-center">Tidak ada siswa tersedia. Siswa yang sudah dimapping tidak muncul di list.</p>@endforelse</div>
                <button class="mt-4 rounded-xl bg-red-600 px-5 py-2 text-white font-semibold">Tambahkan ke Rombel PKL</button>
            </form><div class="mt-4">{{ $siswa->links() }}</div>
        </div>
    </div></div>
</x-app-layout>
