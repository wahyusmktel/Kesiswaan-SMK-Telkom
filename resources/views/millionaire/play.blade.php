<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Who Wants to Be a Millionaire - {{ $set->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #000033; color: white; overflow: hidden; }
        .millionaire-bg {
            background: radial-gradient(circle at center, #000066 0%, #000033 100%);
            min-height: 100vh;
        }
        .option-btn {
            background: linear-gradient(to right, #000066, #0000cc, #000066);
            border: 2px solid #5555ff;
            clip-path: polygon(5% 0%, 95% 0%, 100% 50%, 95% 100%, 5% 100%, 0% 50%);
            transition: all 0.3s;
        }
        .option-btn:hover:not(:disabled) {
            background: linear-gradient(to right, #000099, #0000ff, #000099);
            border-color: #ffffff;
            transform: scale(1.02);
        }
        .option-btn.selected { background: #ffaa00 !important; color: black; border-color: white; }
        .option-btn.correct { background: #00ff00 !important; color: black; border-color: white; animation: pulse 0.5s infinite; }
        .option-btn.wrong { background: #ff0000 !important; color: white; border-color: white; }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .level-item { transition: all 0.3s; }
        .level-item.active { background: #ffaa00; color: black; font-weight: 800; transform: scale(1.1); }
        .level-item.milestone { color: white; }
        .level-item.reached { color: #aaaaaa; }
        
        .lifeline-btn {
            width: 50px; height: 35px; border-radius: 50%; border: 2px solid #5555ff;
            background: #000066; display: flex; items-center; justify-center;
            font-size: 10px; font-weight: 800; cursor: pointer; transition: all 0.3s;
        }
        .lifeline-btn:hover:not(.used) { background: #0000cc; border-color: white; }
        .lifeline-btn.used { opacity: 0.3; cursor: not-allowed; border-color: #ff0000; text-decoration: line-through; }
    </style>
</head>
<body class="millionaire-bg">
    <div x-data="millionaireGame()" class="flex flex-col h-screen p-4 md:p-8">
        <!-- Top Section: Lifelines & Logo -->
        <div class="flex justify-between items-start mb-8">
            <div class="flex gap-4">
                <button @click="use5050()" :class="{'lifeline-btn': true, 'used': lifelines.fifty50}" :disabled="lifelines.fifty50">50:50</button>
                <button @click="usePhone()" :class="{'lifeline-btn': true, 'used': lifelines.phone}" :disabled="lifelines.phone">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 004.87 4.87l.773-1.548a1 1 0 011.06-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                </button>
                <button @click="useAudience()" :class="{'lifeline-btn': true, 'used': lifelines.audience}" :disabled="lifelines.audience">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                </button>
            </div>

            <div class="hidden md:flex flex-col items-center">
                <div class="w-24 h-24 rounded-full border-4 border-amber-400 flex items-center justify-center bg-blue-900 shadow-2xl shadow-blue-500/50 mb-2">
                    <span class="text-xs font-black text-amber-400 text-center tracking-tighter">WHO WANTS<br>TO BE A<br>MILLIONAIRE</span>
                </div>
                <!-- <h1 class="text-sm font-bold uppercase tracking-widest">{{ $set->name }}</h1> -->
            </div>

            <div class="flex flex-col items-end">
                <a href="{{ route('notted.millionaire.index') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-white transition-colors">Menyerah & Keluar</a>
            </div>
        </div>

        <div class="flex-1 flex flex-col items-center justify-center max-w-6xl mx-auto w-full">
            <!-- Loading State -->
            <template x-if="loading">
                <div class="text-center animate-pulse">
                    <div class="w-16 h-16 border-4 border-amber-400 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="font-bold tracking-widest uppercase text-xs">Menyiapkan Pertanyaan...</p>
                </div>
            </template>

            <!-- Game Board -->
            <template x-if="!loading && !gameOver">
                <div class="w-full grid grid-cols-1 lg:grid-cols-4 gap-8 items-center">
                    <!-- Questions & Options -->
                    <div class="lg:col-span-3">
                        <!-- Question -->
                        <div class="mb-12 relative">
                            <div class="bg-gradient-to-r from-transparent via-blue-900 to-transparent p-1">
                                <div class="bg-black/40 border-y-2 border-blue-500 py-6 px-12 text-center">
                                    <h2 class="text-xl md:text-2xl font-bold leading-relaxed" x-text="currentQuestion.question"></h2>
                                </div>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 px-4">
                            <template x-for="(opt, idx) in options" :key="idx">
                                <button 
                                    @click="selectOption(opt.key)" 
                                    :disabled="isChecking || opt.hidden"
                                    :class="{
                                        'option-btn w-full py-4 px-8 text-left flex items-center gap-4': true,
                                        'selected': selectedOption === opt.key,
                                        'correct': showResult && opt.key === currentQuestion.correct_answer,
                                        'wrong': showResult && selectedOption === opt.key && selectedOption !== currentQuestion.correct_answer,
                                        'invisible': opt.hidden
                                    }"
                                >
                                    <span class="text-amber-400 font-black" x-text="opt.key + ':'"></span>
                                    <span class="font-bold" x-text="opt.text"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Prize Ladder -->
                    <div class="hidden lg:block bg-black/40 rounded-3xl p-6 border border-blue-900/50">
                        <div class="flex flex-col-reverse gap-1">
                            <template x-for="lvl in [15,14,13,12,11,10,9,8,7,6,5,4,3,2,1]" :key="lvl">
                                <div :class="{
                                    'level-item px-4 py-1.5 rounded-lg flex justify-between text-[11px]': true,
                                    'active': currentLevel === lvl,
                                    'milestone': lvl % 5 === 0,
                                    'reached': currentLevel > lvl
                                }">
                                    <span x-text="lvl"></span>
                                    <span class="font-black" x-text="formatMoney(prizes[lvl-1])"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Game Over / Victory screen -->
            <template x-if="gameOver">
                <div class="text-center animate-in zoom-in duration-500">
                    <div class="w-32 h-32 notted-gradient rounded-full flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-indigo-500/50">
                        <svg x-show="won" class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <svg x-show="!won" class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </div>
                    <h2 class="text-4xl font-black mb-4 tracking-tighter uppercase" x-text="won ? 'Kamu Menang!' : 'Game Over!'"></h2>
                    <p class="text-xl mb-8">Anda berhasil mendapatkan:</p>
                    <div class="text-5xl font-black text-amber-400 mb-12 tracking-tighter" x-text="formatMoney(winnings)"></div>
                    
                    <div class="flex gap-4 justify-center">
                        <button @click="restartGame()" class="px-8 py-4 bg-white text-blue-900 rounded-2xl font-black uppercase tracking-widest hover:bg-blue-50 transition-all">Main Lagi</button>
                        <a href="{{ route('notted.millionaire.index') }}" class="px-8 py-4 border-2 border-white rounded-2xl font-black uppercase tracking-widest hover:bg-white/10 transition-all">Kembali</a>
                    </div>
                </div>
            </template>
        </div>

        <!-- Background Sound / Music Placeholders could be added here -->
    </div>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function millionaireGame() {
            return {
                loading: true,
                gameOver: false,
                won: false,
                questions: [],
                currentQuestion: null,
                currentLevel: 1,
                selectedOption: null,
                showResult: false,
                isChecking: false,
                winnings: 0,
                lifelines: { fifty50: false, phone: false, audience: false },
                options: [],
                prizes: [
                    500, 1000, 2000, 3000, 5000, 
                    10000, 20000, 40000, 80000, 160000,
                    320000, 640000, 1250000, 2500000, 5000000
                ],

                async init() {
                    try {
                        const response = await fetch(`/notted/millionaire/questions/{{ $set->id }}`);
                        this.questions = await response.json();
                        if (this.questions.length === 0) {
                            alert('Belum ada soal di kategori ini.');
                            window.location.href = "{{ route('notted.millionaire.index') }}";
                            return;
                        }
                        this.loadQuestion();
                    } catch (error) {
                        console.error('Failed to load questions:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                loadQuestion() {
                    const qData = this.questions.find(q => q.level === this.currentLevel);
                    if (!qData) {
                        this.victory();
                        return;
                    }

                    this.currentQuestion = qData;
                    this.options = [
                        { key: 'A', text: qData.option_a, hidden: false },
                        { key: 'B', text: qData.option_b, hidden: false },
                        { key: 'C', text: qData.option_c, hidden: false },
                        { key: 'D', text: qData.option_d, hidden: false }
                    ];
                    this.selectedOption = null;
                    this.showResult = false;
                    this.isChecking = false;
                },

                selectOption(key) {
                    if (this.isChecking) return;
                    this.selectedOption = key;
                    this.isChecking = true;

                    // Delay for suspense
                    setTimeout(() => {
                        this.showResult = true;
                        if (key === this.currentQuestion.correct_answer) {
                            setTimeout(() => {
                                if (this.currentLevel === 15) {
                                    this.victory();
                                } else {
                                    this.currentLevel++;
                                    this.loadQuestion();
                                }
                            }, 1500);
                        } else {
                            setTimeout(() => {
                                this.endGame();
                            }, 1500);
                        }
                    }, 2000);
                },

                use5050() {
                    if (this.lifelines.fifty50 || this.isChecking) return;
                    this.lifelines.fifty50 = true;
                    
                    const correct = this.currentQuestion.correct_answer;
                    const incorrects = this.options.filter(o => o.key !== correct);
                    const toHide = this.shuffle(incorrects).slice(0, 2);
                    
                    this.options.forEach(o => {
                        if (toHide.find(h => h.key === o.key)) o.hidden = true;
                    });
                },

                usePhone() {
                    if (this.lifelines.phone || this.isChecking) return;
                    this.lifelines.phone = true;
                    const ans = this.currentQuestion.correct_answer;
                    alert(`Sahabat Anda berpikir jawabannya adalah ${ans}, tapi dia tidak 100% yakin.`);
                },

                useAudience() {
                    if (this.lifelines.audience || this.isChecking) return;
                    this.lifelines.audience = true;
                    const ans = this.currentQuestion.correct_answer;
                    alert(`85% Penonton memilih ${ans}.`);
                },

                shuffle(array) {
                    return array.sort(() => Math.random() - 0.5);
                },

                formatMoney(amount) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);
                },

                victory() {
                    this.winnings = this.prizes[14];
                    this.won = true;
                    this.gameOver = true;
                },

                endGame() {
                    let milestoneLevel = 0;
                    if (this.currentLevel > 10) milestoneLevel = 10;
                    else if (this.currentLevel > 5) milestoneLevel = 5;
                    
                    this.winnings = milestoneLevel > 0 ? this.prizes[milestoneLevel-1] : 0;
                    this.won = false;
                    this.gameOver = true;
                },

                restartGame() {
                    this.currentLevel = 1;
                    this.lifelines = { fifty50: false, phone: false, audience: false };
                    this.gameOver = false;
                    this.won = false;
                    this.loadQuestion();
                }
            }
        }
    </script>
</body>
</html>
