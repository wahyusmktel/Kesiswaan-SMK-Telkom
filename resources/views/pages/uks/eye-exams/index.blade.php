<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Tes Buta Warna & Kesehatan Mata</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pemeriksaan Ishihara sederhana, ketajaman penglihatan, resume, dan rekomendasi tindak lanjut.</p>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ createOpen: false, examineeType: 'siswa' }">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Total Pemeriksaan</p>
                <p class="mt-3 text-3xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-emerald-600">Normal</p>
                <p class="mt-3 text-3xl font-black text-emerald-700">{{ number_format($stats['normal']) }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-amber-600">Indikasi Buta Warna</p>
                <p class="mt-3 text-3xl font-black text-amber-700">{{ number_format($stats['color_alert']) }}</p>
            </div>
            <div class="rounded-2xl border border-red-100 bg-red-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-red-600">Perlu Rujukan</p>
                <p class="mt-3 text-3xl font-black text-red-700">{{ number_format($stats['referrals']) }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h3 class="font-black text-gray-900">Riwayat Pemeriksaan Mata</h3>
                    <p class="text-sm text-gray-500">Kelola hasil tes siswa dan pegawai dalam satu halaman.</p>
                </div>
                <button @click="createOpen = true" class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white hover:bg-red-700">Tambah Pemeriksaan</button>
            </div>

            <form method="GET" action="{{ route('uks.eye-exams.index') }}" class="mt-5 grid grid-cols-1 md:grid-cols-7 gap-3">
                <input name="search" value="{{ request('search') }}" class="md:col-span-2 rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Cari nama, NIS, email, catatan">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                <select name="type" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Semua peserta</option>
                    <option value="siswa" @selected(request('type') === 'siswa')>Siswa</option>
                    <option value="pegawai" @selected(request('type') === 'pegawai')>Pegawai</option>
                </select>
                <select name="result" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Semua hasil</option>
                    <option value="normal" @selected(request('result') === 'normal')>Normal</option>
                    <option value="partial" @selected(request('result') === 'partial')>Parsial</option>
                    <option value="total" @selected(request('result') === 'total')>Total</option>
                    <option value="inconclusive" @selected(request('result') === 'inconclusive')>Ulang</option>
                </select>
                <div class="flex gap-2">
                    <button class="flex-1 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">Filter</button>
                    <a href="{{ route('uks.eye-exams.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50">Reset</a>
                </div>
            </form>

            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Peserta</th>
                            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Buta Warna</th>
                            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Visus</th>
                            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Kesimpulan</th>
                            <th class="px-4 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($exams as $exam)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <div class="font-bold text-gray-900">{{ $exam->examinee_name }}</div>
                                    <div class="text-xs text-gray-400">{{ ucfirst($exam->examinee_type) }} - {{ $exam->examinee_identity }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-600">{{ $exam->examined_at->translatedFormat('d M Y H:i') }}</td>
                                <td class="px-4 py-4">
                                    <span class="rounded-full px-3 py-1.5 text-xs font-black {{ $exam->color_blind_result === 'normal' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $exam->color_blind_label }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm font-bold text-gray-700">Kanan {{ $exam->visual_acuity_right ?: '-' }} / Kiri {{ $exam->visual_acuity_left ?: '-' }}</td>
                                <td class="px-4 py-4 text-sm font-bold text-gray-700">{{ $exam->conclusion_label }}</td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('uks.eye-exams.resume', $exam) }}" target="_blank" class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-black text-gray-700 hover:bg-gray-50">PDF</a>
                                        <a href="{{ route('uks.eye-exams.show', $exam) }}" class="rounded-xl bg-gray-900 px-3 py-2 text-xs font-black text-white hover:bg-red-600">Detail</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">Belum ada hasil pemeriksaan mata.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($exams->hasPages())
                <div class="mt-5">{{ $exams->links() }}</div>
            @endif
        </div>

        <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 p-4 backdrop-blur-sm">
            <div class="flex min-h-full items-center justify-center">
                <form method="POST" action="{{ route('uks.eye-exams.store') }}" class="w-full max-w-5xl rounded-3xl bg-white shadow-2xl overflow-hidden">
                    @csrf
                    <div class="border-b border-gray-100 p-6 flex justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Tambah Tes Kesehatan Mata</h3>
                            <p class="mt-1 text-sm text-gray-500">Catat hasil tes buta warna dan pemeriksaan mata dasar.</p>
                        </div>
                        <button type="button" @click="createOpen = false" class="h-10 w-10 rounded-xl border border-gray-200 text-gray-600">X</button>
                    </div>
                    <div class="max-h-[72vh] overflow-y-auto p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <label class="block">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Jenis Peserta</span>
                                <select name="examinee_type" x-model="examineeType" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="siswa">Siswa</option>
                                    <option value="pegawai">Pegawai</option>
                                </select>
                            </label>
                            <label x-show="examineeType === 'siswa'" class="block lg:col-span-2">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Pilih Siswa</span>
                                <select name="master_siswa_id" :disabled="examineeType !== 'siswa'" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="">Pilih siswa</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->nama_lengkap }} - {{ $student->nis }} - {{ $student->rombels->first()?->kelas?->nama_kelas ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label x-show="examineeType === 'pegawai'" x-cloak class="block lg:col-span-2">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Pilih Pegawai</span>
                                <select name="user_id" :disabled="examineeType !== 'pegawai'" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="">Pilih pegawai</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->email }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="block">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Waktu Pemeriksaan</span>
                                <input type="datetime-local" name="examined_at" required value="{{ now()->format('Y-m-d\\TH:i') }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            </label>
                            <label class="block">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Hasil Tes Buta Warna</span>
                                <select name="color_blind_result" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="normal">Normal</option>
                                    <option value="partial">Indikasi parsial</option>
                                    <option value="total">Indikasi total</option>
                                    <option value="inconclusive">Perlu tes ulang</option>
                                </select>
                            </label>
                            <label class="block">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Kesimpulan</span>
                                <select name="conclusion" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="baik">Kesehatan mata baik</option>
                                    <option value="perlu_observasi">Perlu observasi</option>
                                    <option value="perlu_rujukan">Perlu rujukan</option>
                                </select>
                            </label>
                            <label class="block">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Visus Mata Kanan</span>
                                <input name="visual_acuity_right" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: 6/6 atau 20/20">
                            </label>
                            <label class="block">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Visus Mata Kiri</span>
                                <input name="visual_acuity_left" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: 6/9">
                            </label>
                            <label class="block lg:col-span-3">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Tes Buta Warna</span>
                                <textarea name="color_blind_notes" rows="2" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Respons baik pada plate mayoritas, kesulitan pada spektrum merah-hijau."></textarea>
                            </label>
                            <label class="block lg:col-span-3">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Temuan Kesehatan Mata</span>
                                <textarea name="eye_health_findings" rows="3" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Keluhan mata, mata merah, nyeri, sering berkedip, penggunaan kacamata, dan temuan lain."></textarea>
                            </label>
                            <label class="block lg:col-span-2">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Rekomendasi</span>
                                <textarea name="recommendation" rows="2" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Edukasi istirahat mata, gunakan kacamata, rujuk ke optik/dokter mata."></textarea>
                            </label>
                            <label class="block">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Internal</span>
                                <textarea name="notes" rows="2" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"></textarea>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                        <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-black text-white hover:bg-red-700">Simpan Pemeriksaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
