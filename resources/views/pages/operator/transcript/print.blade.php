<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900">Cetak Transkrip</h2>
                <p class="text-sm text-slate-500">Cetak baru atau cetak ulang transkrip dari arsip angkatan lulusan.</p>
            </div>
            <div class="inline-flex w-fit rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
                <a href="{{ route('operator.transcript.print.index', ['mode' => 'active']) }}"
                    class="rounded-lg px-4 py-2 text-sm font-black transition {{ $mode === 'active' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                    Angkatan Aktif
                </a>
                <a href="{{ route('operator.transcript.print.index', ['mode' => 'alumni']) }}"
                    class="rounded-lg px-4 py-2 text-sm font-black transition {{ $mode === 'alumni' ? 'bg-red-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                    Arsip Lulusan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="mb-2 inline-flex rounded-full px-3 py-1 text-xs font-black uppercase tracking-widest {{ $mode === 'alumni' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700' }}">
                            {{ $mode === 'alumni' ? 'Cetak Ulang Arsip' : 'PDF Transkrip' }}
                        </div>
                        <h3 class="text-2xl font-black text-slate-900">
                            {{ $mode === 'alumni' ? 'Pilih Angkatan dan Kelas Lulusan' : 'Pilih Kelas Tingkat Akhir Aktif' }}
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-slate-500">
                            @if($mode === 'alumni')
                                Buka kembali data lulusan berdasarkan tahun pelajaran kelulusan. Nomor transkrip yang telah terkunci akan digunakan kembali saat PDF dicetak.
                            @else
                                Cetak satu siswa atau seluruh kelas. Nomor transkrip dikunci saat cetak pertama agar tidak berubah dan tidak dipakai siswa lain.
                            @endif
                        </p>
                    </div>
                    <form class="grid gap-2 sm:grid-cols-2 lg:min-w-[580px]">
                        <input type="hidden" name="mode" value="{{ $mode }}">
                        @if($mode === 'alumni')
                            <label>
                                <span class="mb-1 block text-xs font-black uppercase text-slate-500">Angkatan Lulusan</span>
                                <select name="graduation_period_id" class="w-full rounded-2xl border-slate-200 text-sm" required>
                                    @forelse($graduationPeriods as $period)
                                        <option value="{{ $period->id }}" @selected($selectedGraduationPeriod?->id === $period->id)>
                                            Lulusan {{ $period->tahun }}
                                        </option>
                                    @empty
                                        <option value="">Belum ada arsip angkatan</option>
                                    @endforelse
                                </select>
                            </label>
                        @endif
                        <label class="{{ $mode === 'active' ? 'sm:col-span-2' : '' }}">
                            <span class="mb-1 block text-xs font-black uppercase text-slate-500">Rombel/Kelas</span>
                        <select name="rombel_id" class="min-w-[280px] rounded-2xl border-slate-200 text-sm" required>
                            <option value="">Pilih Rombel/Kelas</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" @selected($selectedRombel?->id === $rombel->id)>
                                    {{ $rombel->kelas?->nama_kelas ?? 'Rombel' }} - {{ $rombel->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                        </label>
                        <button class="rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-black text-white sm:col-span-2 hover:bg-red-600">Tampilkan Data</button>
                    </form>
                </div>
            </div>

            @if($selectedRombel)
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-black uppercase text-slate-500">{{ $mode === 'alumni' ? 'Arsip Rombel' : 'Rombel' }}</p>
                        <p class="mt-2 text-xl font-black text-slate-900">{{ $selectedRombel->kelas?->nama_kelas ?? '-' }}</p>
                        <p class="mt-1 text-xs font-bold text-slate-500">{{ $selectedRombel->tahunPelajaran?->tahun ?? $selectedRombel->tahun_ajaran }}</p>
                    </div>
                    <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-black uppercase text-slate-500">Jumlah Siswa</p><p class="mt-2 text-xl font-black text-slate-900">{{ $students->count() }}</p></div>
                    <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-black uppercase text-slate-500">{{ $mode === 'alumni' ? 'Cetak Ulang Massal' : 'Cetak Massal' }}</p>
                        <a href="{{ route('operator.transcript.print.classroom', array_filter([
                                'rombel_id' => $selectedRombel->id,
                                'graduation_period_id' => $mode === 'alumni' ? $selectedGraduationPeriod?->id : null,
                            ])) }}" target="_blank"
                            class="mt-2 inline-flex rounded-2xl bg-red-600 px-5 py-2 text-sm font-black text-white hover:bg-red-700">
                            {{ $mode === 'alumni' ? 'Cetak Ulang Satu Kelas' : 'Cetak Semua Siswa' }}
                        </a>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase text-slate-500">No</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase text-slate-500">Siswa</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase text-slate-500">NISN</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase text-slate-500">Nomor Ijazah</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase text-slate-500">Nomor Transkrip</th>
                                <th class="px-6 py-4 text-right text-xs font-black uppercase text-slate-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $student)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4"><p class="font-bold text-slate-900">{{ $student->nama_lengkap }}</p><p class="text-xs text-slate-500">{{ $student->nis }}</p></td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $student->dapodik?->nisn ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $student->transcriptDiplomaNumber?->diploma_number ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        @if($student->transcriptNumber)
                                            <p class="max-w-[260px] break-words text-xs font-bold text-emerald-700">{{ $student->transcriptNumber->number }}</p>
                                            <span class="mt-1 inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-black uppercase text-emerald-700">Terkunci</span>
                                        @else
                                            <span class="text-xs font-bold text-amber-600">Dibuat saat cetak pertama</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('operator.transcript.print.student', ['student' => $student, 'rombel_id' => $selectedRombel->id]) }}" target="_blank"
                                            class="whitespace-nowrap rounded-2xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-black text-red-700 hover:bg-red-100">
                                            {{ $mode === 'alumni' ? 'Cetak Ulang' : 'Cetak PDF' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Tidak ada siswa di rombel ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif($mode === 'alumni' && $graduationPeriods->isEmpty())
                <div class="mt-6 rounded-[24px] border border-amber-200 bg-amber-50 px-6 py-10 text-center">
                    <p class="font-black text-amber-900">Belum ada arsip angkatan lulusan</p>
                    <p class="mt-1 text-sm text-amber-700">Arsip akan muncul dari rombel kelas XII pada tahun pelajaran Genap sebelumnya.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
