<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Rombel PKL & Mapping Siswa</h2></x-slot>
    <div class="py-8"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white border border-gray-100 shadow-sm sm:rounded-2xl p-6">
            <form method="POST" action="{{ route('prakerin.rombel-pkl.store') }}" class="grid gap-4 lg:grid-cols-3">@csrf
                <input name="nama_rombel" class="rounded-xl border-gray-200" placeholder="Nama rombel PKL" required>
                <select name="prakerin_industri_id" class="rounded-xl border-gray-200" required><option value="">Pilih industri</option>@foreach($industri as $i)<option value="{{ $i->id }}">{{ $i->nama_industri }}</option>@endforeach</select>
                <select name="pembimbing_internal_id" class="rounded-xl border-gray-200"><option value="">Pembimbing internal</option>@foreach($internal as $p)<option value="{{ $p->id }}">{{ $p->nama }}</option>@endforeach</select>
                <select name="pembimbing_external_id" class="rounded-xl border-gray-200"><option value="">Pembimbing external</option>@foreach($external as $p)<option value="{{ $p->id }}">{{ $p->nama }} - {{ $p->industri?->nama_industri }}</option>@endforeach</select>
                <input type="date" name="tanggal_mulai" class="rounded-xl border-gray-200"><input type="date" name="tanggal_selesai" class="rounded-xl border-gray-200">
                <select name="status" class="rounded-xl border-gray-200"><option value="draft">Draft</option><option value="aktif">Aktif</option><option value="selesai">Selesai</option></select>
                <button class="rounded-xl bg-red-600 px-4 py-2 text-white font-semibold">Buat Rombel PKL</button>
            </form>
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($rombels as $r)<div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                <div class="flex justify-between gap-3"><div><h3 class="font-bold text-gray-900">{{ $r->nama_rombel }}</h3><p class="text-sm text-gray-500">{{ $r->industri?->nama_industri }}</p></div><span class="text-xs font-bold rounded-full bg-gray-100 px-3 py-1 h-fit">{{ $r->status }}</span></div>
                <div class="mt-4 text-sm text-gray-600 space-y-1"><p>Internal: {{ $r->pembimbingInternal?->nama ?? '-' }}</p><p>External: {{ $r->pembimbingExternal?->nama ?? '-' }}</p><p>Anggota: {{ $r->penempatans_count }} siswa</p></div>
                <div class="mt-5 flex gap-2"><a href="{{ route('prakerin.rombel-pkl.mapping',$r) }}" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white">Mapping Siswa</a><form method="POST" action="{{ route('prakerin.rombel-pkl.destroy',$r) }}">@csrf @method('DELETE')<button class="rounded-xl border px-4 py-2 text-sm text-red-600">Hapus</button></form></div>
            </div>@empty<div class="bg-white rounded-2xl p-8 text-gray-500">Belum ada rombel PKL.</div>@endforelse
        </div>
        {{ $rombels->links() }}
    </div></div>
</x-app-layout>
