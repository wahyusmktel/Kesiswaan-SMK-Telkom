<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold text-gray-800">Persetujuan Izin Pegawai Tetap</h2></x-slot>
    <div class="space-y-5 px-4 py-6 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-gradient-to-r from-slate-950 to-red-950 p-6 text-white shadow-lg">
            <h3 class="text-xl font-black">Persetujuan Akhir Kepala Sekolah</h3>
            <p class="mt-2 text-sm text-slate-300">Hanya izin Pegawai Tetap yang telah disetujui KAUR SDM masuk ke tahap ini. Izin baru sah dan dikecualikan dari ketidakhadiran setelah Anda menyetujuinya.</p>
        </div>
        @if(session('success'))<div class="rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800">{{ $errors->first() }}</div>@endif
        <div class="flex flex-wrap gap-2">
            @foreach(['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $key => $label)
                <a href="{{ route('kepala-sekolah.persetujuan-izin-guru.index', ['status' => $key]) }}" class="rounded-full px-4 py-2 text-sm font-bold {{ $status === $key ? 'bg-red-600 text-white' : 'border bg-white text-gray-600' }}">{{ $label }}</a>
            @endforeach
        </div>
        <div class="overflow-x-auto rounded-2xl border bg-white shadow-sm">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500"><tr><th class="p-4">Pegawai</th><th class="p-4">Waktu Izin</th><th class="p-4">Jenis & Alasan</th><th class="p-4">Persetujuan SDM</th><th class="p-4">Keputusan</th></tr></thead>
                <tbody class="divide-y">
                    @forelse($izins as $izin)
                        <tr class="align-top">
                            <td class="p-4"><p class="font-black text-gray-900">{{ $izin->guru?->nama_lengkap ?? '-' }}</p><p class="mt-1 text-xs text-gray-500">{{ $izin->guru?->dapodikGuru?->status_kepegawaian ?: 'Status tidak tersedia' }}</p></td>
                            <td class="p-4 text-gray-700">{{ $izin->tanggal_mulai->translatedFormat('d M Y H:i') }}<br><span class="text-gray-400">s.d.</span> {{ $izin->tanggal_selesai->translatedFormat('d M Y H:i') }}</td>
                            <td class="p-4"><span class="rounded-full bg-violet-50 px-2 py-1 text-xs font-bold text-violet-700">{{ $izin->categoryLabel() }} · {{ $izin->jenis_izin }}</span><p class="mt-2 max-w-sm whitespace-normal text-gray-600">{{ $izin->deskripsi }}</p>
                                <details class="mt-3"><summary class="cursor-pointer text-xs font-bold text-indigo-700">Jadwal yang ditinggalkan</summary>
                                    @forelse($izin->jadwals as $jadwal)<p class="mt-2 text-xs">{{ $jadwal->rombel?->kelas?->nama_kelas }} · {{ $jadwal->mataPelajaran?->nama_mapel }} · {{ $jadwal->jam_mulai }}–{{ $jadwal->jam_selesai }}</p>@empty<p class="mt-2 text-xs text-gray-500">Tidak ada jadwal terlampir.</p>@endforelse
                                </details>
                            </td>
                            <td class="p-4"><x-status-badge-izin :status="$izin->status_sdm"/><p class="mt-1 text-xs text-gray-500">{{ $izin->sdm?->name }} · {{ $izin->sdm_at?->format('d/m/Y H:i') }}</p></td>
                            <td class="p-4">
                                @if($izin->status_kepala_sekolah === 'menunggu')
                                    <div class="flex flex-col gap-2">
                                        <form method="POST" action="{{ route('kepala-sekolah.persetujuan-izin-guru.approve', $izin) }}" onsubmit="return confirm('Setujui izin ini sebagai keputusan akhir?');">@csrf @method('PATCH')<button class="w-full rounded-lg bg-emerald-600 px-4 py-2 font-bold text-white">Setujui</button></form>
                                        <form method="POST" action="{{ route('kepala-sekolah.persetujuan-izin-guru.reject', $izin) }}" class="space-y-2" onsubmit="return confirm('Tolak izin ini?');">@csrf @method('PATCH')<textarea name="catatan_kepala_sekolah" required maxlength="2000" rows="2" class="w-full rounded-lg border-gray-300 text-xs" placeholder="Alasan penolakan"></textarea><button class="w-full rounded-lg border border-red-300 px-4 py-2 font-bold text-red-700">Tolak</button></form>
                                    </div>
                                @else
                                    <x-status-badge-izin :status="$izin->status_kepala_sekolah"/>
                                    <p class="mt-1 text-xs text-gray-500">{{ $izin->kepalaSekolah?->name }} · {{ $izin->kepala_sekolah_at?->format('d/m/Y H:i') }}</p>
                                    @if($izin->catatan_kepala_sekolah)<p class="mt-2 max-w-xs text-xs text-red-700">{{ $izin->catatan_kepala_sekolah }}</p>@endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-10 text-center text-gray-500">Tidak ada izin dengan status {{ strtolower($status) }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($izins->hasPages())<div class="border-t p-4">{{ $izins->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
