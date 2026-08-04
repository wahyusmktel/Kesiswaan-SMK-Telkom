<x-app-layout>
    <div class="py-12 w-full flex flex-col bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 w-full">
            
            {{-- Header Section --}}
            <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white p-8 rounded-[32px] shadow-sm border border-gray-100">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Portofolio Sertifikat</h2>
                        <p class="text-gray-500 mt-1 font-medium">Arsipkan bukti keahlian dan prestasimu sebagai portofolio profesional.</p>
                    </div>
                </div>
                
                <a href="{{ route('siswa.certificates.create') }}" class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-white transition-all duration-200 bg-gray-900 border border-transparent rounded-xl hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 shadow-xl active:scale-95">
                    <svg class="w-5 h-5 mr-2 -ml-1 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Sertifikat Baru
                </a>
            </div>

            @if(session('success'))
            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold flex-1">{{ session('success') }}</span>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endif

            {{-- Certificate Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($certificates as $cert)
                <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col relative h-full">
                    
                    {{-- Expiration Badge --}}
                    @if($cert->expiration_date)
                        @if($cert->expiration_date->isPast())
                            <div class="absolute top-4 right-4 z-10 bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-black shadow-sm flex items-center gap-1 backdrop-blur-sm bg-white/80 border border-red-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Kedaluwarsa
                            </div>
                        @else
                            <div class="absolute top-4 right-4 z-10 bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-xs font-black shadow-sm flex items-center gap-1 backdrop-blur-sm bg-white/80 border border-emerald-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Aktif
                            </div>
                        @endif
                    @endif

                    {{-- Image Preview Area (Gradient background for PDF, direct for image) --}}
                    <div class="h-48 w-full bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center relative overflow-hidden border-b border-gray-50">
                        @if(Str::endsWith($cert->file_path, '.pdf'))
                            <div class="text-indigo-200 group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                            </div>
                            <div class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-lg text-xs font-bold text-gray-600 border border-gray-200 shadow-sm">
                                Dokumen PDF
                            </div>
                        @else
                            <img src="{{ Storage::url($cert->file_path) }}" alt="{{ $cert->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @endif
                        
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3 backdrop-blur-sm">
                            <a href="{{ Storage::url($cert->file_path) }}" target="_blank" class="w-10 h-10 bg-white text-gray-900 rounded-full flex items-center justify-center hover:scale-110 transition-transform shadow-lg" title="Lihat">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('siswa.certificates.edit', $cert->id) }}" class="w-10 h-10 bg-white text-indigo-600 rounded-full flex items-center justify-center hover:scale-110 transition-transform shadow-lg" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="p-6 flex flex-col flex-1">
                        <div class="text-xs font-bold text-indigo-600 mb-1 uppercase tracking-wider">{{ $cert->issuer }}</div>
                        <h3 class="text-xl font-black text-gray-900 leading-tight mb-2 line-clamp-2" title="{{ $cert->name }}">{{ $cert->name }}</h3>
                        
                        <div class="mt-4 space-y-2 mb-6">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Diterbitkan</span>
                                <span class="font-bold text-gray-900">{{ $cert->issue_date->format('d M Y') }}</span>
                            </div>
                            @if($cert->expiration_date)
                            <div class="flex justify-between items-center text-sm pt-2 border-t border-gray-50">
                                <span class="text-gray-500 font-medium">Berlaku s.d</span>
                                <span class="font-bold text-gray-900">{{ $cert->expiration_date->format('d M Y') }}</span>
                            </div>
                            @endif
                        </div>
                        
                        @if($cert->credential_url)
                        <div class="mt-auto">
                            <a href="{{ $cert->credential_url }}" target="_blank" class="w-full flex items-center justify-center gap-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold py-2.5 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                Cek Kredensial
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full py-24 text-center bg-white rounded-[32px] border border-dashed border-gray-300 flex flex-col items-center justify-center">
                    <div class="w-24 h-24 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Belum Ada Portofolio</h3>
                    <p class="text-gray-500 font-medium max-w-md mx-auto mb-8">Arsipkan sertifikat keahlian, prestasi, atau pelatihan yang pernah kamu ikuti untuk membangun portofolio profesionalmu.</p>
                    <a href="{{ route('siswa.certificates.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-lg shadow-indigo-600/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Upload Sertifikat Pertamamu
                    </a>
                </div>
                @endforelse
            </div>
            
            @if($certificates->hasPages())
                <div class="mt-10">
                    {{ $certificates->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
