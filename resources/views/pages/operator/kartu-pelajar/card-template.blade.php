@props([
    'siswa',
    'barcodeBase64' => null,
    'kepsekData' => [],
    'namaKelas' => null,
])

@php
    $kelas = $namaKelas ?: ($siswa->rombels->first()?->kelas?->nama_kelas ?? '-');
    $fotoUrl = $siswa->foto_url;
    $kepsekNama = $kepsekData['nama'] ?? 'Kepala Sekolah';
    $kepsekNip = $kepsekData['nip'] ?? '-';
    $signaturePath = $kepsekData['signature_path'] ?? null;
    $hasSignature = !empty($signaturePath) && file_exists($signaturePath);
@endphp

<div class="kartu-pelajar-cr80 relative bg-white overflow-hidden shadow-md border border-gray-200 rounded-xl"
     style="width: 86mm; height: 54mm; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif;">
    
    {{-- Header Merah Telkom --}}
    <div class="relative bg-gradient-to-r from-red-700 via-red-600 to-red-700 px-3 py-1.5 flex items-center justify-between text-white border-b-2 border-gray-300"
         style="height: 12.5mm;">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-white p-0.5 flex-shrink-0 flex items-center justify-center shadow-sm">
                @if (file_exists(public_path('loader.png')))
                    <img src="{{ asset('loader.png') }}" alt="Logo" class="w-full h-full object-contain">
                @elseif (file_exists(public_path('images/asset-report/smk-telkom-lampung-white.png')))
                    <img src="{{ asset('images/asset-report/smk-telkom-lampung-white.png') }}" alt="Logo" class="w-full h-full object-contain">
                @else
                    <span class="text-[9px] font-black text-red-600">ST</span>
                @endif
            </div>
            <div class="leading-tight">
                <h2 class="text-[10px] font-black tracking-wider uppercase text-white drop-shadow-sm">SMK TELKOM LAMPUNG</h2>
                <h3 class="text-[8px] font-bold tracking-widest text-red-100 uppercase">KARTU TANDA PELAJAR</h3>
                <p class="text-[5.5px] text-red-200 truncate">Jl. P. Ranau No. 88, Airan Raya, Jati Agung, Lampung Selatan</p>
            </div>
        </div>
        <div class="text-right flex-shrink-0">
            <span class="text-[7.5px] font-bold px-1.5 py-0.5 bg-white/20 rounded text-white border border-white/30">{{ $kelas }}</span>
        </div>
    </div>

    {{-- Body Kartu --}}
    <div class="p-2 flex gap-2.5" style="height: 38.5mm;">
        {{-- Sisi Kiri: Foto Siswa & Barcode --}}
        <div class="flex flex-col items-center justify-between flex-shrink-0" style="width: 22mm;">
            {{-- Foto Siswa --}}
            <div class="w-full bg-gray-100 border border-gray-300 rounded overflow-hidden flex items-center justify-center shadow-inner"
                 style="height: 25mm;">
                @if ($fotoUrl)
                    <img src="{{ $fotoUrl }}" alt="{{ $siswa->nama_lengkap }}" class="w-full h-full object-cover">
                @else
                    <div class="flex flex-col items-center justify-center text-gray-400 p-1">
                        <svg class="w-7 h-7 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <span class="text-[6px] text-gray-400 text-center mt-0.5">FOTO</span>
                    </div>
                @endif
            </div>

            {{-- 1D Barcode (NIS) --}}
            <div class="w-full flex flex-col items-center mt-0.5">
                @if ($barcodeBase64)
                    <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Barcode" class="h-3 w-full object-contain">
                @endif
                <span class="text-[6.5px] font-mono font-bold text-gray-800 tracking-wider leading-none mt-0.5">{{ $siswa->nis }}</span>
            </div>
        </div>

        {{-- Sisi Kanan: Biodata & Tanda Tangan --}}
        <div class="flex-1 flex flex-col justify-between min-w-0">
            {{-- Biodata --}}
            <div class="space-y-0.5">
                <div class="border-b border-red-500 pb-0.5 mb-1">
                    <h4 class="text-[9.5px] font-black text-gray-900 uppercase truncate leading-tight">{{ $siswa->nama_lengkap }}</h4>
                </div>

                <table class="w-full text-[7px] text-gray-700 leading-tight">
                    <tbody>
                        <tr>
                            <td class="text-gray-500 w-14 whitespace-nowrap align-top py-0.5">NIS</td>
                            <td class="w-2 align-top py-0.5">:</td>
                            <td class="font-bold text-gray-900 align-top py-0.5">{{ $siswa->nis }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 whitespace-nowrap align-top py-0.5">TTL</td>
                            <td class="align-top py-0.5">:</td>
                            <td class="font-semibold text-gray-800 align-top py-0.5">
                                {{ ($siswa->tempat_lahir ? $siswa->tempat_lahir . ', ' : '') . ($siswa->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d M Y') : '-') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 whitespace-nowrap align-top py-0.5">Jenis Kelamin</td>
                            <td class="align-top py-0.5">:</td>
                            <td class="font-semibold text-gray-800 align-top py-0.5">
                                {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 whitespace-nowrap align-top py-0.5">Alamat</td>
                            <td class="align-top py-0.5">:</td>
                            <td class="font-semibold text-gray-800 align-top py-0.5 leading-snug">
                                <span class="line-clamp-2">{{ Str::limit($siswa->alamat ?: '-', 50) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Tanda Tangan Kepala Sekolah --}}
            <div class="flex justify-end pr-1 mt-auto">
                <div class="text-center" style="width: 32mm;">
                    <p class="text-[6.5px] text-gray-600 font-medium leading-none">Kepala Sekolah,</p>
                    
                    <div class="h-5 flex items-center justify-center my-0.5">
                        @if ($hasSignature)
                            @php
                                $sigContent = base64_encode(file_get_contents($signaturePath));
                                $sigMime = mime_content_type($signaturePath) ?: 'image/png';
                            @endphp
                            <img src="data:{{ $sigMime }};base64,{{ $sigContent }}" alt="TTD Kepsek" class="h-5 max-w-full object-contain">
                        @else
                            <div class="h-4"></div>
                        @endif
                    </div>

                    <p class="text-[6.5px] font-bold text-gray-900 underline leading-none uppercase">{{ $kepsekNama }}</p>
                    <p class="text-[5.5px] text-gray-500 font-mono leading-none mt-0.5">NIP. {{ $kepsekNip }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Strip Bawah Merah Telkom --}}
    <div class="absolute bottom-0 inset-x-0 bg-red-600" style="height: 1.5mm;"></div>
</div>
