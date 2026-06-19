<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">📝 Buat Rencana Pembelajaran</h2>
        <p class="text-sm text-gray-500 mt-1">Ikuti 7 tahap alur perencanaan modern</p>
    </x-slot>

    <div class="py-6 w-full max-w-5xl mx-auto">
        <div class="px-4 sm:px-6 lg:px-8">
            <form id="rpp-form" action="{{ route('guru-kelas.lesson-plan.store') }}" method="POST">
                @csrf

                {{-- INFO DASAR --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                            <select name="class_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
                            <select name="subject_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mengajar</label>
                            <input type="date" name="teach_date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ request('date', today()->toDateString()) }}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Topik / Judul Materi</label>
                            <input type="text" name="topic" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="cth: Pemrograman Berorientasi Objek — Inheritance" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Durasi (menit)</label>
                            <input type="number" name="duration_minutes" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="90" min="15" max="480">
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- TAHAP 1 --}}
                    <div class="bg-white rounded-2xl border border-blue-200 shadow-sm overflow-hidden">
                        <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                            <h5 class="text-white font-bold m-0">Tahap 1 — Tujuan Pembelajaran</h5>
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Apa yang akan bisa siswa LAKUKAN?</span>
                        </div>
                        <div class="p-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Setelah pelajaran ini, siswa mampu...</label>
                            <textarea name="learning_objectives" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" rows="4" placeholder="cth:&#10;1. Menjelaskan konsep inheritance dengan kata-kata sendiri&#10;2. Mengimplementasikan kelas turunan dalam bahasa Python" required></textarea>
                            <p class="mt-2 text-xs text-gray-500">Saran kata kerja: menganalisis · membuat · mengevaluasi · mendemonstrasikan · merancang · memecahkan</p>
                        </div>
                    </div>

                    {{-- TAHAP 2 --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <h5 class="font-bold text-gray-800 m-0">Tahap 2 — Asesmen Awal</h5>
                        </div>
                        <div class="p-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Bagaimana Anda akan mengecek pengetahuan awal siswa?</label>
                            <textarea name="pre_assessment" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="cth: 3 pertanyaan kuis di awal, atau tanya langsung tentang materi sebelumnya"></textarea>
                        </div>
                    </div>

                    {{-- TAHAP 3 --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <h5 class="font-bold text-gray-800 m-0">Tahap 3 — Metode & Aktivitas Pembelajaran</h5>
                        </div>
                        <div class="p-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Metode Pembelajaran</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                @foreach([
                                    ['PBL', 'Problem-Based Learning', 'Siswa memecahkan masalah', '🔍'],
                                    ['PjBL', 'Project-Based Learning', 'Siswa menghasilkan produk', '🏗️'],
                                    ['Collaborative', 'Collaborative Learning', 'Belajar berkelompok', '🤝'],
                                    ['Inquiry', 'Inquiry-Based Learning', 'Siswa bertanya & mencari', '🔬'],
                                    ['Flipped Classroom', 'Flipped Classroom', 'Materi di rumah, praktik kelas', '🔄'],
                                    ['Direct', 'Direct Instruction', 'Ceramah singkat ≤15 menit', '🎤'],
                                ] as [$val, $label, $desc, $icon])
                                <label class="flex items-start gap-3 p-4 border rounded-xl hover:bg-blue-50 cursor-pointer transition">
                                    <input type="checkbox" name="methods[]" value="{{ $val }}" class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $icon }} {{ $label }}</div>
                                        <div class="text-xs text-gray-500">{{ $desc }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>

                            <label class="block text-sm font-semibold text-gray-700 mb-3">Urutan Aktivitas Kelas</label>
                            <div class="space-y-4">
                                @foreach([
                                    ['pembuka', '🌅 Pembuka (5-10 mnt)', 'Apersepsi, motivasi, tujuan belajar'],
                                    ['eksplorasi', '🔍 Eksplorasi (20-30 mnt)', 'Siswa menjelajahi materi/masalah'],
                                    ['elaborasi', '✍️ Elaborasi (30-40 mnt)', 'Siswa mengerjakan tugas/proyek'],
                                    ['konfirmasi', '✅ Konfirmasi (10-15 mnt)', 'Pembahasan, klarifikasi, feedback'],
                                    ['penutup', '🌙 Penutup (5-10 mnt)', 'Rangkuman, exit ticket, PR'],
                                ] as [$key, $label, $hint])
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $label }}</label>
                                    <textarea name="activities[{{ $key }}]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" rows="2" placeholder="{{ $hint }}"></textarea>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- TAHAP 4 & 5 --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                                <h5 class="font-bold text-gray-800 m-0">Tahap 4 — Media</h5>
                            </div>
                            <div class="p-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Link / Keterangan Modul</label>
                                <input type="text" name="resources[link]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="https:// atau nama file">
                                
                                <div class="mt-4 space-y-2">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="resources[needs][]" value="Proyektor" class="rounded border-gray-300 text-blue-600"> <span class="text-sm">Proyektor</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="resources[needs][]" value="Internet" class="rounded border-gray-300 text-blue-600"> <span class="text-sm">Internet</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="resources[needs][]" value="Lab" class="rounded border-gray-300 text-blue-600"> <span class="text-sm">Lab Komputer</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                                <h5 class="font-bold text-gray-800 m-0">Tahap 5 — Asesmen Akhir</h5>
                            </div>
                            <div class="p-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bagaimana guru tahu siswa paham?</label>
                                <textarea name="final_assessment" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" rows="4" placeholder="cth: Unjuk kerja, kuis, presentasi kelompok"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex justify-end gap-4 mt-8 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                        <a href="{{ route('guru-kelas.lesson-plan.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Batal
                        </a>
                        <button type="submit" name="action" value="draft" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Simpan Draft
                        </button>
                        <button type="submit" name="action" value="publish" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Simpan & Siap Mengajar
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
