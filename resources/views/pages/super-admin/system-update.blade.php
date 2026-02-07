<x-app-layout px="0">
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Pembaharuan Sistem</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola dan perbarui aplikasi langsung dari repositori Git.</p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Info Card -->
            <div class="lg:col-span-1">
                <div class="overflow-hidden bg-white shadow-sm rounded-2xl border border-gray-100">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 mb-4 bg-indigo-100 rounded-xl text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Status Deployment</h3>
                        <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                            Klik tombol di bawah untuk menarik pembaharuan terbaru dari repositori Git dan menjalankan skrip otomasi deployment.
                        </p>

                        <div class="mt-6 space-y-4">
                            <div class="flex items-center text-sm">
                                <span class="w-3 h-3 mr-2 {{ $scriptExists ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                                <span class="text-gray-700">Script `deploy_sisfo.sh`: <strong>{{ $scriptExists ? 'Tersedia' : 'Tidak Ditemukan' }}</strong></span>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button id="deployBtn" 
                                    @if(!$scriptExists) disabled @endif
                                    class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-lg shadow-indigo-200 disabled:opacity-50 disabled:shadow-none">
                                <svg id="btnIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                </svg>
                                <span id="btnText">Mulai Pembaharuan</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-100 rounded-xl">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Perhatian</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Proses ini dapat menyebabkan aplikasi tidak dapat diakses sementara. Pastikan Anda melakukan pembaharuan di jam tidak sibuk.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terminal Card -->
            <div class="lg:col-span-2">
                <div class="overflow-hidden bg-[#1e1e1e] shadow-2xl rounded-2xl border border-gray-800 flex flex-col h-[500px]">
                    <div class="px-4 py-2 bg-[#323233] flex items-center justify-between border-b border-gray-700">
                        <div class="flex space-x-2">
                            <div class="w-3 h-3 bg-[#ff5f56] rounded-full"></div>
                            <div class="w-3 h-3 bg-[#ffbd2e] rounded-full"></div>
                            <div class="w-3 h-3 bg-[#27c93f] rounded-full"></div>
                        </div>
                        <div class="text-[11px] font-medium text-gray-400 font-mono tracking-wider uppercase">Deployment Terminal</div>
                        <div class="w-10"></div>
                    </div>
                    <div id="terminal" class="flex-1 p-6 font-mono text-sm text-gray-300 overflow-y-auto leading-relaxed custom-scrollbar bg-black/20">
                        <div class="text-gray-500 mb-2">// Siap melakukan pembaharuan sistem...</div>
                        <div class="flex items-center">
                            <span class="text-green-500 mr-2">admin@sisfo:~$</span>
                            <span id="currentCommand" class="text-white"></span>
                            <span class="animate-pulse ml-1 opacity-75">_</span>
                        </div>
                        <div id="outputContainer" class="mt-4 whitespace-pre-wrap breakdown-words"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1e1e1e;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #444;
        }
        .animate-pulse {
            animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            border-left: 2px solid white;
            height: 1.2em;
            display: inline-block;
            vertical-align: middle;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('deployBtn').addEventListener('click', function() {
            const btn = this;
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            const terminal = document.getElementById('terminal');
            const outputContainer = document.getElementById('outputContainer');
            const currentCommand = document.getElementById('currentCommand');

            Swal.fire({
                title: 'Konfirmasi Pembaharuan',
                text: "Apakah Anda yakin ingin memperbarui aplikasi sekarang? Sistem akan menarik kode terbaru dari repositori.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Perbarui Sekarang!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-gray-100',
                    title: 'text-xl font-bold text-gray-900',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-semibold transition-all',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-semibold transition-all'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // UI State Loading
                    btn.disabled = true;
                    btnText.innerText = 'Memproses...';
                    btnIcon.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                    
                    currentCommand.innerText = './deploy_sisfo.sh';
                    outputContainer.innerHTML = '<div class="text-blue-400">[SYSTEM] Menghubungkan ke server...</div>';
                    terminal.scrollTop = terminal.scrollHeight;

                    // Execute Deployment
                    fetch('{{ route("super-admin.system-update.deploy") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            outputContainer.innerHTML += `<div class="text-green-400 mt-2">${data.output}</div>`;
                            outputContainer.innerHTML += `<div class="text-green-500 font-bold mt-4">✓ ${data.message}</div>`;
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonColor: '#4f46e5',
                                customClass: {
                                    popup: 'rounded-2xl',
                                    confirmButton: 'rounded-xl'
                                }
                            });
                        } else {
                            outputContainer.innerHTML += `<div class="text-red-400 mt-2">${data.output || ''}</div>`;
                            outputContainer.innerHTML += `<div class="text-red-500 font-bold mt-4">✗ Gagal: ${data.message}</div>`;
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message,
                                confirmButtonColor: '#4f46e5',
                                customClass: {
                                    popup: 'rounded-2xl',
                                    confirmButton: 'rounded-xl'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        outputContainer.innerHTML += `<div class="text-red-500 font-bold mt-4">✗ Error: Tidak dapat menghubungi server.</div>`;
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menghubungi server.',
                            confirmButtonColor: '#4f46e5',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'rounded-xl'
                            }
                        });
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btnText.innerText = 'Mulai Pembaharuan';
                        btnIcon.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" /></svg>';
                        terminal.scrollTop = terminal.scrollHeight;
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
