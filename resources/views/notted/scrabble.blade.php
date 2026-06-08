<x-app-layout px="0">
    <style>
        [x-cloak] { display: none !important; }
        .scrabble-bg {
            background:
                radial-gradient(circle at 16% 12%, rgba(6, 182, 212, .25), transparent 30%),
                radial-gradient(circle at 82% 20%, rgba(16, 185, 129, .2), transparent 28%),
                linear-gradient(135deg, #020617 0%, #0f172a 52%, #06202a 100%);
        }
        .scrabble-cell {
            width: clamp(22px, 5.8vw, 42px);
            height: clamp(22px, 5.8vw, 42px);
        }
        .scrabble-tile {
            background: linear-gradient(145deg, #fef3c7, #fbbf24);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.65), 0 12px 24px rgba(15, 23, 42, .28);
        }
        .scrollbar-thin::-webkit-scrollbar { height: 8px; width: 8px; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, .45); border-radius: 999px; }
    </style>

    <div class="min-h-screen scrabble-bg text-white" x-data="scrabbleGame()" x-init="init()" x-ref="root">
        <header class="sticky top-0 z-40 border-b border-white/10 bg-slate-950/75 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <a href="{{ route('notted.games') }}" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.28em] text-cyan-300">NOTTED Games</p>
                        <h1 class="text-xl font-black tracking-tight sm:text-2xl">Scrabble Stella</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="hidden rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-xs font-bold text-slate-300 sm:block">
                        <span x-text="'Giliran: ' + currentTurnName()"></span>
                    </div>
                    <button type="button" @click="toggleFullscreen()" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10 hover:text-white">
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

        <main class="mx-auto grid max-w-7xl gap-5 px-4 py-6 sm:px-6 lg:grid-cols-[1fr_330px] lg:px-8">
            <section class="space-y-5">
                <div class="flex flex-col gap-3 rounded-[28px] border border-white/10 bg-white/[0.06] p-3 shadow-2xl shadow-black/10 backdrop-blur sm:flex-row sm:items-center sm:justify-between">
                    <div class="inline-flex rounded-2xl border border-white/10 bg-slate-950/45 p-1">
                        <button type="button" @click="setMode('solo')" class="rounded-xl px-4 py-2 text-xs font-black uppercase tracking-widest transition" :class="mode === 'solo' ? 'bg-white text-slate-950' : 'text-slate-400 hover:text-white'">Solo</button>
                        <button type="button" @click="setMode('multi')" class="rounded-xl px-4 py-2 text-xs font-black uppercase tracking-widest transition" :class="mode === 'multi' ? 'bg-cyan-500 text-white' : 'text-slate-400 hover:text-white'">Multiplayer</button>
                    </div>
                    <p class="text-xs font-semibold text-slate-400" x-text="mode === 'solo' ? 'Susun kata melawan bot Stella.' : 'Buat room, tunggu akun lain join, lalu main bergantian.'"></p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <template x-for="player in activePlayers()" :key="player.name + player.slot">
                        <div class="rounded-3xl border border-white/10 bg-white/[0.06] p-4 backdrop-blur" :class="isCurrentPlayer(player) ? 'ring-2 ring-cyan-300/70' : ''">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-black" x-text="player.name"></p>
                                    <p class="text-xs font-semibold text-slate-400" x-text="playerRackCount(player) + ' tile tersisa'"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Skor</p>
                                    <p class="text-2xl font-black text-cyan-200" x-text="player.score"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="overflow-hidden rounded-[32px] border border-white/10 bg-slate-950/55 p-3 shadow-2xl shadow-black/30 sm:p-5">
                    <div class="scrollbar-thin overflow-auto pb-2">
                        <div class="grid w-max grid-cols-15 gap-1" style="grid-template-columns: repeat(15, minmax(22px, 42px));">
                            <template x-for="row in 15" :key="'r' + row">
                                <template x-for="col in 15" :key="(row - 1) + '-' + (col - 1)">
                                    <button type="button" @click="placeSelected(row - 1, col - 1)"
                                        class="scrabble-cell relative flex items-center justify-center rounded-lg border text-[10px] font-black transition"
                                        :class="cellClass(row - 1, col - 1)">
                                        <template x-if="cellTile(row - 1, col - 1)">
                                            <div class="scrabble-tile absolute inset-0 flex items-center justify-center rounded-lg border border-amber-200 text-slate-900">
                                                <span class="text-base font-black sm:text-lg" x-text="cellTile(row - 1, col - 1).letter"></span>
                                                <span class="absolute bottom-0.5 right-1 text-[8px]" x-text="cellTile(row - 1, col - 1).score"></span>
                                            </div>
                                        </template>
                                        <span x-show="!cellTile(row - 1, col - 1)" class="text-slate-500" x-text="premiumLabel(row - 1, col - 1)"></span>
                                    </button>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="rounded-[32px] border border-white/10 bg-white/[0.07] p-4 shadow-2xl shadow-black/20 backdrop-blur">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black">Rack Kamu</h2>
                            <p class="text-xs font-semibold text-slate-400" x-text="messageText()"></p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="submitWord()" :disabled="!isMyTurn() || placements.length === 0" class="rounded-2xl bg-cyan-500 px-5 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-cyan-400 disabled:cursor-not-allowed disabled:opacity-40">Submit</button>
                            <button type="button" @click="undoPlacement()" :disabled="placements.length === 0" class="rounded-2xl border border-white/10 bg-white/10 px-5 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-white/15 disabled:opacity-40">Undo</button>
                            <button type="button" @click="swapSelected()" :disabled="!isMyTurn() || selectedSwap.length === 0" class="rounded-2xl border border-white/10 bg-white/10 px-5 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-white/15 disabled:opacity-40">Tukar</button>
                            <button type="button" @click="passTurn()" :disabled="!isMyTurn()" class="rounded-2xl border border-white/10 bg-white/10 px-5 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-white/15 disabled:opacity-40">Pass</button>
                            <button type="button" @click="resetGame()" class="rounded-2xl bg-emerald-600 px-5 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-emerald-500">Game Baru</button>
                        </div>
                    </div>
                    <div class="flex min-h-[76px] flex-wrap gap-3">
                        <template x-for="tile in myRack()" :key="tile.id">
                            <button type="button" @click="selectTile(tile)" @dblclick="toggleSwap(tile)"
                                class="scrabble-tile relative flex h-16 w-14 items-center justify-center rounded-2xl border border-amber-200 text-slate-900 transition hover:-translate-y-1"
                                :class="selectedTile?.id === tile.id ? 'ring-4 ring-cyan-300' : (selectedSwap.includes(tile.id) ? 'ring-4 ring-rose-300' : '')">
                                <span class="text-2xl font-black" x-text="tile.letter"></span>
                                <span class="absolute bottom-1 right-2 text-[10px] font-black" x-text="tile.score"></span>
                            </button>
                        </template>
                    </div>
                    <p class="mt-3 text-xs font-semibold text-slate-500">Klik tile lalu klik papan untuk menaruh. Double click tile untuk memilih tukar.</p>
                </div>
            </section>

            <aside class="space-y-5">
                <div x-show="mode === 'multi'" x-cloak class="rounded-[28px] border border-cyan-300/20 bg-cyan-500/10 p-5 shadow-2xl shadow-black/20 backdrop-blur">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-cyan-100">Room Scrabble</h3>
                            <p class="text-xs font-semibold text-cyan-100/70" x-text="activeRoom ? 'Room #' + activeRoom.code : 'Belum masuk room'"></p>
                        </div>
                        <button type="button" @click="loadRooms()" class="rounded-xl border border-white/10 bg-white/10 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-white/15">Refresh</button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="createRoom()" :disabled="loadingRoom" class="rounded-2xl bg-white px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-950 transition hover:bg-cyan-100 disabled:opacity-50">Buat Room</button>
                        <button type="button" @click="copyRoomCode()" :disabled="!activeRoom" class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-white/15 disabled:opacity-40">Copy Kode</button>
                    </div>
                    <div class="mt-4 space-y-2">
                        <template x-for="room in rooms" :key="room.id">
                            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-3">
                                <div class="mb-2 flex items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-black text-white" x-text="'#' + room.code"></p>
                                        <p class="text-xs font-semibold text-slate-400" x-text="(room.host?.name || '-') + ' vs ' + (room.guest?.name || 'menunggu')"></p>
                                    </div>
                                    <span class="rounded-full px-2 py-1 text-[10px] font-black uppercase" :class="room.status === 'waiting' ? 'bg-yellow-400 text-slate-950' : (room.status === 'active' ? 'bg-emerald-500 text-white' : 'bg-white/10 text-slate-300')" x-text="room.status"></span>
                                </div>
                                <button type="button" @click="joinRoom(room)" x-show="room.can_join" class="w-full rounded-xl bg-cyan-600 px-3 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-cyan-500">Join Room</button>
                                <button type="button" @click="openRoom(room)" x-show="room.is_mine && !room.can_join" class="w-full rounded-xl border border-white/10 bg-white/10 px-3 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-white/15">Buka Room</button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="rounded-[28px] border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur">
                    <h3 class="mb-4 text-sm font-black uppercase tracking-widest text-slate-300">Status</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Bag</p>
                            <p class="text-2xl font-black" x-text="bagCount()"></p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/45 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Turn</p>
                            <p class="text-2xl font-black" x-text="turnNumber()"></p>
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
        function scrabbleGame() {
            return {
                mode: 'solo',
                scores: { A:1,I:1,N:1,R:1,S:1,T:1,U:1,D:2,G:2,K:2,L:2,M:2,B:3,H:3,O:3,P:3,C:4,E:4,J:4,W:4,F:5,V:5,Y:5,Z:8,Q:10,X:10 },
                board: [],
                bag: [],
                players: [],
                currentSlot: 0,
                turn: 1,
                selectedTile: null,
                selectedSwap: [],
                placements: [],
                logs: [],
                message: 'Mulai',
                activeRoom: null,
                rooms: [],
                mpState: null,
                poller: null,
                loadingRoom: false,
                isFullscreen: false,
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',

                init() {
                    this.newSoloGame();
                    document.addEventListener('fullscreenchange', () => this.isFullscreen = Boolean(document.fullscreenElement));
                },

                setMode(mode) {
                    this.mode = mode;
                    this.clearDraft();
                    if (mode === 'multi') {
                        this.loadRooms();
                        this.startPolling();
                    } else {
                        this.stopPolling();
                        this.activeRoom = null;
                        this.mpState = null;
                        this.newSoloGame();
                    }
                },

                newSoloGame() {
                    this.board = Array.from({ length: 15 }, () => Array(15).fill(null));
                    this.bag = this.shuffle(this.buildBag());
                    this.players = [
                        { slot: 0, name: 'Kamu', rack: [], score: 0 },
                        { slot: 1, name: 'Bot Stella', rack: [], score: 0 },
                    ];
                    this.players.forEach(player => player.rack = this.draw([], 7));
                    this.currentSlot = 0;
                    this.turn = 1;
                    this.logs = [];
                    this.message = 'Kata pertama wajib melewati tengah.';
                    this.addLog('Game Scrabble solo dimulai.');
                },

                buildBag() {
                    const dist = { A:9,I:9,N:6,R:6,S:6,T:6,U:6,D:4,G:4,K:4,L:4,M:4,B:3,H:3,O:3,P:3,C:2,E:2,J:2,W:2,F:1,V:1,Y:1,Z:1,Q:1,X:1 };
                    let id = 1;
                    return Object.entries(dist).flatMap(([letter, amount]) => Array.from({ length: amount }, () => ({ id: 'l' + id++, letter, score: this.scores[letter] })));
                },

                shuffle(items) {
                    const list = [...items];
                    for (let i = list.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [list[i], list[j]] = [list[j], list[i]];
                    }
                    return list;
                },

                draw(rack, amount) {
                    const nextRack = [...rack];
                    for (let i = 0; i < amount && this.bag.length > 0; i++) nextRack.push(this.bag.pop());
                    return nextRack;
                },

                activeBoard() {
                    return this.mode === 'multi' ? (this.mpState?.board || this.emptyBoard()) : this.board;
                },

                emptyBoard() {
                    return Array.from({ length: 15 }, () => Array(15).fill(null));
                },

                activePlayers() {
                    return this.mode === 'multi' ? (this.mpState?.players || []) : this.players;
                },

                myRack() {
                    if (this.mode === 'multi') return (this.mpState?.players || []).find(player => player.is_me)?.rack || [];
                    return this.players[0].rack;
                },

                playerRackCount(player) {
                    return this.mode === 'multi' ? player.rack_count : player.rack.length;
                },

                isCurrentPlayer(player) {
                    return this.mode === 'multi' ? this.mpState?.current_slot === player.slot : this.currentSlot === player.slot;
                },

                currentTurnName() {
                    const player = this.activePlayers().find(item => item.slot === (this.mode === 'multi' ? this.mpState?.current_slot : this.currentSlot));
                    return player?.name || 'Menunggu';
                },

                isMyTurn() {
                    return this.mode === 'multi' ? Boolean(this.mpState?.is_my_turn) && this.mpState?.status === 'active' : this.currentSlot === 0;
                },

                bagCount() {
                    return this.mode === 'multi' ? (this.mpState?.bag_count || 0) : this.bag.length;
                },

                turnNumber() {
                    return this.mode === 'multi' ? (this.mpState?.turn || 1) : this.turn;
                },

                activeLogs() {
                    return this.mode === 'multi' ? (this.mpState?.logs || []) : this.logs;
                },

                messageText() {
                    return this.mode === 'multi' ? (this.mpState?.message || 'Multiplayer') : this.message;
                },

                cellTile(row, col) {
                    const draft = this.placements.find(item => item.row === row && item.col === col);
                    if (draft) return draft.tile;
                    return this.activeBoard()[row]?.[col] || null;
                },

                cellClass(row, col) {
                    const hasTile = this.cellTile(row, col);
                    const isDraft = this.placements.some(item => item.row === row && item.col === col);
                    if (hasTile) return isDraft ? 'border-cyan-200 bg-cyan-400/20' : 'border-white/10 bg-white/10';
                    if (row === 7 && col === 7) return 'border-yellow-300/70 bg-yellow-400/20 text-yellow-100';
                    if ([0, 7, 14].includes(row) && [0, 7, 14].includes(col)) return 'border-rose-300/40 bg-rose-500/15 text-rose-100';
                    if ((row + col) % 7 === 0) return 'border-cyan-300/30 bg-cyan-500/10 text-cyan-100';
                    return 'border-white/10 bg-slate-900/70 hover:bg-white/10';
                },

                premiumLabel(row, col) {
                    if (row === 7 && col === 7) return '*';
                    if ([0, 7, 14].includes(row) && [0, 7, 14].includes(col)) return 'TW';
                    if ((row + col) % 7 === 0) return 'DL';
                    return '';
                },

                selectTile(tile) {
                    if (!this.isMyTurn()) return;
                    this.selectedTile = tile;
                },

                toggleSwap(tile) {
                    if (!this.isMyTurn()) return;
                    this.selectedSwap = this.selectedSwap.includes(tile.id)
                        ? this.selectedSwap.filter(id => id !== tile.id)
                        : [...this.selectedSwap, tile.id];
                },

                placeSelected(row, col) {
                    if (!this.isMyTurn() || !this.selectedTile || this.cellTile(row, col)) return;
                    this.placements.push({ row, col, tile: this.selectedTile, tile_id: this.selectedTile.id });
                    this.selectedTile = null;
                    this.selectedSwap = [];
                },

                undoPlacement() {
                    const item = this.placements.pop();
                    if (item) this.selectedTile = item.tile;
                },

                submitWord() {
                    if (this.mode === 'multi') {
                        this.sendRoomAction({ action: 'submit', placements: this.placements.map(({ row, col, tile_id }) => ({ row, col, tile_id })) });
                        return;
                    }

                    const result = this.applySoloWord(0, this.placements);
                    if (!result) return;
                    this.afterSoloTurn();
                },

                applySoloWord(slot, placements) {
                    if (placements.length === 0) return false;
                    const rows = [...new Set(placements.map(p => p.row))];
                    const cols = [...new Set(placements.map(p => p.col))];
                    if (rows.length > 1 && cols.length > 1) {
                        this.addLog('Tile harus satu baris atau satu kolom.');
                        return false;
                    }
                    if (this.isBoardEmpty() && !placements.some(p => p.row === 7 && p.col === 7)) {
                        this.addLog('Kata pertama wajib melewati tengah.');
                        return false;
                    }

                    placements.forEach(p => this.board[p.row][p.col] = p.tile);
                    const horizontal = rows.length === 1;
                    const word = this.readWord(placements[0].row, placements[0].col, horizontal);
                    if (word.text.length < 2) {
                        placements.forEach(p => this.board[p.row][p.col] = null);
                        this.addLog('Kata minimal 2 huruf.');
                        return false;
                    }

                    const ids = placements.map(p => p.tile_id);
                    this.players[slot].rack = this.players[slot].rack.filter(tile => !ids.includes(tile.id));
                    this.players[slot].rack = this.draw(this.players[slot].rack, 7 - this.players[slot].rack.length);
                    this.players[slot].score += word.score;
                    this.addLog(this.players[slot].name + ' membuat "' + word.text + '" +' + word.score + ' poin.');
                    this.clearDraft();
                    return true;
                },

                readWord(row, col, horizontal) {
                    const dr = horizontal ? 0 : 1;
                    const dc = horizontal ? 1 : 0;
                    while (row - dr >= 0 && col - dc >= 0 && this.board[row - dr][col - dc]) {
                        row -= dr; col -= dc;
                    }
                    let text = '', score = 0;
                    while (row < 15 && col < 15 && this.board[row][col]) {
                        text += this.board[row][col].letter;
                        score += this.board[row][col].score;
                        row += dr; col += dc;
                    }
                    return { text, score };
                },

                isBoardEmpty() {
                    return this.board.every(row => row.every(cell => !cell));
                },

                afterSoloTurn() {
                    this.currentSlot = 1;
                    this.message = 'Bot Stella berpikir...';
                    window.setTimeout(() => this.botMove(), 700);
                },

                botMove() {
                    const bot = this.players[1];
                    if (bot.rack.length < 2) return this.passTurn();
                    const row = 7 + Math.min(6, this.turn);
                    const col = 6;
                    const tiles = bot.rack.slice(0, 2);
                    const placements = tiles.map((tile, index) => ({ row: Math.min(row, 14), col: col + index, tile, tile_id: tile.id }));
                    if (placements.some(p => this.board[p.row][p.col])) {
                        this.addLog('Bot Stella pass.');
                    } else {
                        this.applySoloWord(1, placements);
                    }
                    this.currentSlot = 0;
                    this.turn++;
                    this.message = 'Giliran kamu';
                },

                swapSelected() {
                    if (this.mode === 'multi') {
                        this.sendRoomAction({ action: 'swap', tile_ids: this.selectedSwap });
                        return;
                    }
                    if (this.selectedSwap.length === 0 || this.bag.length < this.selectedSwap.length) return;
                    const swap = [];
                    this.players[0].rack = this.players[0].rack.filter(tile => {
                        if (this.selectedSwap.includes(tile.id)) { swap.push(tile); return false; }
                        return true;
                    });
                    this.players[0].rack = this.draw(this.players[0].rack, swap.length);
                    this.bag = this.shuffle([...this.bag, ...swap]);
                    this.addLog('Kamu menukar ' + swap.length + ' tile.');
                    this.clearDraft();
                    this.afterSoloTurn();
                },

                passTurn() {
                    if (this.mode === 'multi') {
                        this.sendRoomAction({ action: 'pass' });
                        return;
                    }
                    this.addLog((this.currentSlot === 0 ? 'Kamu' : 'Bot Stella') + ' pass.');
                    if (this.currentSlot === 0) this.afterSoloTurn();
                    else { this.currentSlot = 0; this.turn++; this.message = 'Giliran kamu'; }
                },

                resetGame() {
                    if (this.mode === 'multi') {
                        this.activeRoom ? this.sendRoomAction({ action: 'restart' }) : this.createRoom();
                        return;
                    }
                    this.newSoloGame();
                },

                clearDraft() {
                    this.selectedTile = null;
                    this.selectedSwap = [];
                    this.placements = [];
                },

                addLog(text) {
                    this.logs.unshift({ id: Date.now() + Math.random(), text });
                    this.logs = this.logs.slice(0, 30);
                },

                async toggleFullscreen() {
                    if (!document.fullscreenElement) await this.$refs.root.requestFullscreen();
                    else await document.exitFullscreen();
                },

                async loadRooms() {
                    if (this.mode !== 'multi') return;
                    const data = await this.requestJson('/notted/scrabble/rooms');
                    this.rooms = data.rooms || [];
                },

                async createRoom() {
                    this.loadingRoom = true;
                    try {
                        this.applyRoomPayload(await this.requestJson('/notted/scrabble/rooms', { method: 'POST' }));
                        await this.loadRooms();
                        this.startPolling();
                    } catch (error) { this.addLog(error.message); }
                    finally { this.loadingRoom = false; }
                },

                async joinRoom(room) {
                    try {
                        this.applyRoomPayload(await this.requestJson(`/notted/scrabble/rooms/${room.id}/join`, { method: 'POST' }));
                        await this.loadRooms();
                        this.startPolling();
                    } catch (error) { this.addLog(error.message); }
                },

                async openRoom(room) {
                    this.activeRoom = room;
                    await this.loadRoomState();
                    this.startPolling();
                },

                async loadRoomState() {
                    if (!this.activeRoom) return;
                    this.applyRoomPayload(await this.requestJson(`/notted/scrabble/rooms/${this.activeRoom.id}/state`));
                },

                async sendRoomAction(payload) {
                    if (!this.activeRoom) return this.addLog('Masuk room dulu.');
                    try {
                        this.applyRoomPayload(await this.requestJson(`/notted/scrabble/rooms/${this.activeRoom.id}/action`, {
                            method: 'POST',
                            body: JSON.stringify(payload),
                        }));
                        this.clearDraft();
                        await this.loadRooms();
                    } catch (error) { this.addLog(error.message); }
                },

                applyRoomPayload(data) {
                    this.activeRoom = data.room || this.activeRoom;
                    this.mpState = data.state || this.mpState;
                    this.message = this.mpState?.message || 'Multiplayer';
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
                    if (this.poller) window.clearInterval(this.poller);
                    this.poller = null;
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
                    if (!response.ok) throw new Error(data.message || 'Request gagal.');
                    return data;
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
            };
        }
    </script>
</x-app-layout>
