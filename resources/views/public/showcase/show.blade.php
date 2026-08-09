<x-guest-layout>
    {{-- Decorative Background Elements --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-50/50 to-transparent"></div>
    </div>

    <div class="min-h-screen pt-24 pb-20">
        {{-- Navbar --}}
        <nav class="fixed top-0 left-0 right-0 z-50 px-6 py-4 transition-all duration-300 ease-out bg-white/80 backdrop-blur-lg border-b border-gray-100">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <a href="{{ route('public.showcase.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-900 font-bold transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Direktori
                </a>
                <div class="flex items-center gap-3">
                    <span class="font-outfit font-black text-lg tracking-tighter text-gray-300">SISFO <span class="text-gray-200">TS</span></span>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-6 pt-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Profile Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Profile Card --}}
                    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8 text-center relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-br from-blue-600 to-purple-600"></div>
                        
                        <div class="w-32 h-32 bg-white rounded-3xl p-1.5 shadow-xl mx-auto relative z-10 mt-6 mb-4 transform hover:scale-105 transition-transform duration-300">
                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl overflow-hidden flex items-center justify-center text-4xl">
                                @if($student->avatar)
                                    <img src="{{ Storage::url($student->avatar) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                                @else
                                    👋
                                @endif
                            </div>
                        </div>
                        
                        <h2 class="text-2xl font-black text-gray-900 mb-1 leading-tight">{{ $student->name }}</h2>
                        @php
                            $latestRombel = $student->masterSiswa?->rombels->last();
                            $jurusan = $latestRombel?->kelas?->jurusan ?? 'Siswa SMK Telkom';
                        @endphp
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-6">{{ $jurusan }}</p>
                        
                        <div class="flex gap-4 border-t border-gray-100 pt-6">
                            <div class="flex-1">
                                <div class="text-2xl font-black text-gray-900">{{ $student->siswaSkills->count() }}</div>
                                <div class="text-xs font-bold text-gray-500 uppercase">Keahlian</div>
                            </div>
                            <div class="w-px bg-gray-100"></div>
                            <div class="flex-1">
                                <div class="text-2xl font-black text-blue-600">{{ $student->siswaProjects->count() }}</div>
                                <div class="text-xs font-bold text-gray-500 uppercase">Karya</div>
                            </div>
                        </div>
                    </div>

                    {{-- Skills --}}
                    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Keahlian Teratas
                        </h3>

                        <div class="space-y-5">
                            @forelse($student->siswaSkills as $skill)
                            <div>
                                <div class="flex justify-between items-end mb-1.5">
                                    <span class="font-bold text-gray-700 text-sm">{{ $skill->name }}</span>
                                    <span class="font-black text-blue-600 text-sm">{{ $skill->percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-1000 ease-out" style="width: {{ $skill->percentage }}%"></div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <p class="text-sm font-bold text-gray-400">Belum ada data keahlian.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Projects Grid --}}
                <div class="lg:col-span-2">
                    <div class="mb-6">
                        <h3 class="text-2xl font-black text-gray-900 flex items-center gap-3">
                            <span class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            Galeri Karya Siswa
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($student->siswaProjects as $project)
                        <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col h-full">
                            {{-- Image Preview --}}
                            <div class="h-56 w-full bg-gray-100 relative overflow-hidden">
                                @if($project->image_path)
                                    <img src="{{ Storage::url($project->image_path) }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                
                                @if($project->image_path)
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3 backdrop-blur-sm">
                                    <a href="{{ Storage::url($project->image_path) }}" target="_blank" class="px-5 py-2.5 bg-white/20 hover:bg-white text-white hover:text-gray-900 rounded-xl font-bold text-sm backdrop-blur-md transition-all shadow-lg">
                                        Lihat Gambar Penuh
                                    </a>
                                </div>
                                @endif
                            </div>

                            <div class="p-6 flex flex-col flex-1">
                                <h4 class="text-xl font-black text-gray-900 leading-tight mb-3">{{ $project->title }}</h4>
                                <p class="text-sm text-gray-600 leading-relaxed mb-6">{{ $project->description }}</p>
                                
                                <div class="mt-auto flex gap-3 pt-4 border-t border-gray-50">
                                    @if($project->project_url)
                                    <a href="{{ $project->project_url }}" target="_blank" class="flex-1 flex items-center justify-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold py-2.5 rounded-xl transition-colors text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Live Demo
                                    </a>
                                    @endif
                                    @if($project->github_url)
                                    <a href="{{ $project->github_url }}" target="_blank" class="flex-1 flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-xl transition-colors text-sm">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/></svg>
                                        Source Code
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-24 text-center bg-white rounded-[32px] border border-dashed border-gray-200">
                            <div class="w-20 h-20 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-5">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 mb-2">Belum Ada Karya</h3>
                            <p class="text-gray-500 font-medium text-sm">Siswa ini belum mengunggah proyek portofolio apapun.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
