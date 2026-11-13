<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Tambah Agenda OSIS</h2>
                <p class="text-xs text-gray-500 mt-1">Buat jadwal kegiatan atau event baru untuk siswa</p>
            </div>
            <a href="{{ route('osis.agenda.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm">
                <form action="{{ route('osis.agenda.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Judul Agenda</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Latihan Dasar Kepemimpinan Siswa (LDKS)" required
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Selesai (Opsional)</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kategori Event</label>
                            <select name="kategori" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                                <option value="Program Kerja">Program Kerja</option>
                                <option value="Demokrasi Sekolah">Demokrasi Sekolah</option>
                                <option value="Pengembangan Karakter">Pengembangan Karakter</option>
                                <option value="Event Tahunan">Event Tahunan</option>
                                <option value="Lomba & Kompetisi">Lomba &amp; Kompetisi</option>
                                <option value="Bakti Sosial">Bakti Sosial</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status</label>
                            <select name="status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                                <option value="akan_datang">Akan Datang</option>
                                <option value="berlangsung">Sedang Berlangsung</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Lokasi / Tempat</label>
                            <input type="text" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Aula Graha Telkom / Lapangan Utama"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Deskripsi Agenda</label>
                            <textarea name="deskripsi" rows="3"
                                      class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-800">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Foto / Poster Event (Opsional)</label>
                            <input type="file" name="foto" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_published" id="is_published" value="1" checked class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500">
                        <label for="is_published" class="text-xs font-bold text-gray-700">Publikasikan di Web Publik OSIS</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('osis.agenda.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-xs font-bold">Batal</a>
                        <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-500/20">
                            Simpan Agenda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
