<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Edit Agenda OSIS</h2>
                <p class="text-xs text-gray-500 mt-1">Perbarui jadwal, status, atau rincian kegiatan</p>
            </div>
            <a href="{{ route('osis.agenda.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm">
                <form action="{{ route('osis.agenda.update', $agenda) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Judul Agenda</label>
                        <input type="text" name="judul" value="{{ old('judul', $agenda->judul) }}" required
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $agenda->tanggal_mulai->format('Y-m-d')) }}" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Selesai (Opsional)</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $agenda->tanggal_selesai ? $agenda->tanggal_selesai->format('Y-m-d') : '') }}"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kategori Event</label>
                            <select name="kategori" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                                @foreach(['Program Kerja', 'Demokrasi Sekolah', 'Pengembangan Karakter', 'Event Tahunan', 'Lomba & Kompetisi', 'Bakti Sosial'] as $kat)
                                    <option value="{{ $kat }}" {{ $agenda->kategori == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status</label>
                            <select name="status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                                <option value="akan_datang" {{ $agenda->status == 'akan_datang' ? 'selected' : '' }}>Akan Datang</option>
                                <option value="berlangsung" {{ $agenda->status == 'berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                                <option value="selesai" {{ $agenda->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Lokasi / Tempat</label>
                            <input type="text" name="lokasi" value="{{ old('lokasi', $agenda->lokasi) }}"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Deskripsi Agenda</label>
                            <textarea name="deskripsi" rows="3"
                                      class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-800">{{ old('deskripsi', $agenda->deskripsi) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Ganti Foto / Poster Event</label>
                            <input type="file" name="foto" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                            @if($agenda->foto)
                                <div class="mt-2 flex items-center gap-2">
                                    <img src="{{ $agenda->foto_url }}" class="w-12 h-12 rounded-lg object-cover border">
                                    <span class="text-[11px] text-gray-500">Foto tersimpan</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_published" id="is_published" value="1" {{ $agenda->is_published ? 'checked' : '' }} class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500">
                        <label for="is_published" class="text-xs font-bold text-gray-700">Publikasikan di Web Publik OSIS</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('osis.agenda.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-xs font-bold">Batal</a>
                        <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-500/20">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
