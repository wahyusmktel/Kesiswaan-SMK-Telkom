<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('URL Shortener') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen" x-data="shortenerApp()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative flex items-center shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative shadow-sm" role="alert">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <strong class="font-bold text-sm">Gagal memendekkan URL</strong>
                    </div>
                    <ul class="list-disc ml-9 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Top Action / Form Builder -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden transform transition-all hover:shadow-md">
                <div class="p-6 sm:p-8 bg-indigo-600 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white via-indigo-400 to-indigo-900 pointer-events-none"></div>
                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold text-white mb-2">Persingkat Tautan Anda</h3>
                        <p class="text-indigo-100 text-sm mb-6">Buat URL panjang Anda menjadi lebih rapi dan mudah dibagikan. Gunakan custom alias (+/- 5 detik) atau biarkan sistem membuat kode rahasia untukmu.</p>
                        
                        <form action="{{ route('shortener.store') }}" method="POST" class="space-y-4 sm:space-y-0 sm:flex sm:gap-4 items-start">
                            @csrf
                            <!-- URL Input -->
                            <div class="flex-grow">
                                <label for="original_url" class="sr-only">URL Asli</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </div>
                                    <input type="url" name="original_url" id="original_url" required placeholder="https://contoh.com/halaman-yang-sangat-panjang/sekali" class="block w-full pl-11 pr-4 py-4 border-transparent rounded-xl focus:ring-2 focus:ring-white focus:border-white sm:text-lg bg-indigo-700/50 text-white placeholder-indigo-300 backdrop-blur-md transition-all shadow-inner">
                                </div>
                            </div>

                            <!-- Custom Alias -->
                            <div class="sm:w-64">
                                <label for="custom_code" class="sr-only">Kustom Tautan (Opsional)</label>
                                <div class="relative flex rounded-xl shadow-inner bg-indigo-700/50 backdrop-blur-md overflow-hidden focus-within:ring-2 focus-within:ring-white transition-all">
                                    <span class="inline-flex items-center px-3 border-r border-indigo-600/50 text-indigo-300 sm:text-sm font-semibold selection:bg-transparent">
                                        /
                                    </span>
                                    <input type="text" name="custom_code" id="custom_code" placeholder="Alias (Opsional)" value="{{ old('custom_code') }}" class="flex-1 block w-full px-3 py-4 border-transparent bg-transparent text-white placeholder-indigo-300 focus:ring-0 sm:text-md">
                                </div>
                            </div>

                            <button type="submit" class="w-full sm:w-auto flex-shrink-0 inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-bold rounded-xl shadow-lg text-indigo-600 bg-white hover:bg-gray-50 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-indigo-600 transition-all duration-200">
                                <span>Persingkat</span>
                                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Content List -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Riwayat Tautan
                    </h3>
                    <span class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold leading-none text-indigo-800 bg-indigo-100 rounded-full">Total: {{ $shortUrls->total() }}</span>
                </div>

                @if($shortUrls->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tautan Asli</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tautan Pendek</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Klik</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dibuat Pada</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @foreach($shortUrls as $url)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="ml-0">
                                                    <div class="text-sm font-medium text-gray-900 max-w-xs sm:max-w-sm truncate" title="{{ $url->original_url }}">
                                                        {{ $url->original_url }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $fullShortUrl = url('/' . $url->short_code);
                                            @endphp
                                            <div class="flex items-center gap-2 group">
                                                <a href="{{ $fullShortUrl }}" target="_blank" class="text-sm font-bold text-indigo-600 hover:text-indigo-900 hover:underline flex items-center gap-1 group-hover:bg-indigo-50 px-2 py-1 rounded-md transition-colors">
                                                    {{ $fullShortUrl }}
                                                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                                <button @click="copyToClipboard('{{ $fullShortUrl }}')" class="text-gray-400 hover:text-indigo-600 focus:outline-none p-1.5 rounded-md hover:bg-gray-100 transition-all pointer-events-auto shadow-sm border border-transparent hover:border-gray-200" title="Salin URL">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 justify-center min-w-[3rem]">
                                                {{ number_format($url->clicks) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $url->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('shortener.destroy', $url) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus tautan ini? Redirect tidak akan berfungsi lagi.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-white bg-white hover:bg-red-500 border border-red-200 rounded-lg px-3 py-1.5 transition-all shadow-sm flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    <span class="hidden sm:inline">Hapus</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($shortUrls->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                            {{ $shortUrls->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-20 px-4 bg-gray-50/30">
                        <div class="mx-auto w-24 h-24 mb-6 bg-indigo-50 rounded-full flex items-center justify-center border border-indigo-100 shadow-sm">
                            <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        <h3 class="mt-2 text-xl font-bold text-gray-900">Belum ada tautan pendek</h3>
                        <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">Mulai pendekkan tautan pertama Anda menggunakan form biru di atas.</p>
                    </div>
                @endif
            </div>
            
            <!-- Toast Notification Element -->
            <div x-show="toastShow" style="display: none;" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed bottom-0 right-0 z-50 p-4 w-full sm:max-w-sm">
                 <div class="bg-indigo-600 rounded-xl shadow-lg border border-indigo-500 overflow-hidden flex transform transition-all items-center">
                    <div class="bg-indigo-700/50 p-4 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="p-4 py-3 flex-1 flex items-center justify-between">
                        <p class="text-sm font-medium text-white" x-text="toastMessage"></p>
                        <button @click="toastShow = false" class="text-indigo-200 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                 </div>
            </div>
        </div>
    </div>

    <!-- Alpine Widget Script for Copy Feature -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('shortenerApp', () => ({
                toastShow: false,
                toastMessage: '',
                copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                        this.toastMessage = 'Tautan pendek berhasil disalin!';
                        this.toastShow = true;
                        setTimeout(() => { this.toastShow = false; }, 3000);
                    }).catch(err => {
                        console.error('Failed to copy text: ', err);
                    });
                }
            }))
        })
    </script>
</x-app-layout>
