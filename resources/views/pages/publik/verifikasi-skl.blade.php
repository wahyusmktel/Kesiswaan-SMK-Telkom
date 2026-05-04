<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Keterangan Lulus</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto max-w-4xl my-10 p-8 bg-white shadow-lg rounded-lg">
        <div class="text-center mb-6">
            <svg class="mx-auto h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Dokumen Resmi Terverifikasi</h1>
            <p class="text-gray-500">Surat Keterangan Lulus ini sah dan resmi dikeluarkan oleh SMK Telkom Lampung.</p>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <span class="text-green-800 font-semibold text-sm">✓ Dokumen ini terverifikasi keasliannya dalam sistem akademik SMK Telkom Lampung</span>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Siswa</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ strtoupper($kelulusan->siswa->nama_lengkap) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">NIS</dt>
                    <dd class="mt-1 text-gray-900">{{ $kelulusan->siswa->nis }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Kelas</dt>
                    <dd class="mt-1 text-gray-900">
                        @php
                            $rombel = $kelulusan->siswa->rombels
                                ->filter(fn($r) => str_starts_with($r->kelas->nama_kelas ?? '', 'XII'))
                                ->first() ?? $kelulusan->siswa->rombels->first();
                        @endphp
                        {{ $rombel?->kelas?->nama_kelas ?? '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tahun Pelajaran</dt>
                    <dd class="mt-1 text-gray-900">{{ $kelulusan->pengumumanKelulusan?->tahunPelajaran?->tahun ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status Kelulusan</dt>
                    <dd class="mt-1">
                        @if($kelulusan->status === 'lulus')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">LULUS</span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">TIDAK LULUS</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Penetapan</dt>
                    <dd class="mt-1 text-gray-900">
                        {{ $kelulusan->pengumumanKelulusan?->tanggal_surat
                            ? \Carbon\Carbon::parse($kelulusan->pengumumanKelulusan->tanggal_surat)->translatedFormat('d F Y')
                            : '-' }}
                    </dd>
                </div>
            </dl>
        </div>

        @if($kelulusan->catatan)
        <div class="border-t border-gray-200 pt-4 mt-4">
            <dt class="text-sm font-medium text-gray-500">Catatan</dt>
            <dd class="mt-1 text-gray-700">{{ $kelulusan->catatan }}</dd>
        </div>
        @endif
    </div>

    <footer class="mt-6 mb-10 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} Sistem Akademik SMK Telkom Lampung.
    </footer>
</body>

</html>
