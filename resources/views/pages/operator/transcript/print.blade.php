<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-black text-slate-900">Cetak Transkrip</h2>
            <p class="text-sm text-slate-500">Cetak transkrip nilai PDF satuan per siswa atau masal per kelas.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="mb-2 inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-black uppercase tracking-widest text-red-700">PDF Transkrip</div>
                        <h3 class="text-2xl font-black text-slate-900">Pilih Kelas Tingkat Akhir</h3>
                        <p class="mt-1 max-w-2xl text-sm text-slate-500">Setelah kelas dipilih, Anda bisa mencetak satu siswa atau seluruh siswa dalam kelas tersebut.</p>
                    </div>
                    <form class="flex flex-col gap-2 sm:flex-row">
                        <select name="rombel_id" class="min-w-[280px] rounded-2xl border-slate-200 text-sm" required>
                            <option value="">Pilih Rombel/Kelas</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" @selected(request('rombel_id') == $rombel->id)>{{ $rombel->kelas?->nama_kelas ?? 'Rombel' }} - {{ $rombel->tahun_ajaran }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-2xl border border-slate-200 px-5 py-2 text-sm font-black">Tampilkan</button>
                    </form>
                </div>
            </div>

            @if($selectedRombel)
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-black uppercase text-slate-500">Rombel</p><p class="mt-2 text-xl font-black text-slate-900">{{ $selectedRombel->kelas?->nama_kelas ?? '-' }}</p></div>
                    <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-black uppercase text-slate-500">Jumlah Siswa</p><p class="mt-2 text-xl font-black text-slate-900">{{ $students->count() }}</p></div>
                    <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-black uppercase text-slate-500">Cetak Masal</p>
                        <a href="{{ route('operator.transcript.print.classroom', ['rombel_id' => $selectedRombel->id]) }}" target="_blank" class="mt-2 inline-flex rounded-2xl bg-red-600 px-5 py-2 text-sm font-black text-white">Cetak Semua Siswa</a>
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
                                    <td class="px-6 py-4 text-right"><a href="{{ route('operator.transcript.print.student', $student) }}" target="_blank" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-black text-red-700">Cetak PDF</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Tidak ada siswa di rombel ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
