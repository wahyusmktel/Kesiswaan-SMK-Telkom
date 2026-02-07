<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Mengetik 1 Menit - NOTTED Social</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .notted-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        }

        .word {
            display: inline-block;
            margin: 0 4px;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 1.5rem;
            font-weight: 500;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .word.current {
            background: #e2e8f0;
            color: #1e293b;
        }

        .word.correct {
            color: #10b981;
        }

        .word.incorrect {
            color: #ef4444;
            text-decoration: underline;
        }

        .typing-box {
            background: white;
            border: 2px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .typing-box:focus-within {
            border-color: #6366f1;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.5);
        }
    </style>
</head>

<body class="bg-slate-50 antialiased overflow-x-hidden">

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ route('notted.app') }}" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 notted-gradient rounded-lg flex items-center justify-center font-bold text-white shadow-lg transition-transform group-hover:scale-110">N</div>
                        <span class="text-xl font-bold tracking-tighter text-slate-800">NOTTED</span>
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('notted.app') }}" class="text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors uppercase tracking-widest">Kembali ke Feed</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-12 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Test Mengetik <span class="text-indigo-600">1 Menit</span></h1>
                <p class="text-slate-500 font-medium max-w-2xl mx-auto leading-relaxed">
                    Ukur kecepatan jemarimu dalam menari di atas keyboard. Fokus pada ketepatan dan kecepatan untuk menjadi yang terbaik di NOTTED.
                </p>
            </div>

            <!-- Language & Timer Display -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex gap-2">
                    <button onclick="changeLanguage('id')" id="btn-id" class="px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border-2 border-indigo-600 bg-indigo-600 text-white">Indonesia</button>
                    <button onclick="changeLanguage('en')" id="btn-en" class="px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border-2 border-slate-200 text-slate-500 hover:border-indigo-600 hover:text-indigo-600 bg-white">English</button>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-6 py-2 bg-white rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="timer" class="text-xl font-black text-indigo-700 tabular-nums tracking-tight">01:00</span>
                    </div>
                    <button onclick="resetTest()" class="p-2.5 bg-white rounded-xl border border-slate-200 text-slate-400 hover:text-indigo-600 hover:rotate-180 transition-all duration-500 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Typing Interface -->
            <div class="bg-white rounded-[32px] p-8 shadow-xl border border-slate-200 mb-8 relative overflow-hidden">
                <!-- Background Decoration -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
                
                <!-- Word Display Container -->
                <div id="words-container" class="relative z-10 h-32 overflow-hidden mb-8 select-none leading-relaxed flex flex-wrap content-start scroll-smooth">
                    <!-- Words will be injected here -->
                </div>

                <!-- Input Field -->
                <div class="relative z-10 typing-box rounded-2xl p-2 transition-all group">
                    <input type="text" id="typing-input" autocomplete="off" 
                        class="w-full bg-slate-50 border-none rounded-xl py-5 px-6 text-2xl font-bold text-slate-800 placeholder-slate-300 focus:ring-0"
                        placeholder="Mulai mengetik di sini...">
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest opacity-0 group-focus-within:opacity-100 transition-opacity">
                        FOKUS!
                    </div>
                </div>
            </div>

            <!-- Score Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">KPM (WPM)</p>
                    <p id="stat-kpm" class="text-3xl font-black text-slate-900 leading-none">0</p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center border-b-4 border-b-indigo-500">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Akurasi</p>
                    <p id="stat-accuracy" class="text-3xl font-black text-indigo-600 leading-none">0%</p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Benar</p>
                    <p id="stat-correct" class="text-3xl font-black text-emerald-500 leading-none">0</p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Salah</p>
                    <p id="stat-wrong" class="text-3xl font-black text-rose-500 leading-none">0</p>
                </div>
            </div>

            <!-- Analysis & History Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-20 animate-in fade-in slide-in-from-bottom-6 duration-1000">
                <!-- Performance Graph -->
                <div class="bg-white p-8 rounded-[32px] shadow-xl border border-slate-200 h-full flex flex-col">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Analisis Performa</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Tren Kecepatan (KPM)</p>
                        </div>
                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-grow flex items-center justify-center">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>

                <!-- History Table -->
                <div class="bg-white p-8 rounded-[32px] shadow-xl border border-slate-200 h-full">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Riwayat Test</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">10 Percobaan Terakhir</p>
                        </div>
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                    <th class="pb-4">Waktu</th>
                                    <th class="pb-4">KPM</th>
                                    <th class="pb-4">Akurasi</th>
                                    <th class="pb-4">Lang</th>
                                </tr>
                            </thead>
                            <tbody id="history-body" class="divide-y divide-slate-50">
                                @forelse($history as $item)
                                    <tr>
                                        <td class="py-4">
                                            <p class="text-xs font-bold text-slate-900">{{ $item->created_at->format('d M') }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $item->created_at->format('H:i') }}</p>
                                        </td>
                                        <td class="py-4">
                                            <span class="text-sm font-black text-indigo-600">{{ $item->kpm }}</span>
                                        </td>
                                        <td class="py-4">
                                            <span class="text-sm font-black text-emerald-600">{{ $item->accuracy }}%</span>
                                        </td>
                                        <td class="py-4 text-center">
                                            <span class="px-2 py-0.5 bg-slate-100 text-[10px] font-bold rounded-lg uppercase text-slate-500">{{ $item->language }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400 text-xs font-medium italic">
                                            Belum ada riwayat test. Ayo mulai mengetik!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Results Modal -->
    <div id="results-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md"></div>
        <div class="relative bg-white rounded-[40px] shadow-2xl max-w-lg w-full overflow-hidden animate-in zoom-in fade-in duration-300">
            <div class="p-12 text-center">
                <div class="w-24 h-24 notted-gradient rounded-[32px] flex items-center justify-center mx-auto mb-8 shadow-xl">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-3xl font-black text-slate-900 mb-2 tracking-tight">Hasil Tes Kamu!</h2>
                <p class="text-slate-500 font-medium mb-12">Pencapaian jemarimu sungguh luar biasa.</p>

                <div class="grid grid-cols-2 gap-6 mb-12">
                    <div class="bg-slate-50 p-6 rounded-3xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kecepatan</p>
                        <p class="text-4xl font-black text-indigo-600"><span id="final-kpm">0</span> <span class="text-sm">KPM</span></p>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Ketepatan</p>
                        <p class="text-4xl font-black text-emerald-500"><span id="final-accuracy">0</span><span class="text-sm">%</span></p>
                    </div>
                </div>

                <div class="space-y-4 mb-10 text-left bg-slate-50 p-6 rounded-3xl">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Total Karakter</span>
                        <span id="final-chars" class="font-black text-slate-900">0</span>
                    </div>
                    <div class="h-px bg-slate-200"></div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Kata Benar</span>
                        <span id="final-correct" class="font-black text-emerald-600">0</span>
                    </div>
                    <div class="h-px bg-slate-200"></div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Kata Salah</span>
                        <span id="final-wrong" class="font-black text-rose-600">0</span>
                    </div>
                </div>

                <button onclick="resetTest()" class="w-full py-5 notted-gradient text-white rounded-[20px] text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-500/30 hover:scale-[1.02] active:scale-95 transition-all">
                    BAGIKAN & COBA LAGI
                </button>
            </div>
        </div>
    </div>

    <script>
        const wordsBank = {
            id: [
                "saya", "yang", "dan", "untuk", "dengan", "dalam", "tidak", "akan", "dari", "ada", "itu", "bisa", "ini", "kami", "mereka", "juga", "sudah", "oleh", "pada", "untuk", "lebih", "hari", "sebagai", "saat", "jika", "atau", "kita", "kata", "telah", "tapi", "banyak", "orang", "setelah", "satu", "semua", "harus", "masih", "dapat", "karena", "seperti", "sedang", "hanya", "lain", "kembali", "besar", "tahun", "ingin", "bukan", "secara", "lalu", "melalui", "dunia", "anak", "ke", "tempat", "kerja", "masa", "hidup", "masalah", "masyarakat"
            ],
            en: [
                "the", "be", "to", "of", "and", "a", "in", "that", "have", "it", "for", "not", "on", "with", "he", "as", "you", "do", "at", "this", "but", "his", "by", "from", "they", "we", "say", "her", "she", "or", "an", "will", "my", "one", "all", "would", "there", "their", "what", "so", "up", "out", "if", "about", "who", "get", "which", "go", "me", "when", "make", "can", "like", "time", "no", "just", "him", "know", "take", "people", "into", "year", "your", "good", "some", "could", "them", "see", "other", "than", "then", "now", "look", "only"
            ]
        };

        let currentLanguage = 'id';
        let words = [];
        let currentWordIndex = 0;
        let correctWords = 0;
        let incorrectWords = 0;
        let totalChars = 0;
        let timerSeconds = 60;
        let timerInterval = null;
        let testStarted = false;

        const container = document.getElementById('words-container');
        const input = document.getElementById('typing-input');
        const timerEl = document.getElementById('timer');

        function shuffle(array) {
            return array.sort(() => Math.random() - 0.5);
        }

        function generateWords() {
            container.innerHTML = '';
            words = [];
            const bank = wordsBank[currentLanguage];
            for (let i = 0; i < 200; i++) {
                const randomWord = bank[Math.floor(Math.random() * bank.length)];
                words.push(randomWord);
                const span = document.createElement('span');
                span.innerText = randomWord;
                span.className = 'word';
                if (i === 0) span.classList.add('current');
                container.appendChild(span);
            }
        }

        function startTimer() {
            if (timerInterval) return;
            testStarted = true;
            timerInterval = setInterval(() => {
                timerSeconds--;
                const mins = Math.floor(timerSeconds / 60);
                const secs = timerSeconds % 60;
                timerEl.innerText = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;

                if (timerSeconds === 0) {
                    finishTest();
                }
            }, 1000);
        }

        function finishTest() {
            clearInterval(timerInterval);
            testStarted = false;
            input.disabled = true;

            const totalTyped = correctWords + incorrectWords;
            const accuracy = totalTyped > 0 ? Math.round((correctWords / totalTyped) * 100) : 0;
            const kpm = correctWords; // Simplification: 1 word per minute in a 60s test

            document.getElementById('final-kpm').innerText = kpm;
            document.getElementById('final-accuracy').innerText = accuracy;
            document.getElementById('final-chars').innerText = totalChars;
            document.getElementById('final-correct').innerText = correctWords;
            document.getElementById('final-wrong').innerText = incorrectWords;

            // Save Result to Database via AJAX
            saveResult(kpm, accuracy);

            document.getElementById('results-modal').classList.remove('hidden');
        }

        async function saveResult(kpm, accuracy) {
            try {
                const response = await fetch("{{ route('notted.typing-test.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        kpm: kpm,
                        accuracy: accuracy,
                        correct_words: correctWords,
                        wrong_words: incorrectWords,
                        total_chars: totalChars,
                        language: currentLanguage
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    console.log('Result saved:', result);
                    // Optionally update history table dynamically here or just tell user to refresh
                }
            } catch (error) {
                console.error('Error saving result:', error);
            }
        }

        function resetTest() {
            clearInterval(timerInterval);
            timerInterval = null;
            timerSeconds = 60;
            currentWordIndex = 0;
            correctWords = 0;
            incorrectWords = 0;
            totalChars = 0;
            testStarted = false;
            
            input.disabled = false;
            input.value = '';
            timerEl.innerText = "01:00";
            container.scrollTop = 0;
            document.getElementById('results-modal').classList.add('hidden');
            
            updateStats();
            generateWords();
        }

        function updateStats() {
            const totalTyped = correctWords + incorrectWords;
            const accuracy = totalTyped > 0 ? Math.round((correctWords / totalTyped) * 100) : 0;
            
            document.getElementById('stat-kpm').innerText = correctWords;
            document.getElementById('stat-accuracy').innerText = accuracy + '%';
            document.getElementById('stat-correct').innerText = correctWords;
            document.getElementById('stat-wrong').innerText = incorrectWords;
        }

        function changeLanguage(lang) {
            currentLanguage = lang;
            document.getElementById('btn-id').className = lang === 'id' ? 'px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border-2 border-indigo-600 bg-indigo-600 text-white' : 'px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border-2 border-slate-200 text-slate-500 hover:border-indigo-600 hover:text-indigo-600 bg-white';
            document.getElementById('btn-en').className = lang === 'en' ? 'px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border-2 border-indigo-600 bg-indigo-600 text-white' : 'px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border-2 border-slate-200 text-slate-500 hover:border-indigo-600 hover:text-indigo-600 bg-white';
            resetTest();
        }

        input.addEventListener('input', (e) => {
            if (!testStarted && input.value.trim() !== '') {
                startTimer();
            }

            const typedValue = input.value;
            const currentWord = words[currentWordIndex];
            const wordSpans = container.querySelectorAll('.word');

            if (typedValue.endsWith(' ')) {
                const innerValue = typedValue.trim();
                
                if (innerValue === currentWord) {
                    wordSpans[currentWordIndex].classList.add('correct');
                    correctWords++;
                    totalChars += currentWord.length + 1;
                } else {
                    wordSpans[currentWordIndex].classList.add('incorrect');
                    incorrectWords++;
                }

                wordSpans[currentWordIndex].classList.remove('current');
                currentWordIndex++;
                if (wordSpans[currentWordIndex]) {
                    const nextWord = wordSpans[currentWordIndex];
                    nextWord.classList.add('current');
                    
                    // Shifting Logic: Keep current word on the first or second line
                    if (nextWord.offsetTop > 40) {
                        container.scrollTop = nextWord.offsetTop - 40;
                    }
                }

                input.value = '';
                updateStats();
            } else {
                // Tracking current typing accuracy in real-time
                if (currentWord.startsWith(typedValue.trim())) {
                    wordSpans[currentWordIndex].classList.remove('incorrect');
                } else {
                    wordSpans[currentWordIndex].classList.add('incorrect');
                }
            }
        });

        // Initialize Performance Chart
        const chartCtx = document.getElementById('performanceChart').getContext('2d');
        const chartData = @json($chartData);
        
        const labels = chartData.map((item, index) => {
            const date = new Date(item.created_at);
            return date.getHours() + ':' + date.getMinutes().toString().padStart(2, '0');
        });
        
        const dataValues = chartData.map(item => item.kpm);

        const performanceChart = new Chart(chartCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kecepatan (KPM)',
                    data: dataValues,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#a855f7',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(226, 232, 240, 0.5)'
                        },
                        ticks: {
                            font: {
                                family: 'Outfit',
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Outfit',
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });

        // Initialize
        generateWords();
    </script>
</body>

</html>
