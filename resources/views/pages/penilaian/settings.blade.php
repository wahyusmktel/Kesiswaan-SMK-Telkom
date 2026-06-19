<x-app-layout>
    <x-slot name="header"><h2 class="font-bold text-xl text-gray-800">Seting Periode Penilaian</h2></x-slot>

    <div class="grid xl:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4">Tambah Periode</h3>
            <form method="POST" action="{{ route('penilaian.periods.store') }}" class="space-y-4">
                @csrf
                <input name="title" required placeholder="Contoh: Penilaian Semester Ganjil" class="w-full rounded-lg border-gray-300 text-sm">
                <select name="tahun_pelajaran_id" required class="w-full rounded-lg border-gray-300 text-sm">
                    @foreach($tahunPelajaran as $tp)
                        <option value="{{ $tp->id }}">{{ $tp->tahun }} - {{ $tp->semester }} {{ $tp->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
                <input name="semester" required placeholder="Ganjil/Genap" class="w-full rounded-lg border-gray-300 text-sm">
                <input type="datetime-local" name="start_at" required class="w-full rounded-lg border-gray-300 text-sm">
                <input type="datetime-local" name="end_at" required class="w-full rounded-lg border-gray-300 text-sm">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700"><input type="checkbox" name="is_active" value="1" checked class="rounded text-red-600"> Aktif</label>
                <textarea name="notes" rows="3" placeholder="Catatan" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                <button class="w-full rounded-lg bg-red-600 text-white font-bold py-2">Simpan</button>
            </form>
        </div>

        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Arsip Periode</h3>
                <a href="{{ route('penilaian.instruments') }}" class="text-sm font-bold text-red-600">Seting Instrumen</a>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($periods as $period)
                    <form method="POST" action="{{ route('penilaian.periods.update', $period) }}" class="p-5 grid md:grid-cols-6 gap-3 items-end">
                        @csrf @method('PUT')
                        <input name="title" value="{{ $period->title }}" class="md:col-span-2 rounded-lg border-gray-300 text-sm">
                        <select name="tahun_pelajaran_id" class="rounded-lg border-gray-300 text-sm">
                            @foreach($tahunPelajaran as $tp)
                                <option value="{{ $tp->id }}" @selected($period->tahun_pelajaran_id === $tp->id)>{{ $tp->tahun }}</option>
                            @endforeach
                        </select>
                        <input name="semester" value="{{ $period->semester }}" class="rounded-lg border-gray-300 text-sm">
                        <input type="datetime-local" name="start_at" value="{{ $period->start_at->format('Y-m-d\TH:i') }}" class="rounded-lg border-gray-300 text-sm">
                        <input type="datetime-local" name="end_at" value="{{ $period->end_at->format('Y-m-d\TH:i') }}" class="rounded-lg border-gray-300 text-sm">
                        <div class="md:col-span-6 flex flex-wrap items-center gap-3">
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($period->is_active) class="rounded text-red-600"> Aktif</label>
                            <button class="px-3 py-2 rounded-lg bg-gray-900 text-white text-xs font-bold">Update</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('penilaian.periods.notify', $period) }}" class="px-5 -mt-3 pb-5">
                        @csrf
                        <button class="px-3 py-2 rounded-lg bg-red-600 text-white text-xs font-bold">Kirim Notifikasi Penilaian</button>
                    </form>
                @endforeach
            </div>
            <div class="p-4">{{ $periods->links() }}</div>
        </div>
    </div>
</x-app-layout>
