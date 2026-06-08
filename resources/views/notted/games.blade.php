<x-app-layout px="0">
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl shadow-2xl mb-12">
            <div class="absolute inset-0 bg-grid-white/[0.1] bg-[size:20px_20px]"></div>
            <div class="relative px-8 py-12 md:px-16 md:py-16 flex flex-col md:flex-row items-center justify-between">
                <div class="text-center md:text-left md:max-w-xl">
                    <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight leading-tight">
                        Waktunya Belajar Sambil <span class="text-yellow-400">Main!</span> 🎮
                    </h1>
                    <p class="text-indigo-100 text-lg md:text-xl font-medium mb-8 leading-relaxed">
                        Tingkatkan skill kamu dengan koleksi game edukatif kami. Siapa bilang belajar itu membosankan? Ayo tantang dirimu sekarang!
                    </p>
                    <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                        <a href="#games-list" class="px-8 py-3 bg-white text-indigo-600 font-bold rounded-xl shadow-lg hover:bg-yellow-400 hover:text-indigo-900 transition-all transform hover:-translate-y-1">
                            Lihat Semua Game
                        </a>
                        <div class="flex items-center space-x-2 text-white/80">
                            <div class="flex -space-x-2">
                                <img class="w-8 h-8 rounded-full border-2 border-indigo-500" src="https://placehold.co/100x100?text=A" alt="">
                                <img class="w-8 h-8 rounded-full border-2 border-indigo-500" src="https://placehold.co/100x100?text=B" alt="">
                                <img class="w-8 h-8 rounded-full border-2 border-indigo-500" src="https://placehold.co/100x100?text=C" alt="">
                            </div>
                            <span class="text-sm font-semibold tracking-wide uppercase">100+ Siswa Sedang Bermain</span>
                        </div>
                    </div>
                </div>
                <!-- Abstract Game Elements Illustration -->
                <div class="hidden md:block w-72 h-72 relative">
                    <div class="absolute inset-0 bg-yellow-400/20 blur-3xl rounded-full animate-pulse"></div>
                    <div class="relative w-full h-full flex items-center justify-center">
                        <svg class="w-48 h-48 text-white drop-shadow-2xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a2 2 0 01-2 2H3a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 13a2 2 0 012-2h2a2 2 0 012 2v3a2 2 0 01-2 2H6a2 2 0 01-2-2v-3z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 13a2 2 0 012-2h2a2 2 0 012 2v3a2 2 0 01-2 2h-2a2 2 0 01-2-2v-3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Games Grid -->
        <div id="games-list">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Koleksi Game</h2>
                    <p class="text-sm text-gray-500 font-medium">Temukan tantangan baru setiap hari</p>
                </div>
                <div class="flex gap-2">
                    <button class="px-4 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-bold border border-gray-200 hover:bg-white hover:shadow-sm transition-all">Terbaru</button>
                    <button class="px-4 py-1.5 bg-white text-gray-700 rounded-lg text-sm font-bold border border-gray-200 hover:shadow-sm transition-all focus:ring-2 focus:ring-indigo-500">Populer</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <!-- Word Typing Test -->
                <div class="group relative bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-100/50 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="relative h-48 bg-gradient-to-tr from-indigo-500 to-blue-600">
                        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_20%_30%,#fff_0,transparent_10%)]"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="absolute top-4 right-4 px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-[10px] font-black text-white uppercase tracking-widest border border-white/20">
                            Kecepatan
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Tes Mengetik</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-6 flex-1">
                            Uji seberapa cepat kamu mengetik dan tantang dirimu untuk mencapai rekor baru!
                        </p>
                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex items-center text-xs font-bold text-gray-400">
                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                4.9 (2k+)
                            </div>
                            <a href="{{ route('notted.typing-test') }}" class="px-5 py-2 bg-indigo-50 text-indigo-700 font-bold text-sm rounded-xl border border-indigo-100 hover:bg-indigo-600 hover:text-white transition-all transform hover:scale-105">
                                Mainkan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Snake & Ladder Game -->
                <div class="group relative bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-100/50 hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="relative h-48 bg-gradient-to-tr from-emerald-500 to-teal-600 overflow-hidden">
                        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_80%_20%,#fff_0,transparent_10%)]"></div>
                        <!-- Mini board preview -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="grid grid-cols-5 gap-0.5 opacity-30">
                                <template x-if="true"></template>
                                @for($i = 0; $i < 25; $i++)
                                    <div class="w-5 h-5 rounded-sm {{ in_array($i, [0,4,10,18,24]) ? 'bg-red-300' : (in_array($i, [3,7,15,20]) ? 'bg-yellow-200' : 'bg-white/40') }}"></div>
                                @endfor
                            </div>
                            <!-- Snake -->
                            <svg class="absolute w-32 h-32 text-white/70 group-hover:scale-110 transition-transform duration-300 drop-shadow-lg" viewBox="0 0 100 100" fill="none">
                                <path d="M20 80 Q50 60 80 40 Q95 30 90 15 Q85 5 75 8 Q65 12 70 25 Q75 35 60 45 Q40 58 15 70 Q5 77 8 88 Q12 97 22 95" stroke="currentColor" stroke-width="6" stroke-linecap="round" fill="none"/>
                                <circle cx="22" cy="95" r="5" fill="currentColor"/>
                                <ellipse cx="8" cy="88" rx="4" ry="3" fill="currentColor"/>
                                <!-- Ladder -->
                                <line x1="30" y1="75" x2="55" y2="25" stroke="#fde68a" stroke-width="4" stroke-linecap="round"/>
                                <line x1="42" y1="75" x2="67" y2="25" stroke="#fde68a" stroke-width="4" stroke-linecap="round"/>
                                <line x1="33" y1="65" x2="58" y2="35" stroke="#fde68a" stroke-width="2.5" stroke-linecap="round"/>
                                <line x1="36" y1="55" x2="61" y2="43" stroke="#fde68a" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="absolute top-4 left-4 px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-[10px] font-black text-white uppercase tracking-widest border border-white/20">
                            Edukasi
                        </div>
                        <div class="absolute top-4 right-4 px-3 py-1 bg-emerald-800/40 backdrop-blur-md rounded-lg text-[10px] font-black text-emerald-200 uppercase tracking-widest border border-emerald-500/30">
                            2–4 Pemain
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Ular Tangga IT</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-6 flex-1">
                            Ular Tangga edukatif bertema IT! Jawab soal jaringan & coding untuk naik tangga dan hindari ular.
                        </p>
                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex items-center text-xs font-bold text-gray-400">
                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Baru
                            </div>
                            <a href="{{ route('notted.snake-ladder') }}" class="px-5 py-2 bg-emerald-50 text-emerald-700 font-bold text-sm rounded-xl border border-emerald-100 hover:bg-emerald-600 hover:text-white transition-all transform hover:scale-105">
                                Mainkan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- UNO Game -->
                <div class="group relative bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-100/50 hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="relative h-48 bg-slate-950 overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(248,113,113,.45),transparent_28%),radial-gradient(circle_at_85%_15%,rgba(250,204,21,.35),transparent_22%),radial-gradient(circle_at_55%_85%,rgba(34,197,94,.28),transparent_26%)]"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="relative h-32 w-44">
                                <div class="absolute left-2 top-5 h-24 w-16 -rotate-12 rounded-xl bg-gradient-to-br from-red-500 to-rose-700 p-1 shadow-2xl ring-2 ring-white/20 transition-transform duration-300 group-hover:-translate-y-2">
                                    <div class="flex h-full items-center justify-center rounded-lg border-2 border-white/80 text-3xl font-black text-white">7</div>
                                </div>
                                <div class="absolute left-16 top-1 h-28 w-20 rotate-3 rounded-2xl bg-gradient-to-br from-yellow-300 to-amber-500 p-1.5 shadow-2xl ring-2 ring-white/20 transition-transform duration-300 group-hover:-translate-y-4">
                                    <div class="flex h-full items-center justify-center rounded-xl border-2 border-white text-4xl font-black text-white drop-shadow">+2</div>
                                </div>
                                <div class="absolute right-2 top-6 h-24 w-16 rotate-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-700 p-1 shadow-2xl ring-2 ring-white/20 transition-transform duration-300 group-hover:-translate-y-2">
                                    <div class="flex h-full items-center justify-center rounded-lg border-2 border-white/80 text-3xl font-black text-white">R</div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-4 left-4 px-3 py-1 bg-white/15 backdrop-blur-md rounded-lg text-[10px] font-black text-white uppercase tracking-widest border border-white/20">
                            Kartu Strategi
                        </div>
                        <div class="absolute top-4 right-4 px-3 py-1 bg-rose-500/30 backdrop-blur-md rounded-lg text-[10px] font-black text-rose-100 uppercase tracking-widest border border-rose-300/30">
                            Custom Skin
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">UNO Stella</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-6 flex-1">
                            Main melawan bot atau akun lain lewat room multiplayer, gunakan kartu aksi, fullscreen, dan unggah gambar untuk skin kartu.
                        </p>
                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex items-center text-xs font-bold text-gray-400">
                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Baru
                            </div>
                            <a href="{{ route('notted.uno') }}" class="px-5 py-2 bg-rose-50 text-rose-700 font-bold text-sm rounded-xl border border-rose-100 hover:bg-rose-600 hover:text-white transition-all transform hover:scale-105">
                                Mainkan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Scrabble Game -->
                <div class="group relative bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-100/50 hover:shadow-2xl hover:shadow-cyan-500/10 transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="relative h-48 bg-gradient-to-br from-cyan-950 via-slate-900 to-emerald-950 overflow-hidden">
                        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,.08)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.08)_1px,transparent_1px)] bg-[size:28px_28px]"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="grid grid-cols-5 gap-1.5 rotate-[-8deg] transition-transform duration-300 group-hover:rotate-0 group-hover:scale-105">
                                @foreach(['S', 'C', 'R', 'A', 'B', 'B', 'L', 'E', '+', '2'] as $tile)
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-amber-200 bg-gradient-to-br from-amber-100 to-amber-300 text-lg font-black text-slate-900 shadow-lg">
                                        {{ $tile }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="absolute top-4 left-4 px-3 py-1 bg-white/15 backdrop-blur-md rounded-lg text-[10px] font-black text-white uppercase tracking-widest border border-white/20">
                            Word Battle
                        </div>
                        <div class="absolute top-4 right-4 px-3 py-1 bg-cyan-500/25 backdrop-blur-md rounded-lg text-[10px] font-black text-cyan-100 uppercase tracking-widest border border-cyan-300/30">
                            Solo / Multiplayer
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Scrabble Stella</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-6 flex-1">
                            Susun kata di papan 15x15, kumpulkan poin dari nilai huruf, main solo lawan bot atau duel akun lain lewat room.
                        </p>
                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex items-center text-xs font-bold text-gray-400">
                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Baru
                            </div>
                            <a href="{{ route('notted.scrabble') }}" class="px-5 py-2 bg-cyan-50 text-cyan-700 font-bold text-sm rounded-xl border border-cyan-100 hover:bg-cyan-600 hover:text-white transition-all transform hover:scale-105">
                                Mainkan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Coming Soon Placeholder 2 -->
                <div class="hidden lg:flex group relative bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 border-spacing-8 overflow-hidden flex-col opacity-60">
                    <div class="p-8 flex-1 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">Coming Soon</h4>
                        <p class="text-xs text-gray-400 font-medium">Coming next to broaden your horizons!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Educational CTA Section -->
        <div class="mt-20 p-8 rounded-3xl border-2 border-dashed border-indigo-200 bg-indigo-50/30 flex flex-col md:flex-row items-center gap-8">
            <div class="flex-shrink-0 w-20 h-20 bg-white rounded-2xl shadow-xl shadow-indigo-100 flex items-center justify-center">
                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div class="text-center md:text-left flex-1">
                <h3 class="text-xl font-black text-gray-900 mb-1 leading-tight">Punya Ide Game Edukatif? 💡</h3>
                <p class="text-gray-500 font-medium text-sm">Bagikan ide bahagiamu dengan tim admin! Mari kita buat belajar jadi lebih menyenangkan untuk semua orang.</p>
            </div>
            <a href="#" class="px-6 py-3 bg-white text-gray-900 border border-gray-200 font-bold rounded-xl hover:bg-gray-900 hover:text-white transition-all whitespace-nowrap">
                Hubungi Admin
            </a>
        </div>
    </div>
</x-app-layout>
