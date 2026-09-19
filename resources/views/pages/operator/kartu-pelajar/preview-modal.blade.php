<div class="flex flex-col items-center">
    <div class="shadow-2xl rounded-xl overflow-hidden mb-6 border border-gray-200">
        @include('pages.operator.kartu-pelajar.card-template', [
            'siswa' => $siswa,
            'barcodeBase64' => $barcodeBase64,
            'kepsekData' => $kepsekData,
            'namaKelas' => $siswa->rombels->first()?->kelas?->nama_kelas ?? '-',
        ])
    </div>

    <div class="flex items-center justify-center gap-3 w-full border-t border-gray-100 pt-4">
        <a href="{{ route('operator.kartu-pelajar.cetak', $siswa->id) }}" target="_blank"
            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold shadow-sm transition gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Kartu
        </a>
        <a href="{{ route('operator.kartu-pelajar.download-jpg', $siswa->id) }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download JPG
        </a>
        <button type="button" @click="closePreviewModal()"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
            Tutup
        </button>
    </div>
</div>
