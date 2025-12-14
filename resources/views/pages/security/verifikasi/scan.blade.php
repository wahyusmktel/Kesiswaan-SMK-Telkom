<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Pindai QR Code</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="max-w-xl mx-auto px-4">

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="p-6 text-center">

                    <div
                        class="relative w-full max-w-sm mx-auto aspect-square bg-black rounded-xl overflow-hidden mb-6 border-4 border-slate-800">
                        <div id="reader" class="w-full h-full"></div>
                        <div
                            class="absolute inset-0 border-2 border-red-500/50 pointer-events-none m-8 rounded-lg animate-pulse">
                        </div>
                    </div>

                    <div id="result" class="mb-6 font-mono text-sm min-h-[20px] text-gray-600">
                        Pastikan izin kamera aktif.
                    </div>

                    <div class="flex flex-col gap-3">
                        <button id="startButton"
                            class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-md hover:bg-indigo-500 transition-colors flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Aktifkan Kamera
                        </button>

                        <button id="stopButton" style="display: none;"
                            class="w-full py-3 bg-red-100 text-red-600 font-bold rounded-xl hover:bg-red-200 transition-colors">
                            Matikan Kamera
                        </button>

                        <a href="{{ route('security.verifikasi.index') }}"
                            class="w-full py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const readerElement = document.getElementById('reader');
                const resultElement = document.getElementById('result');
                const startButton = document.getElementById('startButton');
                const stopButton = document.getElementById('stopButton');
                let html5QrCode;

                function onScanSuccess(decodedText, decodedResult) {
                    resultElement.innerHTML =
                        `<span class="text-green-600 font-bold">QR Code Terdeteksi! Memproses...</span>`;

                    if (html5QrCode) {
                        html5QrCode.stop().then(() => {
                            window.location.href = decodedText;
                        }).catch(err => {
                            window.location.href = decodedText;
                        });
                    } else {
                        window.location.href = decodedText;
                    }
                }

                function onScanFailure(error) {
                    // Do nothing
                }

                startButton.addEventListener('click', () => {
                    html5QrCode = new Html5Qrcode("reader");
                    html5QrCode.start({
                            facingMode: "environment"
                        }, {
                            fps: 10,
                            qrbox: {
                                width: 250,
                                height: 250
                            }
                        },
                        onScanSuccess,
                        onScanFailure
                    ).then(() => {
                        startButton.style.display = 'none';
                        stopButton.style.display = 'inline-block';
                        resultElement.innerHTML = 'Arahkan kamera ke QR Code Surat Izin.';
                    }).catch(err => {
                        resultElement.innerHTML =
                            `<span class="text-red-500 font-bold">Gagal akses kamera: ${err}</span>`;
                    });
                });

                stopButton.addEventListener('click', () => {
                    if (html5QrCode) {
                        html5QrCode.stop().then(() => {
                            startButton.style.display = 'flex';
                            stopButton.style.display = 'none';
                            resultElement.innerHTML = 'Kamera dimatikan.';
                        }).catch(err => console.error(err));
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
