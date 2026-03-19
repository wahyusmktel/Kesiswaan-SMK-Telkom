<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Tes IQ') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Toast Alert -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative flex items-center shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden text-center relative">
                
                <!-- Confetti Background Simulation -->
                <div class="absolute top-0 left-0 w-full h-full pointer-events-none opacity-20 overflow-hidden">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-indigo-300 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob"></div>
                    <div class="absolute top-0 -right-10 w-40 h-40 bg-yellow-300 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob animation-delay-2000"></div>
                    <div class="absolute -bottom-8 left-20 w-40 h-40 bg-pink-300 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob animation-delay-4000"></div>
                </div>

                <div class="p-8 sm:p-14 relative z-10">
                    <div class="mx-auto w-24 h-24 mb-6 rounded-full flex flex-col items-center justify-center border-4 border-indigo-100 bg-indigo-50 shadow-inner">
                        <svg class="w-12 h-12 text-indigo-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>

                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Tes Selesai!</h2>
                    <p class="mt-2 text-lg text-gray-500">Berikut adalah hasil evaluasi Anda.</p>

                    <div class="mt-10 mb-8 max-w-sm mx-auto bg-gradient-to-br from-indigo-50 to-indigo-100/50 rounded-2xl p-6 border border-indigo-100 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-24 h-24 text-indigo-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.64-2.25 1.64-1.74 0-2.1-.96-2.17-1.92H8.01c.12 1.98 1.49 3.01 2.9 3.32V19h2.34v-1.63c1.78-.28 3.01-1.3 3.01-3.02 0-2.17-1.88-2.71-3.95-3.21z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-indigo-800 uppercase tracking-wider mb-2">Skor IQ Final</p>
                        <p class="text-6xl font-black text-indigo-600 drop-shadow-sm">{{ $result->iq_score }}</p>
                        <p class="mt-4 text-sm font-medium text-gray-600">
                            Status: <span class="font-bold {{ $result->iq_score >= 120 ? 'text-green-600' : ($result->iq_score >= 100 ? 'text-indigo-600' : 'text-orange-600') }}">
                                @if($result->iq_score >= 130) Sangat Superior (Jenius)
                                @elseif($result->iq_score >= 120) Superior
                                @elseif($result->iq_score >= 110) Di Atas Rata-rata
                                @elseif($result->iq_score >= 90) Rata-rata Normal
                                @else Di Bawah Rata-rata
                                @endif
                            </span>
                        </p>
                        <p class="mt-1 text-xs text-gray-500 font-medium">Jawaban Benar: {{ $result->total_correct }} / 10</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mt-10">
                        <a href="{{ route('tes-iq.start') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="mr-2 -ml-1 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Utama
                        </a>
                        
                        <a href="{{ route('tes-iq.certificate', $result) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3 border border-transparent text-base font-bold rounded-xl shadow-lg text-white bg-indigo-600 hover:bg-indigo-700 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <svg class="mr-2 -ml-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Unduh Sertifikat PDF
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
