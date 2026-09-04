<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2></x-slot>
    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">{{ $note }}</div>
        <form method="GET" class="flex flex-wrap items-end gap-4 rounded-2xl border bg-white p-5">
            <label class="flex-1"><span class="mb-1 block text-sm font-semibold">Nama siswa / guru</span><input name="q" value="{{ request('q') }}" maxlength="100" class="w-full rounded-lg border-gray-300" placeholder="Cari nama..."></label>
            @if($section !== 'jadwal')
                <label><span class="mb-1 block text-sm font-semibold">Dari tanggal</span><input type="date" name="from" value="{{ $from }}" class="rounded-lg border-gray-300"></label>
                <label><span class="mb-1 block text-sm font-semibold">Sampai tanggal</span><input type="date" name="to" value="{{ $to }}" class="rounded-lg border-gray-300"></label>
            @endif
            @if($section === 'izin-siswa')
                <label><span class="mb-1 block text-sm font-semibold">Jenis data</span><select name="jenis_data" class="rounded-lg border-gray-300"><option value="tidak-masuk">Izin Tidak Masuk</option><option value="keluar" @selected(request('jenis_data') === 'keluar')>Izin Keluar Kelas / Sekolah</option></select></label>
            @endif
            <button class="rounded-lg bg-red-600 px-5 py-2.5 font-semibold text-white">Tampilkan</button>
            <a href="{{ route('kepala-sekolah.monitoring.index', $section) }}" class="px-3 py-2 text-sm text-gray-600">Reset</a>
        </form>
        @if($errors->any())<div class="text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <div class="overflow-hidden rounded-2xl border bg-white shadow-sm">
            <div class="border-b px-5 py-4 text-sm font-semibold">{{ number_format($records->total()) }} data sesuai filter · Baca-saja</div>
            <div class="overflow-x-auto"><table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600"><tr>@foreach($headers as $header)<th class="px-5 py-3">{{ $header }}</th>@endforeach</tr></thead>
                <tbody class="divide-y">
                    @forelse($records as $cells)
                        <tr>@foreach($cells as $cell)<td class="px-5 py-4 align-top text-gray-700">{{ $cell ?? '-' }}</td>@endforeach</tr>
                    @empty
                        <tr><td colspan="{{ count($headers) }}" class="px-5 py-12 text-center text-gray-500">Tidak ada data sesuai filter.</td></tr>
                    @endforelse
                </tbody>
            </table></div>
            <div class="border-t p-5">{{ $records->links() }}</div>
        </div>
    </div>
</x-app-layout>
