<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-black text-slate-900">Nilai Transkrip</h2>
            <p class="text-sm text-slate-500">Import dan pantau nilai transkrip berdasarkan rombel tingkat akhir.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" x-data="{ importOpen:false, reportOpen: {{ session('import_report') ? 'true' : 'false' }} }">
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <div class="mb-2 inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-black uppercase tracking-widest text-red-700">Import Excel</div>
                        <h3 class="text-2xl font-black text-slate-900">Kelola Nilai Transkrip</h3>
                        <p class="mt-1 max-w-2xl text-sm text-slate-500">Pilih kelas/rombel, unduh format yang sudah berisi siswa dan list mapel aktif, lalu import kembali nilai dengan format desimal.</p>
                    </div>
                    <div class="flex flex-col gap-2 md:flex-row">
                        <form class="flex flex-col gap-2 md:flex-row">
                            <select name="rombel_id" class="min-w-[260px] rounded-2xl border-slate-200 text-sm" required>
                                <option value="">Pilih Rombel/Kelas</option>
                                @foreach($rombels as $rombel)
                                    <option value="{{ $rombel->id }}" @selected(request('rombel_id') == $rombel->id)>{{ $rombel->kelas?->nama_kelas ?? 'Rombel' }} - {{ $rombel->tahun_ajaran }}</option>
                                @endforeach
                            </select>
                            <button class="rounded-2xl border border-slate-200 px-5 py-2 text-sm font-black">Tampilkan</button>
                        </form>
                        @if($selectedRombel)
                            <a href="{{ route('operator.transcript.grades.template', ['rombel_id' => $selectedRombel->id]) }}" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-2 text-center text-sm font-black text-emerald-700">Download Format</a>
                            <button @click="importOpen=true" class="rounded-2xl bg-red-600 px-5 py-2 text-sm font-black text-white">Import Nilai</button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-black uppercase text-slate-500">Rombel Dipilih</p><p class="mt-2 text-xl font-black text-slate-900">{{ $selectedRombel?->kelas?->nama_kelas ?? '-' }}</p></div>
                <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-black uppercase text-slate-500">Jumlah Siswa</p><p class="mt-2 text-xl font-black text-slate-900">{{ $students->count() }}</p></div>
                <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-black uppercase text-slate-500">Mapel Aktif</p><p class="mt-2 text-xl font-black text-slate-900">{{ $subjects->count() }}</p></div>
            </div>

            <div class="mt-6 overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="font-black text-slate-900">Preview Nilai Tersimpan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="sticky left-0 z-10 bg-slate-50 px-5 py-4 text-left text-xs font-black uppercase text-slate-500">No</th>
                                <th class="sticky left-14 z-10 min-w-[260px] bg-slate-50 px-5 py-4 text-left text-xs font-black uppercase text-slate-500">Siswa</th>
                                <th class="px-5 py-4 text-left text-xs font-black uppercase text-slate-500">NISN</th>
                                @foreach($subjects as $subject)
                                    <th class="min-w-[130px] px-5 py-4 text-left text-xs font-black uppercase text-slate-500">{{ $subject->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $student)
                                @php($gradeMap = $student->transcriptGrades->keyBy('transcript_subject_id'))
                                <tr>
                                    <td class="sticky left-0 z-10 bg-white px-5 py-4 text-sm">{{ $loop->iteration }}</td>
                                    <td class="sticky left-14 z-10 bg-white px-5 py-4"><p class="font-bold text-slate-900">{{ $student->nama_lengkap }}</p><p class="text-xs text-slate-500">{{ $student->nis }}</p></td>
                                    <td class="px-5 py-4 text-sm text-slate-600">{{ $student->dapodik?->nisn ?? '-' }}</td>
                                    @foreach($subjects as $subject)
                                        <td class="px-5 py-4 text-sm font-bold text-slate-700">{{ $gradeMap->get($subject->id)?->score !== null ? number_format((float) $gradeMap->get($subject->id)->score, 2, '.', '') : '-' }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr><td colspan="{{ 3 + $subjects->count() }}" class="px-6 py-14 text-center text-sm font-semibold text-slate-500">Pilih rombel terlebih dahulu untuk melihat data siswa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($selectedRombel)
                <div x-show="importOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
                    <div class="w-full max-w-lg rounded-[28px] bg-white p-6 shadow-2xl">
                        <h3 class="text-lg font-black text-slate-900">Import Nilai Transkrip</h3>
                        <p class="mt-1 text-sm text-slate-500">Rombel: {{ $selectedRombel->kelas?->nama_kelas ?? '-' }}. Nilai lama akan diperbarui jika sudah tersedia.</p>
                        <form method="POST" action="{{ route('operator.transcript.grades.import') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                            @csrf
                            <input type="hidden" name="rombel_id" value="{{ $selectedRombel->id }}">
                            <input type="file" name="file_import" accept=".xlsx,.xls,.csv" class="w-full rounded-2xl border border-slate-200 p-3" required>
                            <div class="rounded-2xl bg-slate-50 p-4 text-xs font-semibold text-slate-600">Contoh nilai: 69.00, 80.87, 80.00. Nilai koma juga diterima dan akan disimpan sebagai desimal dua angka.</div>
                            <div class="flex justify-end gap-2"><button type="button" @click="importOpen=false" class="rounded-2xl border px-5 py-2 font-bold">Batal</button><button class="rounded-2xl bg-red-600 px-5 py-2 font-black text-white">Import</button></div>
                        </form>
                    </div>
                </div>
            @endif

            @if(session('import_report'))
                @php($report=session('import_report'))
                <div x-show="reportOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
                    <div class="w-full max-w-xl rounded-[28px] bg-white p-6 shadow-2xl">
                        <h3 class="text-lg font-black text-slate-900">Resume Import Nilai</h3>
                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4 text-center">
                            <div class="rounded-2xl bg-emerald-50 p-4"><p class="text-2xl font-black text-emerald-700">{{ $report['created'] }}</p><p class="text-xs font-bold text-emerald-700">Ditambahkan</p></div>
                            <div class="rounded-2xl bg-blue-50 p-4"><p class="text-2xl font-black text-blue-700">{{ $report['updated'] }}</p><p class="text-xs font-bold text-blue-700">Diperbarui</p></div>
                            <div class="rounded-2xl bg-red-50 p-4"><p class="text-2xl font-black text-red-700">{{ $report['scores'] }}</p><p class="text-xs font-bold text-red-700">Total Nilai</p></div>
                            <div class="rounded-2xl bg-amber-50 p-4"><p class="text-2xl font-black text-amber-700">{{ $report['skipped'] }}</p><p class="text-xs font-bold text-amber-700">Dilewati</p></div>
                        </div>
                        @if(!empty($report['messages']))
                            <div class="mt-4 rounded-2xl bg-slate-50 p-4 text-xs text-slate-600">@foreach($report['messages'] as $msg)<p>{{ $msg }}</p>@endforeach</div>
                        @endif
                        <button @click="reportOpen=false" class="mt-5 w-full rounded-2xl bg-slate-900 px-5 py-2 font-black text-white">Tutup</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
