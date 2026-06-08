<x-app-layout px="0">
    <style>
        [x-cloak] { display: none !important; }
        .uno-table {
            background:
                radial-gradient(circle at 20% 10%, rgba(244, 63, 94, .28), transparent 28%),
                radial-gradient(circle at 80% 8%, rgba(250, 204, 21, .2), transparent 24%),
                radial-gradient(circle at 50% 100%, rgba(34, 197, 94, .18), transparent 34%),
                linear-gradient(135deg, #020617 0%, #111827 52%, #0f172a 100%);
        }
        .uno-card {
            width: 92px;
            height: 136px;
            border-radius: 18px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .28);
            transform-style: preserve-3d;
        }
        .uno-card-small {
            width: 42px;
            height: 62px;
            border-radius: 10px;
        }
        .card-red { background: linear-gradient(145deg, #ef4444, #991b1b); }
        .card-yellow { background: linear-gradient(145deg, #facc15, #d97706); }
        .card-green { background: linear-gradient(145deg, #22c55e, #047857); }
        .card-blue { background: linear-gradient(145deg, #3b82f6, #1d4ed8); }
        .card-wild { background: conic-gradient(from 45deg, #ef4444, #facc15, #22c55e, #3b82f6, #ef4444); }
        .card-back {
            background:
                radial-gradient(circle at 30% 25%, rgba(255,255,255,.32), transparent 18%),
                linear-gradient(145deg, #111827, #020617);
        }
        .card-image-overlay {
            background-size: cover;
            background-position: center;
            opacity: .28;
            mix-blend-mode: screen;
        }
        .player-card {
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }
        .player-card:hover {
            transform: translateY(-14px) rotate(var(--tilt));
            box-shadow: 0 30px 70px rgba(244, 63, 94, .28);
        }
        .player-card.disabled {
            filter: grayscale(.35) brightness(.7);
            cursor: not-allowed;
        }
        .scrollbar-thin::-webkit-scrollbar { height: 8px; width: 8px; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, .45); border-radius: 999px; }
    </style>

    <div class="min-h-screen uno-table text-white" x-data="unoGame()" x-init="init()" x-ref="unoRoot">
        <header class="sticky top-0 z-40 border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <a href="{{ route('notted.games') }}" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.28em] text-rose-300">NOTTED Games</p>
                        <h1 class="text-xl font-black tracking-tight sm:text-2xl">UNO Stella</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="hidden items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-xs font-bold text-slate-300 sm:flex">
                        <span class="h-2 w-2 rounded-full" :class="currentColorClass()"></span>
                        <span x-text="'Giliran: ' + currentTurnName()"></span>
                    </div>
                    <button type="button" @click="toggleFullscreen()"
                        class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10 hover:text-white"
                        :title="isFullscreen ? 'Keluar fullscreen' : 'Mode fullscreen'">
                        <svg x-show="!isFullscreen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4" />
                        </svg>
                        <svg x-show="isFullscreen" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4v4H4M16 4v4h4M8 20v-4H4M16 20v-4h4" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <main class="mx-auto grid max-w-7xl gap-5 px-4 py-6 sm:px-6 lg:grid-cols-[1fr_320px] lg:px-8">
            <section class="space-y-5">
                <div class="flex flex-col gap-3 rounded-[28px] border border-white/10 bg-white/[0.06] p-3 shadow-2xl shadow-black/10 backdrop-blur sm:flex-row sm:items-center sm:justify-between">
                    <div class="inline-flex rounded-2xl border border-white/10 bg-slate-950/45 p-1">
                        <button type="button" @click="setMode('solo')"
                            class="rounded-xl px-4 py-2 text-xs font-black uppercase tracking-widest transition"
                            :class="mode === 'solo' ? 'bg-white text-slate-950' : 'text-slate-400 hover:text-white'">
                            Solo
                        </button>
                        <button type="button" @click="setMode('multi')"
                            class="rounded-xl px-4 py-2 text-xs font-black uppercase tracking-widest transition"
                            :class="mode === 'multi' ? 'bg-rose-500 text-white' : 'text-slate-400 hover:text-white'">
                            Multiplayer
                        </button>
                    </div>
                    <p class="text-xs font-semibold text-slate-400" x-text="mode === 'solo' ? 'Main cepat melawan bot.' : 'Buat room dan ajak akun lain join. State sinkron otomatis.'"></p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <template x-for="(opponent, index) in opponents()" :key="opponent.name + index">
                        <div class="rounded-3xl border border-white/10 bg-white/[0.06] p-4 shadow-2xl shadow-black/20 backdrop-blur">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-black" x-text="opponent.name"></p>
                                    <p class="text-xs font-semibold text-slate-400" x-text="opponentCardCount(opponent) + ' kartu tersisa'"></p>
                                </div>
                                <div class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest"
                                    :class="isOpponentTurn(opponent) ? 'bg-rose-500 text-white' : 'bg-white/10 text-slate-400'">
                                    <span x-text="isOpponentTurn(opponent) ? 'Main' : 'Menunggu'"></span>
                                </div>
                            </div>
                            <div class="flex min-h-[68px] -space-x-5 overflow-hidden">
                                <template x-for="i in Math.min(opponentCardCount(opponent), 12)" :key="i">
                                    <div class="uno-card-small card-back relative flex shrink-0 items-center justify-center border border-white/15">
                                        <div class="absolute inset-0 rounded-[10px]" x-show="cardSkin" :style="skinStyle(.5)"></div>
                                        <span class="relative text-[10px] font-black tracking-tighter text-white">UNO</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="relative overflow-hidden rounded-[32px] border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/30 backdrop-blur-xl">
                    <div class="absolute inset-x-8 top-1/2 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                    <div class="relative grid items-center gap-6 md:grid-cols-[1fr_auto_1fr]">
                        <div class="flex flex-col items-center gap-3">
                            <button type="button" @click="drawCard()" :disabled="!isPlayerTurn() || isGameOver()"
                                class="group relative uno-card card-back flex items-center justify-center border border-white/15 transition disabled:cursor-not-allowed disabled:opacity-50 hover:-translate-y-1">
                                <div class="absolute inset-0 rounded-[18px]" x-show="cardSkin" :style="skinStyle(.65)"></div>
                                <div class="relative rounded-full border-4 border-white px-3 py-6 text-center">
                                    <span class="block text-xl font-black tracking-tighter">UNO</span>
                                    <span class="block text-[9px] font-black uppercase tracking-widest text-rose-200">Deck</span>
                                </div>
                            </button>
                            <p class="text-xs font-bold text-slate-400" x-text="deckCount() + ' kartu di deck'"></p>
                        </div>

                        <div class="flex flex-col items-center gap-3">
                            <div class="rounded-full border border-white/10 bg-slate-950/50 px-4 py-2 text-xs font-black uppercase tracking-widest text-slate-300">
                                <span x-text="message"></span>
                            </div>
                            <div class="grid grid-cols-4 gap-2" x-show="choosingColor" x-cloak>
                                <template x-for="color in colors" :key="color">
                                    <button type="button" @click="chooseWildColor(color)"
                                        class="h-10 w-10 rounded-2xl border-2 border-white/80 shadow-lg transition hover:scale-110"
                                        :class="'card-' + color"></button>
                                </template>
                            </div>
                        </div>

                        <div class="flex flex-col items-center gap-3">
                            <div class="uno-card relative flex items-center justify-center overflow-hidden border-4 border-white/90 p-2" :class="cardClass(topCard())">
                                <div class="absolute inset-0" x-show="cardSkin && topCard().color !== 'wild'" :style="skinStyle(.18)"></div>
                                <div class="absolute left-3 top-3 text-lg font-black" x-text="cardLabel(topCard())"></div>
                                <div class="relative flex h-20 w-20 rotate-[-18deg] items-center justify-center rounded-full bg-white/90 text-3xl font-black text-slate-950 shadow-inner" x-text="cardLabel(topCard())"></div>
                                <div class="absolute bottom-3 right-3 rotate-180 text-lg font-black" x-text="cardLabel(topCard())"></div>
                            </div>
                            <p class="text-xs font-bold text-slate-400">Kartu buangan</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[32px] border border-white/10 bg-slate-950/55 p-4 shadow-2xl shadow-black/20">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black">Kartu Kamu</h2>
                            <p class="text-xs font-semibold text-slate-400" x-text="myCards().length + ' kartu di tangan'"></p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="callUno()" :disabled="!isPlayerTurn() || myCards().length !== 2"
                                class="rounded-2xl bg-yellow-400 px-5 py-2 text-xs font-black uppercase tracking-widest text-slate-950 transition hover:bg-yellow-300 disabled:cursor-not-allowed disabled:opacity-40">
                                UNO
                            </button>
                            <button type="button" @click="passTurn()" :disabled="!isPlayerTurn() || !turnDrawn() || isGameOver()"
                                class="rounded-2xl border border-white/10 bg-white/10 px-5 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-white/15 disabled:cursor-not-allowed disabled:opacity-40">
                                Pass
                            </button>
                            <button type="button" @click="resetActiveGame()" class="rounded-2xl bg-rose-600 px-5 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-rose-500">
                                Game Baru
                            </button>
                        </div>
                    </div>

                    <div class="scrollbar-thin flex min-h-[166px] gap-3 overflow-x-auto pb-3">
                        <template x-for="(card, index) in myCards()" :key="card.id">
                            <button type="button" @click="playCard(0, index)" :disabled="!isPlayerTurn() || !canPlay(card) || choosingColor || isGameOver()"
                                class="uno-card player-card relative flex shrink-0 items-center justify-center overflow-hidden border-4 border-white/90 p-2 disabled:pointer-events-none"
                                :class="[cardClass(card), (!isPlayerTurn() || !canPlay(card) || choosingColor || isGameOver()) ? 'disabled' : '']"
                                :style="'--tilt:' + ((index % 5) - 2) * 2 + 'deg'">
                                <div class="absolute inset-0" x-show="cardSkin && card.color !== 'wild'" :style="skinStyle(.2)"></div>
                                <div class="absolute left-3 top-3 text-lg font-black" x-text="cardLabel(card)"></div>
                                <div class="relative flex h-20 w-20 rotate-[-18deg] items-center justify-center rounded-full bg-white/90 text-3xl font-black text-slate-950 shadow-inner" x-text="cardLabel(card)"></div>
                                <div class="absolute bottom-3 right-3 rotate-180 text-lg font-black" x-text="cardLabel(card)"></div>
                            </button>
                        </template>
                    </div>
                </div>
            </section>

            <aside class="space-y-5">
                <div x-show="mode === 'multi'" x-cloak class="rounded-[28px] border border-rose-300/20 bg-rose-500/10 p-5 shadow-2xl shadow-black/20 backdrop-blur">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-rose-100">Multiplayer Room</h3>
                            <p class="text-xs font-semibold text-rose-100/70" x-text="activeRoom ? 'Room #' + activeRoom.code : 'Belum masuk room'"></p>
                        </div>
                        <button type="button" @click="loadRooms()" class="rounded-xl border border-white/10 bg-white/10 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-white/15">
                            Refresh
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="createRoom()" :disabled="loadingRoom"
                            class="rounded-2xl bg-white px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-950 transition hover:bg-rose-100 disabled:opacity-50">
                            Buat Room
                        </button>
                        <button type="button" @click="copyRoomCode()" :disabled="!activeRoom"
                            class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-white/15 disabled:opacity-40">
                            Copy Kode
                        </button>
                    </div>

                    <div class="mt-4 rounded-2xl bg-slate-950/45 p-3" x-show="activeRoom">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Pemain</p>
                        <p class="mt-1 text-sm font-bold text-white" x-text="roomPlayersText()"></p>
                    </div>

                    <div class="mt-4 space-y-2">
                        <template x-for="room in rooms" :key="room.id">
                            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-3">
                                <div class="mb-2 flex items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-black text-white" x-text="'#' + room.code"></p>
                                        <p class="text-xs font-semibold text-slate-400" x-text="(room.host?.name || '-') + ' vs ' + (room.guest?.name || 'menunggu')"></p>
                                    </div>
                                    <span class="rounded-full px-2 py-1 text-[10px] font-black uppercase"
                                        :class="room.status === 'waiting' ? 'bg-yellow-400 text-slate-950' : (room.status === 'active' ? 'bg-emerald-500 text-white' : 'bg-white/10 text-slate-300')"
                                        x-text="room.status"></span>
                                </div>
                                <button type="button" @click="joinRoom(room)" x-show="room.can_join"
                                    class="w-full rounded-xl bg-rose-600 px-3 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-rose-500">
                                    Join Room
                                </button>
                                <button type="button" @click="openRoom(room)" x-show="room.is_mine && !room.can_join"
                                    class="w-full rounded-xl border border-white/10 bg-white/10 px-3 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-white/15">
                                    Buka Room
                                </button>
                            </div>
                        </template>
                        <p x-show="rooms.length === 0" class="rounded-2xl bg-slate-950/35 p-4 text-center text-xs font-semibold text-slate-400">
                            Belum ada room. Buat room lalu minta teman login dan join.
                        </p>
                    </div>
                </div>

                <div class="rounded-[28px] border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur">
                    <h3 class="mb-4 text-sm font-black uppercase tracking-widest text-slate-300">Custom Kartu</h3>
                    <label class="group flex cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-white/15 bg-slate-950/35 p-5 text-center transition hover:border-rose-300/70 hover:bg-white/10">
                        <input type="file" accept="image/*" class="hidden" @change="uploadSkin">
                        <div class="mb-3 uno-card-small card-back relative flex items-center justify-center overflow-hidden border border-white/15">
                            <div class="absolute inset-0" x-show="cardSkin" :style="skinStyle(.8)"></div>
                            <span class="relative text-[10px] font-black text-white">UNO</span>
                        </div>
                        <span class="text-sm font-black text-white">Unggah gambar kartu</span>
                        <span class="mt-1 text-xs font-semibold text-slate-400">Gambar langsung dipakai untuk skin deck dan aksen kartu.</span>
                    </label>
                    <button type="button" @click="clearSkin()" x-show="cardSkin" x-cloak class="mt-3 w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-white/15">
                        Reset Skin
                    </button>
                </div>

                <div class="rounded-[28px] border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur">
                    <h3 class="mb-4 text-sm font-black uppercase tracking-widest text-slate-300">Status</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Arah</p>
                            <p class="text-lg font-black" x-text="activeDirection() === 1 ? 'Searah' : 'Berlawanan'"></p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Warna</p>
                            <p class="text-lg font-black capitalize" x-text="activeColor()"></p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur">
                    <h3 class="mb-4 text-sm font-black uppercase tracking-widest text-slate-300">Log Permainan</h3>
                    <div class="scrollbar-thin max-h-72 space-y-2 overflow-y-auto pr-1">
                        <template x-for="item in activeLogs()" :key="item.id">
                            <div class="rounded-2xl bg-slate-950/45 px-3 py-2 text-xs font-semibold text-slate-300" x-text="item.text"></div>
                        </template>
                    </div>
                </div>
            </aside>
        </main>
    </div>

    <script>
        function unoGame() {
            return {
                colors: ['red', 'yellow', 'green', 'blue'],
                mode: 'solo',
                deck: [],
                discard: [],
                players: [],
                currentPlayer: 0,
                direction: 1,
                currentColor: 'red',
                choosingColor: false,
                pendingWildCard: null,
                hasDrawnThisTurn: false,
                unoCalled: false,
                gameOver: false,
                message: 'Mulai',
                logs: [],
                rooms: [],
                activeRoom: null,
                mpState: null,
                poller: null,
                loadingRoom: false,
                isFullscreen: false,
                cardSkin: localStorage.getItem('notted_uno_card_skin') || '',
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',

                init() {
                    this.newGame();
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = Boolean(document.fullscreenElement);
                    });
                },

                newGame() {
                    this.deck = this.shuffle(this.buildDeck());
                    this.discard = [];
                    this.players = [
                        { id: 0, name: 'Kamu', cards: [] },
                        { id: 1, name: 'Bot Raka', cards: [] },
                        { id: 2, name: 'Bot Stella', cards: [] },
                    ];
                    this.currentPlayer = 0;
                    this.direction = 1;
                    this.choosingColor = false;
                    this.pendingWildCard = null;
                    this.hasDrawnThisTurn = false;
                    this.unoCalled = false;
                    this.gameOver = false;
                    this.logs = [];

                    for (let i = 0; i < 7; i++) {
                        this.players.forEach(player => player.cards.push(this.deck.pop()));
                    }

                    let first = this.deck.pop();
                    while (first.color === 'wild') {
                        this.deck.unshift(first);
                        first = this.deck.pop();
                    }

                    this.discard.push(first);
                    this.currentColor = first.color;
                    this.message = 'Giliran kamu';
                    this.addLog('Game baru dimulai. Cocokkan warna atau angka.');
                },

                setMode(mode) {
                    this.mode = mode;
                    this.choosingColor = false;
                    this.pendingWildCard = null;

                    if (mode === 'multi') {
                        this.loadRooms();
                        this.startPolling();
                    } else {
                        this.stopPolling();
                        this.activeRoom = null;
                        this.mpState = null;
                        this.message = 'Giliran kamu';
                    }
                },

                resetActiveGame() {
                    if (this.mode === 'multi') {
                        if (this.activeRoom) {
                            this.sendRoomAction({ action: 'restart' });
                        } else {
                            this.createRoom();
                        }
                        return;
                    }

                    this.newGame();
                },

                buildDeck() {
                    const deck = [];
                    let id = 1;
                    this.colors.forEach(color => {
                        deck.push({ id: id++, color, value: '0', type: 'number' });
                        for (let n = 1; n <= 9; n++) {
                            deck.push({ id: id++, color, value: String(n), type: 'number' });
                            deck.push({ id: id++, color, value: String(n), type: 'number' });
                        }
                        ['skip', 'reverse', 'draw2'].forEach(type => {
                            deck.push({ id: id++, color, value: type, type });
                            deck.push({ id: id++, color, value: type, type });
                        });
                    });
                    for (let i = 0; i < 4; i++) {
                        deck.push({ id: id++, color: 'wild', value: 'wild', type: 'wild' });
                        deck.push({ id: id++, color: 'wild', value: 'wild4', type: 'wild4' });
                    }
                    return deck;
                },

                shuffle(cards) {
                    const list = [...cards];
                    for (let i = list.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [list[i], list[j]] = [list[j], list[i]];
                    }
                    return list;
                },

                topCard() {
                    if (this.mode === 'multi') {
                        return this.mpState?.top_card || { color: 'red', value: '0', type: 'number' };
                    }

                    return this.discard[this.discard.length - 1] || { color: 'red', value: '0', type: 'number' };
                },

                botPlayers() {
                    return this.players.filter(player => player.id !== 0);
                },

                opponents() {
                    if (this.mode === 'multi') {
                        return (this.mpState?.players || []).filter(player => !player.is_me);
                    }

                    return this.botPlayers();
                },

                opponentCardCount(opponent) {
                    return this.mode === 'multi' ? opponent.cards_count : opponent.cards.length;
                },

                isOpponentTurn(opponent) {
                    return this.mode === 'multi'
                        ? this.mpState?.current_slot === opponent.slot
                        : this.currentPlayer === opponent.id;
                },

                myCards() {
                    if (this.mode === 'multi') {
                        return (this.mpState?.players || []).find(player => player.is_me)?.cards || [];
                    }

                    return this.players[0]?.cards || [];
                },

                deckCount() {
                    return this.mode === 'multi' ? (this.mpState?.deck_count || 0) : this.deck.length;
                },

                currentTurnName() {
                    if (this.mode === 'multi') {
                        const player = (this.mpState?.players || []).find(item => item.slot === this.mpState?.current_slot);
                        return player?.name || 'Menunggu';
                    }

                    return this.players[this.currentPlayer]?.name || 'Menunggu';
                },

                activeDirection() {
                    return this.mode === 'multi' ? (this.mpState?.direction || 1) : this.direction;
                },

                activeColor() {
                    return this.mode === 'multi' ? (this.mpState?.current_color || 'red') : this.currentColor;
                },

                activeLogs() {
                    return this.mode === 'multi' ? (this.mpState?.logs || []) : this.logs;
                },

                turnDrawn() {
                    return this.mode === 'multi' ? Boolean(this.mpState?.turn_drawn) : this.hasDrawnThisTurn;
                },

                isGameOver() {
                    return this.mode === 'multi' ? this.mpState?.status === 'finished' : this.gameOver;
                },

                isPlayerTurn() {
                    if (this.mode === 'multi') {
                        return Boolean(this.mpState?.is_my_turn) && this.mpState?.status === 'active';
                    }

                    return this.currentPlayer === 0;
                },

                canPlay(card) {
                    return card.color === 'wild' || card.color === this.activeColor() || card.value === this.topCard().value;
                },

                playCard(playerId, cardIndex) {
                    if (this.mode === 'multi') {
                        const card = this.myCards()[cardIndex];
                        if (!card || !this.canPlay(card) || !this.isPlayerTurn()) return;

                        if (card.color === 'wild') {
                            this.pendingWildCard = { card, cardIndex };
                            this.choosingColor = true;
                            this.message = 'Pilih warna';
                            return;
                        }

                        this.sendRoomAction({ action: 'play', card_id: card.id });
                        return;
                    }

                    if (this.gameOver || playerId !== this.currentPlayer) return;

                    const player = this.players[playerId];
                    const card = player.cards[cardIndex];
                    if (!this.canPlay(card)) return;

                    player.cards.splice(cardIndex, 1);
                    this.discard.push(card);
                    this.currentColor = card.color === 'wild' ? this.currentColor : card.color;
                    this.hasDrawnThisTurn = false;
                    this.addLog(player.name + ' memainkan ' + this.cardText(card) + '.');

                    if (playerId === 0 && player.cards.length === 1 && !this.unoCalled) {
                        this.drawToPlayer(0, 2);
                        this.addLog('Kamu lupa tekan UNO. Penalti 2 kartu.');
                    }

                    this.unoCalled = false;

                    if (player.cards.length === 0) {
                        this.finishGame(player.name);
                        return;
                    }

                    if (card.color === 'wild' && playerId === 0) {
                        this.pendingWildCard = card;
                        this.choosingColor = true;
                        this.message = 'Pilih warna';
                        return;
                    }

                    if (card.color === 'wild') {
                        this.currentColor = this.bestBotColor(player);
                        this.addLog(player.name + ' memilih warna ' + this.currentColor + '.');
                    }

                    this.applyCardEffect(card);
                },

                chooseWildColor(color) {
                    if (this.mode === 'multi') {
                        if (!this.pendingWildCard?.card) return;

                        this.choosingColor = false;
                        const card = this.pendingWildCard.card;
                        this.pendingWildCard = null;
                        this.sendRoomAction({ action: 'play', card_id: card.id, color });
                        return;
                    }

                    if (!this.pendingWildCard) return;

                    this.currentColor = color;
                    this.choosingColor = false;
                    const card = this.pendingWildCard;
                    this.pendingWildCard = null;
                    this.addLog('Kamu memilih warna ' + color + '.');
                    this.applyCardEffect(card);
                },

                applyCardEffect(card) {
                    if (card.type === 'reverse') {
                        this.direction *= -1;
                        this.addLog('Arah permainan dibalik.');
                    }

                    if (card.type === 'skip') {
                        const skipped = this.nextPlayerIndex();
                        this.addLog(this.players[skipped].name + ' dilewati.');
                        this.currentPlayer = this.nextPlayerIndex(skipped);
                    } else if (card.type === 'draw2') {
                        const target = this.nextPlayerIndex();
                        this.drawToPlayer(target, 2);
                        this.addLog(this.players[target].name + ' mengambil 2 kartu.');
                        this.currentPlayer = this.nextPlayerIndex(target);
                    } else if (card.type === 'wild4') {
                        const target = this.nextPlayerIndex();
                        this.drawToPlayer(target, 4);
                        this.addLog(this.players[target].name + ' mengambil 4 kartu.');
                        this.currentPlayer = this.nextPlayerIndex(target);
                    } else {
                        this.currentPlayer = this.nextPlayerIndex();
                    }

                    this.afterTurn();
                },

                afterTurn() {
                    this.hasDrawnThisTurn = false;
                    this.message = 'Giliran ' + this.players[this.currentPlayer].name;

                    if (this.currentPlayer !== 0 && !this.gameOver) {
                        window.setTimeout(() => this.botTurn(), 850);
                    }
                },

                botTurn() {
                    if (this.gameOver || this.currentPlayer === 0) return;

                    const player = this.players[this.currentPlayer];
                    const playableIndex = player.cards.findIndex(card => this.canPlay(card));

                    if (playableIndex >= 0) {
                        this.playCard(player.id, playableIndex);
                        return;
                    }

                    this.drawToPlayer(player.id, 1);
                    const drawnCard = player.cards[player.cards.length - 1];
                    this.addLog(player.name + ' mengambil kartu.');

                    if (this.canPlay(drawnCard)) {
                        window.setTimeout(() => this.playCard(player.id, player.cards.length - 1), 500);
                    } else {
                        this.currentPlayer = this.nextPlayerIndex();
                        this.afterTurn();
                    }
                },

                drawCard() {
                    if (this.mode === 'multi') {
                        if (!this.isPlayerTurn() || this.isGameOver() || this.choosingColor) return;
                        this.sendRoomAction({ action: 'draw' });
                        return;
                    }

                    if (!this.isPlayerTurn() || this.gameOver || this.choosingColor) return;

                    this.drawToPlayer(0, 1);
                    this.hasDrawnThisTurn = true;
                    this.addLog('Kamu mengambil 1 kartu.');

                    const drawnCard = this.players[0].cards[this.players[0].cards.length - 1];
                    this.message = this.canPlay(drawnCard) ? 'Kartu baru bisa dimainkan' : 'Tidak cocok, tekan Pass';
                },

                passTurn() {
                    if (this.mode === 'multi') {
                        if (!this.isPlayerTurn() || !this.turnDrawn() || this.isGameOver()) return;
                        this.sendRoomAction({ action: 'pass' });
                        return;
                    }

                    if (!this.isPlayerTurn() || !this.hasDrawnThisTurn || this.gameOver) return;

                    this.currentPlayer = this.nextPlayerIndex();
                    this.afterTurn();
                },

                drawToPlayer(playerId, amount) {
                    for (let i = 0; i < amount; i++) {
                        if (this.deck.length === 0) this.recycleDeck();
                        if (this.deck.length > 0) this.players[playerId].cards.push(this.deck.pop());
                    }
                },

                recycleDeck() {
                    const top = this.discard.pop();
                    this.deck = this.shuffle(this.discard);
                    this.discard = [top];
                },

                nextPlayerIndex(from = this.currentPlayer) {
                    return (from + this.direction + this.players.length) % this.players.length;
                },

                bestBotColor(player) {
                    const counts = { red: 0, yellow: 0, green: 0, blue: 0 };
                    player.cards.forEach(card => {
                        if (counts[card.color] !== undefined) counts[card.color]++;
                    });
                    return Object.entries(counts).sort((a, b) => b[1] - a[1])[0][0];
                },

                callUno() {
                    if (this.mode === 'multi') {
                        if (this.isPlayerTurn() && this.myCards().length === 2) {
                            this.sendRoomAction({ action: 'uno' });
                        }
                        return;
                    }

                    if (this.isPlayerTurn() && this.players[0].cards.length === 2) {
                        this.unoCalled = true;
                        this.addLog('Kamu siap UNO.');
                        this.message = 'UNO aktif';
                    }
                },

                finishGame(name) {
                    this.gameOver = true;
                    this.message = name + ' menang';
                    this.addLog(name + ' memenangkan permainan.');
                },

                addLog(text) {
                    this.logs.unshift({ id: Date.now() + Math.random(), text });
                    this.logs = this.logs.slice(0, 24);
                },

                cardClass(card) {
                    if (!card) return 'card-red';
                    return card.color === 'wild' ? 'card-wild' : 'card-' + card.color;
                },

                cardLabel(card) {
                    if (!card) return '';
                    return {
                        skip: 'S',
                        reverse: 'R',
                        draw2: '+2',
                        wild: 'W',
                        wild4: '+4',
                    }[card.value] || card.value;
                },

                cardText(card) {
                    return (card.color === 'wild' ? 'wild' : card.color) + ' ' + this.cardLabel(card);
                },

                currentColorClass() {
                    return 'card-' + this.activeColor();
                },

                async toggleFullscreen() {
                    try {
                        if (!document.fullscreenElement) {
                            await this.$refs.unoRoot.requestFullscreen();
                        } else {
                            await document.exitFullscreen();
                        }
                    } catch (error) {
                        this.addLog('Browser tidak mengizinkan fullscreen.');
                    }
                },

                async loadRooms() {
                    if (this.mode !== 'multi') return;

                    try {
                        const data = await this.requestJson('/notted/uno/rooms');
                        this.rooms = data.rooms || [];
                    } catch (error) {
                        this.addLog(error.message || 'Gagal memuat room.');
                    }
                },

                async createRoom() {
                    this.loadingRoom = true;

                    try {
                        const data = await this.requestJson('/notted/uno/rooms', { method: 'POST' });
                        this.applyRoomPayload(data);
                        await this.loadRooms();
                        this.startPolling();
                    } catch (error) {
                        this.addLog(error.message || 'Gagal membuat room.');
                    } finally {
                        this.loadingRoom = false;
                    }
                },

                async joinRoom(room) {
                    try {
                        const data = await this.requestJson(`/notted/uno/rooms/${room.id}/join`, { method: 'POST' });
                        this.applyRoomPayload(data);
                        await this.loadRooms();
                        this.startPolling();
                    } catch (error) {
                        this.addLog(error.message || 'Gagal join room.');
                    }
                },

                async openRoom(room) {
                    this.activeRoom = room;
                    await this.loadRoomState();
                    this.startPolling();
                },

                async loadRoomState() {
                    if (!this.activeRoom) return;

                    try {
                        const data = await this.requestJson(`/notted/uno/rooms/${this.activeRoom.id}/state`);
                        this.applyRoomPayload(data);
                    } catch (error) {
                        this.addLog(error.message || 'Gagal sinkron room.');
                    }
                },

                async sendRoomAction(payload) {
                    if (!this.activeRoom) {
                        this.addLog('Masuk room dulu untuk multiplayer.');
                        return;
                    }

                    try {
                        const data = await this.requestJson(`/notted/uno/rooms/${this.activeRoom.id}/action`, {
                            method: 'POST',
                            body: JSON.stringify(payload),
                        });
                        this.applyRoomPayload(data);
                        await this.loadRooms();
                    } catch (error) {
                        this.addLog(error.message || 'Aksi tidak bisa diproses.');
                    }
                },

                applyRoomPayload(data) {
                    this.activeRoom = data.room || this.activeRoom;
                    this.mpState = data.state || this.mpState;
                    this.message = this.mpState?.message || this.activeRoom?.status || 'Multiplayer';
                },

                startPolling() {
                    this.stopPolling();
                    this.poller = window.setInterval(() => {
                        if (this.mode === 'multi') {
                            this.loadRooms();
                            if (this.activeRoom) this.loadRoomState();
                        }
                    }, 2500);
                },

                stopPolling() {
                    if (this.poller) {
                        window.clearInterval(this.poller);
                        this.poller = null;
                    }
                },

                async requestJson(url, options = {}) {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            ...(options.headers || {}),
                        },
                        ...options,
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(data.message || 'Request gagal.');
                    }

                    return data;
                },

                roomPlayersText() {
                    if (!this.activeRoom) return '-';

                    return (this.activeRoom.host?.name || '-') + ' vs ' + (this.activeRoom.guest?.name || 'menunggu pemain');
                },

                async copyRoomCode() {
                    if (!this.activeRoom) return;

                    try {
                        await navigator.clipboard.writeText(this.activeRoom.code);
                        this.addLog('Kode room disalin: ' + this.activeRoom.code);
                    } catch (error) {
                        this.addLog('Kode room: ' + this.activeRoom.code);
                    }
                },

                uploadSkin(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = () => {
                        this.cardSkin = reader.result;
                        localStorage.setItem('notted_uno_card_skin', this.cardSkin);
                        this.addLog('Skin kartu berhasil dipasang.');
                    };
                    reader.readAsDataURL(file);
                },

                clearSkin() {
                    this.cardSkin = '';
                    localStorage.removeItem('notted_uno_card_skin');
                    this.addLog('Skin kartu direset.');
                },

                skinStyle(opacity = .3) {
                    return `background-image:url('${this.cardSkin}');background-size:cover;background-position:center;opacity:${opacity};`;
                },
            };
        }
    </script>
</x-app-layout>
