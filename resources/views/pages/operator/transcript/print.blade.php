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
            @if($transcriptConfig->manual_signature_enabled)
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-600 font-black text-white">✓</div>
                    <div>
                        <p class="text-sm font-black">Mode arsip scan bertanda tangan aktif</p>
                        <p class="mt-1 text-xs font-semibold leading-relaxed text-emerald-700">PDF akan ditempeli hasil scan tanda tangan kepala sekolah dan diraster dalam mode {{ $transcriptConfig->scan_color_mode === 'grayscale' ? 'hitam putih' : 'berwarna' }}. Posisi gambar mengikuti pengaturan Config Transkrip.</p>
                    </div>
                </div>
            @endif
            @if(session('success'))
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-black">Periksa kembali data koreksi.</p>
                    <p class="mt-1">{{ $errors->first() }}</p>
                </div>
            @endif

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
                        <div class="mt-2 flex flex-wrap gap-2">
                            <a href="{{ route('operator.transcript.print.classroom', array_filter([
                                    'rombel_id' => $selectedRombel->id,
                                    'graduation_period_id' => $mode === 'alumni' ? $selectedGraduationPeriod?->id : null,
                                ])) }}" target="_blank"
                                class="inline-flex rounded-2xl bg-red-600 px-5 py-2 text-sm font-black text-white hover:bg-red-700">
                                {{ $mode === 'alumni' ? 'Cetak Ulang Satu Kelas' : 'Cetak Semua Siswa' }}
                            </a>
                            <a href="{{ route('operator.transcript.print.classroom.zip', array_filter([
                                    'rombel_id' => $selectedRombel->id,
                                    'graduation_period_id' => $mode === 'alumni' ? $selectedGraduationPeriod?->id : null,
                                ])) }}"
                                class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 bg-white px-5 py-2 text-sm font-black text-slate-700 hover:border-slate-400 hover:bg-slate-50">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M12 3v12m0 0 4-4m-4 4-4-4" />
                                    <path d="M5 19h14" />
                                </svg>
                                Cetak ZIP
                            </a>
                        </div>
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
                                @php
                                    $correction = $mode === 'alumni'
                                        ? $student->transcriptReprintCorrections->firstWhere('rombel_id', $selectedRombel->id)
                                        : null;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-900">{{ $correction?->corrected_name ?? $student->nama_lengkap }}</p>
                                        <p class="text-xs text-slate-500">{{ $student->nis }}</p>
                                        @if($correction)
                                            <span class="mt-1 inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-black uppercase text-blue-700">Identitas cetak diperbaiki</span>
                                        @endif
                                    </td>
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
                                        <div class="flex flex-col items-end gap-2">
                                            <a href="{{ route('operator.transcript.print.student', ['student' => $student, 'rombel_id' => $selectedRombel->id]) }}" target="_blank"
                                                class="whitespace-nowrap rounded-2xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-black text-red-700 hover:bg-red-100">
                                                {{ $mode === 'alumni' ? 'Cetak Ulang' : 'Cetak PDF' }}
                                            </a>
                                            @if($mode === 'alumni')
                                                <button type="button" x-data
                                                    @click="$dispatch('open-modal', 'correction-{{ $student->id }}')"
                                                    class="whitespace-nowrap text-xs font-black text-blue-700 hover:text-blue-900">
                                                    Perbaiki Identitas
                                                </button>
                                                @if($correction)
                                                    <button type="button" x-data
                                                        @click="$dispatch('open-modal', 'history-{{ $student->id }}')"
                                                        class="whitespace-nowrap text-xs font-black text-slate-500 hover:text-slate-900">
                                                        Riwayat ({{ $correction->histories->count() }})
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Tidak ada siswa di rombel ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($mode === 'alumni')
                    @foreach($students as $student)
                        @php
                            $correction = $student->transcriptReprintCorrections->firstWhere('rombel_id', $selectedRombel->id);
                            $originalBirthPlace = $student->dapodik?->tempat_lahir ?? $student->tempat_lahir;
                            $originalBirthDate = $student->dapodik?->tanggal_lahir ?? $student->tanggal_lahir;
                            $showCorrectionModal = $errors->any() && (int) old('correction_student_id') === $student->id;
                        @endphp

                        <x-modal name="correction-{{ $student->id }}" :show="$showCorrectionModal" maxWidth="xl" focusable>
                            <form method="POST" action="{{ route('operator.transcript.print.corrections.update', $student) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="rombel_id" value="{{ $selectedRombel->id }}">
                                <input type="hidden" name="correction_student_id" value="{{ $student->id }}">

                                <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                                    <div>
                                        <h3 class="text-lg font-black text-slate-900">Perbaiki Identitas Cetak Ulang</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ $student->nama_lengkap }} · {{ $selectedRombel->kelas?->nama_kelas }}</p>
                                    </div>
                                    <button type="button" @click="$dispatch('close-modal', 'correction-{{ $student->id }}')" title="Tutup" class="text-2xl leading-none text-slate-400 hover:text-slate-700">&times;</button>
                                </div>

                                <div class="space-y-5 px-6 py-5">
                                    <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                                        Perubahan ini hanya dipakai pada PDF cetak ulang angkatan {{ $selectedGraduationPeriod?->tahun }}. Data Master Siswa dan Dapodik tidak akan berubah.
                                    </div>

                                    <label class="block">
                                        <span class="mb-1 block text-sm font-bold text-slate-700">Nama Lengkap untuk Cetak Ulang</span>
                                        <input name="corrected_name" required maxlength="255"
                                            value="{{ old('correction_student_id') == $student->id ? old('corrected_name') : ($correction?->corrected_name ?? $student->nama_lengkap) }}"
                                            class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">
                                        <span class="mt-1 block text-xs text-slate-400">Data asli: {{ $student->nama_lengkap }}</span>
                                    </label>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <label class="block">
                                            <span class="mb-1 block text-sm font-bold text-slate-700">Tempat Lahir</span>
                                            <input name="corrected_birth_place" required maxlength="255"
                                                value="{{ old('correction_student_id') == $student->id ? old('corrected_birth_place') : ($correction?->corrected_birth_place ?? $originalBirthPlace) }}"
                                                class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">
                                            <span class="mt-1 block text-xs text-slate-400">Data asli: {{ $originalBirthPlace ?: '-' }}</span>
                                        </label>
                                        <label class="block">
                                            <span class="mb-1 block text-sm font-bold text-slate-700">Tanggal Lahir</span>
                                            <input type="date" name="corrected_birth_date" required
                                                value="{{ old('correction_student_id') == $student->id ? old('corrected_birth_date') : ($correction?->corrected_birth_date?->format('Y-m-d') ?? $originalBirthDate?->format('Y-m-d')) }}"
                                                class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">
                                            <span class="mt-1 block text-xs text-slate-400">Data asli: {{ $originalBirthDate?->locale('id')->translatedFormat('d F Y') ?? '-' }}</span>
                                        </label>
                                    </div>

                                    <label class="block">
                                        <span class="mb-1 block text-sm font-bold text-slate-700">Alasan Perbaikan</span>
                                        <textarea name="correction_reason" rows="3" required minlength="5" maxlength="2000"
                                            placeholder="Contoh: Penyesuaian dengan akta kelahiran"
                                            class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">{{ old('correction_student_id') == $student->id ? old('correction_reason') : '' }}</textarea>
                                        <span class="mt-1 block text-xs text-slate-400">Alasan akan disimpan dalam riwayat audit dan tidak dapat dihapus dari catatan.</span>
                                    </label>
                                </div>

                                <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4">
                                    <button type="button" @click="$dispatch('close-modal', 'correction-{{ $student->id }}')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-black text-slate-700">Batal</button>
                                    <button class="rounded-xl bg-red-600 px-5 py-2 text-sm font-black text-white hover:bg-red-700">Simpan Perbaikan</button>
                                </div>
                            </form>
                        </x-modal>

                        @if($correction)
                            <x-modal name="history-{{ $student->id }}" maxWidth="2xl">
                                <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                                    <div>
                                        <h3 class="text-lg font-black text-slate-900">Riwayat Perbaikan Identitas</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ $student->nama_lengkap }} · {{ $selectedRombel->kelas?->nama_kelas }}</p>
                                    </div>
                                    <button type="button" @click="$dispatch('close-modal', 'history-{{ $student->id }}')" title="Tutup" class="text-2xl leading-none text-slate-400 hover:text-slate-700">&times;</button>
                                </div>
                                <div class="max-h-[70vh] space-y-4 overflow-y-auto px-6 py-5">
                                    @foreach($correction->histories as $history)
                                        <article class="rounded-lg border border-slate-200 p-4">
                                            <div class="flex flex-col gap-1 border-b border-slate-100 pb-3 sm:flex-row sm:items-center sm:justify-between">
                                                <p class="text-sm font-black text-slate-900">{{ $history->changer?->name ?? 'Pengguna terhapus' }}</p>
                                                <p class="text-xs font-bold text-slate-500">{{ $history->created_at->locale('id')->translatedFormat('d F Y H:i') }}</p>
                                            </div>
                                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                                <div class="rounded-lg bg-red-50 p-3">
                                                    <p class="mb-2 text-xs font-black uppercase text-red-700">Sebelum</p>
                                                    <p class="text-sm font-bold text-slate-900">{{ data_get($history->old_data, 'name') ?: '-' }}</p>
                                                    <p class="mt-1 text-xs text-slate-600">{{ data_get($history->old_data, 'birth_place') ?: '-' }}, {{ data_get($history->old_data, 'birth_date') ? \Carbon\Carbon::parse(data_get($history->old_data, 'birth_date'))->locale('id')->translatedFormat('d F Y') : '-' }}</p>
                                                </div>
                                                <div class="rounded-lg bg-emerald-50 p-3">
                                                    <p class="mb-2 text-xs font-black uppercase text-emerald-700">Sesudah</p>
                                                    <p class="text-sm font-bold text-slate-900">{{ data_get($history->new_data, 'name') ?: '-' }}</p>
                                                    <p class="mt-1 text-xs text-slate-600">{{ data_get($history->new_data, 'birth_place') ?: '-' }}, {{ data_get($history->new_data, 'birth_date') ? \Carbon\Carbon::parse(data_get($history->new_data, 'birth_date'))->locale('id')->translatedFormat('d F Y') : '-' }}</p>
                                                </div>
                                            </div>
                                            <p class="mt-3 text-sm text-slate-600"><span class="font-black text-slate-800">Alasan:</span> {{ $history->reason }}</p>
                                        </article>
                                    @endforeach
                                </div>
                            </x-modal>
                        @endif
                    @endforeach
                @endif
            @elseif($mode === 'alumni' && $graduationPeriods->isEmpty())
                <div class="mt-6 rounded-[24px] border border-amber-200 bg-amber-50 px-6 py-10 text-center">
                    <p class="font-black text-amber-900">Belum ada arsip angkatan lulusan</p>
                    <p class="mt-1 text-sm text-amber-700">Arsip akan muncul dari rombel kelas XII pada tahun pelajaran Genap sebelumnya.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
