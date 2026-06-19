<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">✏️ Edit Rencana Pembelajaran</h2>
    </x-slot>

    <div class="py-6 w-full max-w-5xl mx-auto">
        <div class="px-4 sm:px-6 lg:px-8">
            <form id="rpp-form" action="{{ route('guru-kelas.lesson-plan.update', $plan->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- INFO DASAR --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                            <select name="class_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ $plan->class_id == $c->id ? 'selected' : '' }}>{{ $c->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
                            <select name="subject_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}" {{ $plan->subject_id == $s->id ? 'selected' : '' }}>{{ $s->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mengajar</label>
                            <input type="date" name="teach_date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ $plan->teach_date->toDateString() }}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Topik / Judul Materi</label>
                            <input type="text" name="topic" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ $plan->topic }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Durasi (menit)</label>
                            <input type="number" name="duration_minutes" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ $plan->duration_minutes }}" min="15" max="480">
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- TAHAP 1 --}}
                    <div class="bg-white rounded-2xl border border-blue-200 shadow-sm overflow-hidden">
                        <div class="bg-blue-600 px-6 py-4">
                            <h5 class="text-white font-bold m-0">Tahap 1 — Tujuan Pembelajaran</h5>
                        </div>
                        <div class="p-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Setelah pelajaran ini, siswa mampu...</label>
                            <textarea name="learning_objectives" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" rows="4" required>{{ $plan->learning_objectives }}</textarea>
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
                                    ['PBL', 'Problem-Based Learning'],
                                    ['PjBL', 'Project-Based Learning'],
                                    ['Collaborative', 'Collaborative Learning'],
                                    ['Inquiry', 'Inquiry-Based Learning'],
                                    ['Flipped Classroom', 'Flipped Classroom'],
                                    ['Direct', 'Direct Instruction'],
                                ] as [$val, $label])
                                <label class="flex items-center gap-2 p-3 border rounded-xl hover:bg-blue-50 cursor-pointer">
                                    <input type="checkbox" name="methods[]" value="{{ $val }}" class="rounded border-gray-300 text-blue-600" {{ in_array($val, $plan->methods ?? []) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-gray-800">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>

                            <label class="block text-sm font-semibold text-gray-700 mb-3">Urutan Aktivitas Kelas</label>
                            <div class="space-y-4">
                                @foreach([
                                    ['pembuka', '🌅 Pembuka (5-10 mnt)'],
                                    ['eksplorasi', '🔍 Eksplorasi (20-30 mnt)'],
                                    ['elaborasi', '✍️ Elaborasi (30-40 mnt)'],
                                    ['konfirmasi', '✅ Konfirmasi (10-15 mnt)'],
                                    ['penutup', '🌙 Penutup (5-10 mnt)'],
                                ] as [$key, $label])
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $label }}</label>
                                    <textarea name="activities[{{ $key }}]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" rows="2">{{ $plan->activities[$key] ?? '' }}</textarea>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex justify-end gap-4 mt-8 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                        <a href="{{ route('guru-kelas.lesson-plan.show', $plan->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Batal
                        </a>
                        <button type="submit" name="action" value="draft" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Simpan Draft
                        </button>
                        <button type="submit" name="action" value="publish" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Update & Siap Mengajar
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
