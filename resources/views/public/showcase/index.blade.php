<x-guest-layout>
    {{-- Decorative Background Elements --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-1/4 -right-1/4 w-[800px] h-[800px] bg-blue-400/20 rounded-full blur-[120px] mix-blend-multiply"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-[800px] h-[800px] bg-purple-400/20 rounded-full blur-[120px] mix-blend-multiply"></div>
    </div>

    <div class="min-h-screen pt-24 pb-20">
        {{-- Navbar --}}
        <nav class="fixed top-0 left-0 right-0 z-50 px-6 py-4 transition-all duration-300 ease-out bg-white/80 backdrop-blur-lg border-b border-gray-100">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden p-1 shadow-md">
                        <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo" class="object-contain w-full h-full">
                    </div>
                    <span class="font-outfit font-black text-xl tracking-tighter text-gray-900">SISFO <span class="text-red-500">TS</span></span>
                </a>
                <div class="flex items-center gap-6">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-900 font-bold text-sm hidden sm:block">Beranda Utama</a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-gray-900 hover:bg-black text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors shadow-lg shadow-gray-900/20">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors shadow-lg shadow-blue-600/30">Login</a>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Hero & Search Section --}}
        <div class="max-w-7xl mx-auto px-6 pt-10 pb-16">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <div class="inline-flex items-center justify-center p-2 bg-blue-50 text-blue-600 rounded-2xl mb-6 shadow-sm border border-blue-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <h1 class="text-5xl md:text-6xl font-black text-gray-900 tracking-tight leading-tight mb-6">
                    Temukan <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Talenta Terbaik</span> SMK Telkom
                </h1>
                <p class="text-xl text-gray-600 font-medium leading-relaxed">
                    Eksplorasi portofolio karya dan keahlian siswa-siswi berprestasi kami. Terhubunglah dengan inovator muda masa depan.
                </p>
            </div>

            <form action="{{ route('public.showcase.index') }}" method="GET" class="max-w-2xl mx-auto relative group">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                    <svg class="h-6 w-6 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama siswa, jurusan, atau skill (cth: Laravel, Design)..." 
                    class="w-full pl-16 pr-32 py-5 bg-white border-2 border-gray-100 rounded-3xl text-gray-900 font-medium text-lg focus:border-blue-500 focus:ring-0 shadow-xl shadow-gray-200/50 transition-all outline-none">
                <button type="submit" class="absolute inset-y-2 right-2 px-8 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl transition-colors shadow-md">
                    Cari
                </button>
            </form>
            
            @if(isset($search) && $search !== '')
                <div class="text-center mt-6">
                    <p class="text-gray-600">Menampilkan hasil pencarian untuk: <span class="font-black text-gray-900">"{{ $search }}"</span></p>
                    <a href="{{ route('public.showcase.index') }}" class="text-sm font-bold text-blue-600 hover:underline mt-2 inline-block">Hapus Pencarian</a>
                </div>
            @endif
        </div>

        {{-- Students Grid --}}
        <div class="max-w-7xl mx-auto px-6">
            @if($students->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($students as $student)
                        <a href="{{ route('public.showcase.show', $student->id) }}" class="group bg-white rounded-[32px] overflow-hidden border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full relative">
                            
                            {{-- Header Card Pattern --}}
                            <div class="h-24 bg-gradient-to-br from-blue-50 to-purple-50 relative overflow-hidden">
                                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#3B82F6 1px, transparent 1px); background-size: 16px 16px;"></div>
                            </div>
                            
                            {{-- Avatar --}}
                            <div class="px-6 relative -mt-12 mb-3">
                                <div class="w-24 h-24 bg-white rounded-2xl p-1.5 shadow-lg border border-gray-50 mx-auto transform group-hover:scale-105 transition-transform duration-300">
                                    <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl overflow-hidden flex items-center justify-center text-3xl">
                                        @if($student->avatar)
                                            <img src="{{ Storage::url($student->avatar) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                                        @else
                                            👋
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="px-6 pb-6 text-center flex flex-col flex-1">
                                <h3 class="text-xl font-black text-gray-900 mb-1 leading-tight group-hover:text-blue-600 transition-colors">{{ $student->name }}</h3>
                                
                                @if($student->masterSiswa && $student->masterSiswa->jurusan)
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">{{ $student->masterSiswa->jurusan->nama_jurusan }}</p>
                                @else
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Siswa SMK Telkom</p>
                                @endif
                                
                                {{-- Top Skills Badges --}}
                                @if($student->siswaSkills->count() > 0)
                                    <div class="flex flex-wrap justify-center gap-1.5 mb-6">
                                        @foreach($student->siswaSkills->take(3) as $skill)
                                            <span class="inline-flex px-2.5 py-1 rounded-lg text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-100">
                                                {{ $skill->name }}
                                            </span>
                                        @endforeach
                                        @if($student->siswaSkills->count() > 3)
                                            <span class="inline-flex px-2.5 py-1 rounded-lg text-[10px] font-black bg-gray-50 text-gray-500 border border-gray-100">
                                                +{{ $student->siswaSkills->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between text-sm text-gray-500 font-bold">
                                    <div class="flex items-center gap-1.5" title="Jumlah Karya">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $student->siswaProjects->count() }} Karya
                                    </div>
                                    <div class="text-blue-600 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity -translate-x-2 group-hover:translate-x-0 transform duration-300">
                                        Lihat Profil <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="mt-12">
                    {{ $students->links() }}
                </div>
            @else
                <div class="py-24 text-center max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gray-100 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Talenta Tidak Ditemukan</h3>
                    <p class="text-gray-500 font-medium">Coba gunakan kata kunci lain untuk mencari siswa atau keahlian yang Anda butuhkan.</p>
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>
