<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Detail Tes Kesehatan Mata</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $eyeExam->examinee_name }} - {{ $eyeExam->examined_at->translatedFormat('d M Y H:i') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ signOpen: false }">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">{{ session('error') }}</div>
        @endif

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <a href="{{ route('uks.eye-exams.index') }}" class="w-fit rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">Kembali</a>
            <div class="flex flex-wrap gap-2">
                @if(!$document)
                    <button type="button" @click="signOpen = true" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">Tandatangani Manual</button>
                @endif
                <a href="{{ route('uks.eye-exams.resume', $eyeExam) }}" target="_blank" class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white hover:bg-red-700">Cetak Resume PDF</a>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Peserta Tes</p>
                        <h3 class="mt-2 text-2xl font-black text-gray-900">{{ $eyeExam->examinee_name }}</h3>
                        <p class="mt-1 text-sm font-semibold text-gray-500">{{ ucfirst($eyeExam->examinee_type) }} - {{ $eyeExam->examinee_identity }}</p>
                    </div>
                    <span class="w-fit rounded-full px-4 py-2 text-xs font-black {{ $eyeExam->conclusion === 'perlu_rujukan' ? 'bg-red-50 text-red-700' : ($eyeExam->conclusion === 'perlu_observasi' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">{{ $eyeExam->conclusion_label }}</span>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs font-black text-gray-400 uppercase">Tes Buta Warna</p>
                        <p class="mt-2 text-lg font-black text-gray-900">{{ $eyeExam->color_blind_label }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs font-black text-gray-400 uppercase">Mata Kanan</p>
                        <p class="mt-2 text-lg font-black text-gray-900">{{ $eyeExam->visual_acuity_right ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs font-black text-gray-400 uppercase">Mata Kiri</p>
                        <p class="mt-2 text-lg font-black text-gray-900">{{ $eyeExam->visual_acuity_left ?: '-' }}</p>
                    </div>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Waktu Pemeriksaan</p>
                        <p class="mt-1 font-bold text-gray-900">{{ $eyeExam->examined_at->translatedFormat('l, d F Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Catatan Tes Buta Warna</p>
                        <p class="mt-1 text-gray-700">{{ $eyeExam->color_blind_notes ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Temuan Kesehatan Mata</p>
                        <p class="mt-1 text-gray-700">{{ $eyeExam->eye_health_findings ?: '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-xs font-black uppercase tracking-widest text-blue-600">Rekomendasi UKS</p>
                        <p class="mt-2 text-sm font-bold text-blue-900">{{ $eyeExam->recommendation ?: 'Tidak ada rekomendasi khusus.' }}</p>
                    </div>
                    @if($eyeExam->notes)
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Catatan Internal</p>
                            <p class="mt-1 text-gray-700">{{ $eyeExam->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="font-black text-gray-900">Tanda Tangan Digital</h3>
                    <div class="mt-4 rounded-2xl bg-gray-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-bold text-gray-900">Resume Pemeriksaan Mata</p>
                                <p class="text-xs text-gray-500">{{ $document ? 'Ditandatangani ' . $document->signed_at->translatedFormat('d M Y H:i') : 'Belum ditandatangani' }}</p>
                            </div>
                            @if($document)
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">Sah</span>
                            @endif
                        </div>
                        @if($qrBase64)
                            <img src="{{ $qrBase64 }}" class="mt-4 h-24 w-24 rounded-xl border border-gray-200 bg-white p-2" alt="QR Verifikasi">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div x-show="signOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">
            <form method="POST" action="{{ route('uks.eye-exams.sign', $eyeExam) }}" class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
                @csrf
                <h3 class="text-xl font-black text-gray-900">Tanda Tangan Manual</h3>
                <p class="mt-1 text-sm text-gray-500">Masukkan PIN tanda tangan digital Petugas UKS.</p>
                <input type="password" name="pin" required inputmode="numeric" class="mt-5 w-full rounded-xl border-gray-300 text-center text-lg font-black tracking-widest focus:border-red-500 focus:ring-red-500" placeholder="PIN">
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="signOpen = false" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold">Batal</button>
                    <button class="rounded-xl bg-red-600 px-4 py-2 text-sm font-black text-white">Tandatangani</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
