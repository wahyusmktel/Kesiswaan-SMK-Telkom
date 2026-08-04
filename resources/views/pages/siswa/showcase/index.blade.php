<x-app-layout>
    <div class="py-12 w-full flex flex-col bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 w-full">
            
            {{-- Header Section --}}
            <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white p-8 rounded-[32px] shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="absolute -top-16 -right-16 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex items-center gap-5 relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Pameran Karya & Keahlian</h2>
                        <p class="text-gray-500 mt-1 font-medium">Etalase digital untuk memamerkan keterampilan dan hasil karya terbaikmu.</p>
                    </div>
                </div>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Skills Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-black text-gray-900">Keahlian Saya</h3>
                            <a href="{{ route('siswa.showcase.skill.create') }}" class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center hover:bg-blue-100 transition-colors" title="Tambah Keahlian">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </a>
                        </div>

                        <div class="space-y-6">
                            @forelse($skills as $skill)
                            <div class="group relative">
                                <div class="flex justify-between items-end mb-1">
                                    <span class="font-bold text-gray-700 text-sm">{{ $skill->name }}</span>
                                    <span class="font-black text-blue-600 text-sm">{{ $skill->percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $skill->percentage }}%"></div>
                                </div>
                                
                                {{-- Quick actions (appears on hover) --}}
                                <div class="absolute -top-1 right-12 hidden group-hover:flex items-center gap-1 bg-white shadow-sm border border-gray-100 rounded-md p-1 z-10">
                                    <a href="{{ route('siswa.showcase.skill.edit', $skill->id) }}" class="p-1 text-gray-500 hover:text-blue-600 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                    <form action="{{ route('siswa.showcase.skill.destroy', $skill->id) }}" method="POST" onsubmit="return confirm('Hapus keahlian ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1 text-gray-500 hover:text-red-600 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-500 mb-4">Belum ada skill yang ditambahkan</p>
                                <a href="{{ route('siswa.showcase.skill.create') }}" class="inline-block px-4 py-2 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition-colors">Tambah Skill</a>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    
                    {{-- Mini Profile Preview (Optional, just for aesthetics) --}}
                    <div class="bg-gradient-to-br from-gray-900 to-black rounded-[32px] p-8 text-white relative overflow-hidden shadow-xl shadow-gray-900/20">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">Profil Publik</h4>
                        <div class="text-2xl font-black mb-4">{{ Auth::user()->name }}</div>
                        <p class="text-gray-300 text-sm mb-6">Tingkatkan terus keahlian dan perbanyak karyamu untuk membangun rekam jejak digital yang kuat.</p>
                        
                        <div class="flex gap-4">
                            <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 flex-1 border border-white/10">
                                <div class="text-2xl font-black">{{ $skills->count() }}</div>
                                <div class="text-xs font-bold text-gray-400">Skills</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 flex-1 border border-white/10">
                                <div class="text-2xl font-black">{{ $projects->count() }}</div>
                                <div class="text-xs font-bold text-gray-400">Projects</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Projects Grid --}}
                <div class="lg:col-span-2">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-black text-gray-900">Galeri Project</h3>
                        <a href="{{ route('siswa.showcase.project.create') }}" class="group inline-flex items-center justify-center px-5 py-2.5 font-bold text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-600/30 active:scale-95 text-sm gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Upload Karya
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($projects as $project)
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
                            {{-- Image Preview --}}
                            <div class="h-48 w-full bg-gray-100 relative overflow-hidden">
                                <img src="{{ Storage::url($project->image_path) }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3 backdrop-blur-sm">
                                    <a href="{{ route('siswa.showcase.project.edit', $project->id) }}" class="w-10 h-10 bg-white text-gray-900 rounded-full flex items-center justify-center hover:scale-110 transition-transform shadow-lg" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('siswa.showcase.project.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Hapus project ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-10 h-10 bg-white text-red-600 rounded-full flex items-center justify-center hover:scale-110 transition-transform shadow-lg" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="p-6 flex flex-col flex-1">
                                <h4 class="text-xl font-black text-gray-900 leading-tight mb-2">{{ $project->title }}</h4>
                                <p class="text-sm text-gray-500 line-clamp-3 mb-6">{{ $project->description }}</p>
                                
                                <div class="mt-auto flex gap-3">
                                    @if($project->project_url)
                                    <a href="{{ $project->project_url }}" target="_blank" class="flex-1 flex items-center justify-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold py-2 rounded-xl transition-colors text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Demo
                                    </a>
                                    @endif
                                    @if($project->github_url)
                                    <a href="{{ $project->github_url }}" target="_blank" class="flex-1 flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 rounded-xl transition-colors text-sm">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/></svg>
                                        Source Code
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-20 text-center bg-white rounded-[32px] border border-dashed border-gray-300 flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mb-5">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 mb-2">Belum Ada Karya</h3>
                            <p class="text-gray-500 font-medium max-w-sm mx-auto mb-6 text-sm">Unggah karya terbaikmu berupa desain, aplikasi, web, atau inovasi lainnya.</p>
                            <a href="{{ route('siswa.showcase.project.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl transition-colors shadow-lg shadow-blue-600/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Upload Karya Pertama
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
