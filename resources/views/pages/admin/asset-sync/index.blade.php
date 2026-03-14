<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Sinkronisasi Data Aset') }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">Tarik data aset terbaru dari Aplikasi Manajemen Aset ke database lokal</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-4">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-bold text-sm">Berhasil!</p>
                        <p class="text-xs">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-4">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-bold text-sm">Gagal!</p>
                        <p class="text-xs">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="p-8 lg:p-12 border-b border-gray-100 flex flex-col items-center text-center">
                    <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
                        <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    
                    <h3 class="text-2xl font-black text-gray-800 mb-2">Sinkronisasi Aset</h3>
                    <p class="text-gray-500 text-sm max-w-lg mb-8">
                        Data aset perlu disinkronkan agar aplikasi ini memiliki salinan data terbaru dari server aset utama. Proses ini akan mengunduh dan memperbarui semua data inventaris secara lokal.
                    </p>

                    <div class="grid grid-cols-2 gap-6 w-full max-w-md mb-10">
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">Total Aset Lokal</p>
                            <p class="text-3xl font-black text-gray-800">{{ number_format($totalAssets) }}</p>
                        </div>
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">Terakhir Sinkron</p>
                            <p class="text-sm font-bold text-gray-800 mt-2">
                                @if($lastSync)
                                    {{ \Carbon\Carbon::parse($lastSync)->diffForHumans() }}
                                    <span class="block text-xs text-gray-500 font-normal mt-0.5">{{ \Carbon\Carbon::parse($lastSync)->format('d M Y, H:i') }}</span>
                                @else
                                    <span class="text-red-500">Belum pernah tersinkron</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('super-admin.asset-sync.process') }}" method="POST" class="w-full max-w-sm" x-data="{ syncing: false }" @submit="syncing = true">
                        @csrf
                        <button type="submit" 
                            class="w-full flex items-center justify-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-blue-500/30 hover:-translate-y-1"
                            :class="{ 'opacity-70 cursor-wait': syncing }"
                            :disabled="syncing">
                            
                            <svg x-show="!syncing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>

                            <svg x-show="syncing" class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            
                            <span x-text="syncing ? 'Sedang Sinkronisasi...' : 'Mulai Sinkronisasi Sekarang'"></span>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">
                    Sistem akan mengambil data via API dari: <span class="font-mono bg-gray-100 px-2 py-1 rounded text-gray-500">{{ config('asset.api_url') }}</span>
                </p>
            </div>
            
        </div>
    </div>
</x-app-layout>
