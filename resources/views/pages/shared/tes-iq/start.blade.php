<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tes IQ Berstandar') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden transform transition-all hover:shadow-md">
                
                <div class="p-8 sm:p-12 text-center relative overflow-hidden bg-indigo-600">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white via-indigo-400 to-indigo-900 pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="mx-auto w-24 h-24 mb-6 bg-white rounded-full flex items-center justify-center border-4 border-indigo-200 shadow-lg">
                            <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <h2 class="text-3xl font-extrabold text-white mb-4 tracking-tight">Tes Intelegensi Umum (IQ)</h2>
                        <p class="text-indigo-100 text-lg max-w-2xl mx-auto">
                            Ukur kemampuan penalaran logis, spasial, dan matematis Anda melalui set pertanyaan yang diadaptasi dari standar internasional.
                        </p>
                    </div>
                </div>

                <div class="p-8 sm:p-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <span class="flex items-center justify-center h-10 w-10 rounded-xl bg-indigo-100 text-indigo-600 font-bold">1</span>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-bold text-gray-900">Format Ujian Pendek</h4>
                                <p class="mt-1 text-gray-500 text-sm">Terdiri dari 10 pertanyaan pilihan ganda yang dirancang untuk menguji batas pemikiran Anda dengan cepat.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <span class="flex items-center justify-center h-10 w-10 rounded-xl bg-green-100 text-green-600 font-bold">2</span>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-bold text-gray-900">Sertifikat Digital Valid</h4>
                                <p class="mt-1 text-gray-500 text-sm">Dapatkan Sertifikat PDF berukuran A4 secara instan lengkap dengan QR Code validasi keaslian.</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center pt-6 border-t border-gray-100">
                        <a href="{{ route('tes-iq.test') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 hover:-translate-y-1 shadow-lg hover:shadow-xl transition-all duration-200">
                            Mulai Tes Sekarang
                            <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                        </a>
                        <p class="mt-4 text-xs text-gray-400">Pastikan Anda berada di tempat yang tenang sebelum memulai.</p>
                    </div>
                </div>

                @if($results->count() > 0)
                <div class="bg-gray-50 px-8 py-6 border-t border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Riwayat Tes Anda
                    </h3>
                    <div class="space-y-3">
                        @foreach($results as $res)
                        <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:border-indigo-300 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Skor IQ: <span class="text-indigo-600 text-lg">{{ $res->iq_score }}</span></p>
                                <p class="text-xs text-gray-500">{{ $res->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('tes-iq.result', $res) }}" class="text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg transition-colors">Lihat Detail</a>
                                <a href="{{ route('tes-iq.certificate', $res) }}" class="text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-2 rounded-lg shadow-sm flex items-center transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Unduh PDF
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
            </div>
        </div>
    </div>
</x-app-layout>
