<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ular Tangga IT — NOTTED Social</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        * { box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #0f172a; color: #f8fafc; min-height: 100vh; }

        /* ── Board ── */
        .board-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .board {
            display: grid;
            grid-template-rows: repeat(10, var(--cs, 52px));
            grid-template-columns: repeat(10, var(--cs, 52px));
            border: 3px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            overflow: hidden;
            min-width: calc(10 * var(--cs, 52px));
            width: calc(10 * var(--cs, 52px));
            box-shadow: 0 0 60px rgba(99,102,241,0.2);
        }
        @media (max-width: 480px) { .board { --cs: 34px; } }
        @media (min-width: 481px) and (max-width: 768px) { .board { --cs: 44px; } }
        @media (min-width: 769px) { .board { --cs: 52px; } }

        .cell {
            width: var(--cs, 52px);
            height: var(--cs, 52px);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-right: 1px solid rgba(255,255,255,0.07);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            transition: all 0.15s ease;
            overflow: hidden;
            cursor: default;
            user-select: none;
        }
        .cell:hover { z-index: 10; }
        .cell-num {
            position: absolute;
            top: 2px;
            left: 4px;
            font-size: 8px;
            font-weight: 800;
            opacity: 0.5;
            line-height: 1;
        }
        @media (min-width: 769px) { .cell-num { font-size: 9px; } }
        .cell-icon {
            font-size: 18px;
            line-height: 1;
            pointer-events: none;
        }
        @media (max-width: 480px) { .cell-icon { font-size: 13px; } }

        /* Cell types */
        .cell-start    { background: linear-gradient(135deg, #1e293b, #334155); }
        .cell-end      { background: linear-gradient(135deg, #78350f, #92400e); }
        .cell-ladder   { background: linear-gradient(135deg, #064e3b, #065f46); border-color: rgba(52,211,153,0.2) !important; }
        .cell-ltop     { background: linear-gradient(135deg, #022c22, #064e3b); }
        .cell-snake    { background: linear-gradient(135deg, #450a0a, #7f1d1d); border-color: rgba(248,113,113,0.2) !important; }
        .cell-stail    { background: linear-gradient(135deg, #2d0b0b, #450a0a); }
        .cell-normal   { background: linear-gradient(135deg, #1e293b, #1a2744); }
        .cell-normal:nth-child(even) { background: linear-gradient(135deg, #192236, #1a2744); }

        /* Player pieces */
        .pieces-container {
            position: absolute;
            bottom: 2px;
            right: 2px;
            display: flex;
            flex-wrap: wrap;
            gap: 1px;
            justify-content: flex-end;
            max-width: 28px;
        }
        .piece {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.5);
            box-shadow: 0 0 4px currentColor;
            flex-shrink: 0;
        }
        @media (max-width: 480px) { .piece { width: 6px; height: 6px; } }
        .piece-0 { background: #818cf8; color: #818cf8; }
        .piece-1 { background: #f87171; color: #f87171; }
        .piece-2 { background: #fbbf24; color: #fbbf24; }
        .piece-3 { background: #34d399; color: #34d399; }

        /* Highlight current cell */
        .cell-current { box-shadow: 0 0 0 2px #fbbf24 inset; z-index: 5; }

        /* Dice */
        .dice-face {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: white;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            padding: 8px;
            gap: 3px;
            transition: transform 0.1s;
        }
        .dice-dot {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #1e293b;
        }
        @keyframes diceRoll {
            0%   { transform: rotate(0deg)   scale(1); }
            25%  { transform: rotate(90deg)  scale(0.85); }
            50%  { transform: rotate(180deg) scale(1.1); }
            75%  { transform: rotate(270deg) scale(0.85); }
            100% { transform: rotate(360deg) scale(1); }
        }
        .dice-rolling { animation: diceRoll 0.12s ease-in-out infinite; }

        /* Question modal */
        .modal-backdrop { backdrop-filter: blur(6px); background: rgba(0,0,0,0.7); }

        /* Answer buttons */
        .answer-btn {
            display: block;
            width: 100%;
            text-align: left;
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            border: 2px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: #e2e8f0;
            transition: all 0.15s;
            cursor: pointer;
        }
        .answer-btn:hover:not(:disabled) { background: rgba(255,255,255,0.12); border-color: rgba(165,180,252,0.5); color: white; }
        .answer-btn.correct { background: rgba(16,185,129,0.2); border-color: #10b981; color: #6ee7b7; }
        .answer-btn.wrong   { background: rgba(239,68,68,0.15); border-color: #ef4444; color: #fca5a5; }
        .answer-btn.reveal  { background: rgba(16,185,129,0.1); border-color: #10b981; color: #6ee7b7; }
        .answer-btn:disabled { cursor: default; }

        /* Message log */
        .msg-log { max-height: 120px; overflow-y: auto; }
        .msg-log::-webkit-scrollbar { width: 3px; }
        .msg-log::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.4); border-radius: 2px; }

        @keyframes msgIn {
            from { opacity: 0; transform: translateX(-8px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .msg-item { animation: msgIn 0.25s ease-out both; }

        /* Win confetti */
        @keyframes confetti {
            0%   { transform: translateY(-20px) rotate(0deg);   opacity: 1; }
            100% { transform: translateY(120px) rotate(720deg); opacity: 0; }
        }
        .confetti-piece {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            animation: confetti 1.5s ease-in both;
        }
    </style>
</head>

<body x-data="snakeLadder()" x-init="init()">

{{-- ═══════════════════════════ HEADER ═══════════════════════════ --}}
<div class="sticky top-0 z-50 bg-slate-950/90 backdrop-blur border-b border-white/5 px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('notted.games') }}" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <div class="text-base font-black leading-tight">🐍 Ular Tangga <span class="text-emerald-400">IT</span></div>
            <div class="text-[10px] text-slate-500 font-medium uppercase tracking-widest">Edukatif · NOTTED Games</div>
        </div>
    </div>
    <button x-show="phase === 'playing'" @click="if(confirm('Reset game?')) phase = 'setup'"
        class="text-xs font-bold text-slate-500 hover:text-red-400 transition-colors px-3 py-1.5 rounded-lg hover:bg-red-900/20 border border-transparent hover:border-red-900/40">
        Reset
    </button>
</div>

{{-- ═══════════════════════════ SETUP SCREEN ═══════════════════════════ --}}
<div x-show="phase === 'setup'" x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    class="max-w-lg mx-auto px-4 py-8">

    <div class="text-center mb-8">
        <div class="text-6xl mb-4">🐍🪜</div>
        <h1 class="text-3xl font-black text-white mb-2">Ular Tangga IT</h1>
        <p class="text-slate-400 text-sm leading-relaxed">Jawab soal untuk naik tangga, hindari ular dengan jawaban benar!</p>
    </div>

    {{-- How to play --}}
    <div class="bg-white/5 rounded-2xl p-5 border border-white/8 mb-6 space-y-3">
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Cara Main</p>
        <div class="flex gap-3 text-sm text-slate-300">
            <span class="text-lg shrink-0">🪜</span>
            <p>Mendarat di <span class="text-emerald-400 font-bold">tangga</span>? Jawab soal benar untuk naik ke atas!</p>
        </div>
        <div class="flex gap-3 text-sm text-slate-300">
            <span class="text-lg shrink-0">🐍</span>
            <p>Mendarat di <span class="text-red-400 font-bold">kepala ular</span>? Jawab benar untuk selamat, salah = meluncur turun!</p>
        </div>
        <div class="flex gap-3 text-sm text-slate-300">
            <span class="text-lg shrink-0">🎲</span>
            <p>Lempar dadu, gilir bergantian. Siapa capai kotak <span class="text-amber-400 font-bold">100</span> duluan — menang!</p>
        </div>
    </div>

    {{-- Player count --}}
    <div class="mb-6">
        <p class="text-sm font-bold text-slate-300 mb-3">Jumlah Pemain</p>
        <div class="flex gap-2">
            <template x-for="n in [2,3,4]" :key="n">
                <button @click="playerCount = n"
                    class="flex-1 py-3 rounded-xl font-black text-lg border-2 transition-all"
                    :class="playerCount === n
                        ? 'bg-indigo-600 border-indigo-500 text-white shadow-lg shadow-indigo-900/50'
                        : 'bg-white/5 border-white/10 text-slate-400 hover:border-white/20'">
                    <span x-text="n"></span>
                </button>
            </template>
        </div>
    </div>

    {{-- Player names --}}
    <div class="space-y-3 mb-8">
        <p class="text-sm font-bold text-slate-300">Nama Pemain</p>
        <template x-for="i in playerCount" :key="i">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full shrink-0 border-2 border-white/20 flex items-center justify-center text-base"
                    :class="['bg-indigo-500','bg-rose-500','bg-amber-500','bg-emerald-500'][i-1]">
                    <span x-text="['🟣','🔴','🟡','🟢'][i-1]"></span>
                </div>
                <input :value="playerNames[i-1]" @input="playerNames[i-1] = $event.target.value"
                    :placeholder="'Pemain ' + i"
                    class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm font-bold text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/30">
            </div>
        </template>
    </div>

    <button @click="startGame()"
        class="w-full py-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-black text-lg rounded-2xl shadow-2xl shadow-emerald-900/50 hover:from-emerald-500 hover:to-teal-500 transition-all transform hover:scale-[1.02] active:scale-[0.98]">
        🎮 Mulai Bermain!
    </button>
</div>

{{-- ═══════════════════════════ GAME SCREEN ═══════════════════════════ --}}
<div x-show="phase === 'playing'" x-cloak class="p-3 sm:p-5 max-w-[1200px] mx-auto">
    <div class="flex flex-col xl:flex-row gap-5 items-start">

        {{-- ──────── BOARD ──────── --}}
        <div class="board-wrap flex-shrink-0 mx-auto xl:mx-0">
            <div class="board">
                <template x-for="(row, ri) in boardRows" :key="ri">
                    <template x-for="cellNum in row" :key="cellNum">
                        <div class="cell"
                            :class="[
                                cellNum === 1   ? 'cell-start' :
                                cellNum === 100 ? 'cell-end'   :
                                isLadderBottom(cellNum) ? 'cell-ladder' :
                                isLadderTop(cellNum)    ? 'cell-ltop'   :
                                isSnakeHead(cellNum)    ? 'cell-snake'  :
                                isSnakeTail(cellNum)    ? 'cell-stail'  : 'cell-normal',
                                players.some(p => p.position === cellNum) ? 'cell-current' : ''
                            ]"
                            :title="getCellTitle(cellNum)">
                            <span class="cell-num" x-text="cellNum === 1 ? 'START' : cellNum === 100 ? '🏆' : cellNum"></span>
                            <span class="cell-icon" x-text="getCellIcon(cellNum)"></span>
                            {{-- Player pieces --}}
                            <div class="pieces-container">
                                <template x-for="(p, pi) in players.filter(p => p.position === cellNum)" :key="pi">
                                    <div class="piece" :class="'piece-' + p.colorIdx" :title="p.name"></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
            {{-- Legend --}}
            <div class="flex flex-wrap gap-3 mt-3 text-[10px] font-bold text-slate-500 justify-center">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-emerald-800 inline-block"></span> Tangga (bawah)</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-red-900 inline-block"></span> Kepala Ular</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-amber-700 inline-block"></span> Finish</span>
            </div>
        </div>

        {{-- ──────── CONTROLS ──────── --}}
        <div class="flex-1 w-full xl:max-w-xs space-y-4">

            {{-- Current player banner --}}
            <div class="rounded-2xl p-4 border border-white/10"
                :class="['bg-indigo-900/40','bg-rose-900/40','bg-amber-900/40','bg-emerald-900/40'][players[currentPlayerIdx]?.colorIdx ?? 0]">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-white/50 uppercase tracking-widest">Giliran</span>
                    <span class="text-[10px] font-black uppercase tracking-widest"
                        :class="['text-indigo-400','text-rose-400','text-amber-400','text-emerald-400'][players[currentPlayerIdx]?.colorIdx ?? 0]">
                        Kotak <span x-text="players[currentPlayerIdx]?.position === 0 ? 'Start' : players[currentPlayerIdx]?.position"></span>
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-2xl border-2 border-white/20"
                        :class="['bg-indigo-500','bg-rose-500','bg-amber-500','bg-emerald-500'][players[currentPlayerIdx]?.colorIdx ?? 0]">
                        <span x-text="['🟣','🔴','🟡','🟢'][players[currentPlayerIdx]?.colorIdx ?? 0]"></span>
                    </div>
                    <div>
                        <p class="font-black text-white text-lg leading-tight" x-text="players[currentPlayerIdx]?.name"></p>
                        <p class="text-xs text-white/50" x-text="'Kotak ' + (players[currentPlayerIdx]?.position === 0 ? 'Awal' : players[currentPlayerIdx]?.position)"></p>
                    </div>
                </div>
            </div>

            {{-- Dice + Roll button --}}
            <div class="bg-white/5 rounded-2xl p-5 border border-white/8 text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="dice-face" :class="isRolling ? 'dice-rolling' : ''">
                        <template x-for="pos in 9" :key="pos">
                            <div class="flex items-center justify-center">
                                <div class="dice-dot" x-show="isDotVisible(diceResult, pos)"></div>
                            </div>
                        </template>
                    </div>
                </div>
                <p class="text-3xl font-black text-white mb-1" x-text="diceResult ? diceResult : '?'"></p>
                <p class="text-xs text-slate-500 mb-4">
                    <span x-show="!diceResult">Klik untuk lempar dadu</span>
                    <span x-show="diceResult && diceResult === 6" class="text-amber-400 font-bold">Dapat 6 — lempar lagi!</span>
                    <span x-show="diceResult && diceResult !== 6">Dadu</span>
                </p>
                <button @click="rollDice()"
                    :disabled="isRolling || isAnimating"
                    class="w-full py-3 rounded-xl font-black text-white transition-all disabled:opacity-40 disabled:cursor-not-allowed text-sm border"
                    :class="isRolling || isAnimating
                        ? 'bg-slate-700 border-slate-600'
                        : 'bg-gradient-to-r from-indigo-600 to-purple-600 border-indigo-500 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-900/40 hover:scale-[1.02] active:scale-[0.98]'">
                    <span x-show="!isRolling">🎲 Lempar Dadu</span>
                    <span x-show="isRolling">Mengocok...</span>
                </button>
            </div>

            {{-- Player Scoreboard --}}
            <div class="bg-white/5 rounded-2xl border border-white/8 overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/5">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Posisi Pemain</p>
                </div>
                <template x-for="(p, i) in players" :key="i">
                    <div class="flex items-center gap-3 px-4 py-3 border-b border-white/5 last:border-0 transition-colors"
                        :class="i === currentPlayerIdx ? 'bg-white/5' : ''">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-sm border border-white/20 flex-shrink-0"
                            :class="['bg-indigo-500/80','bg-rose-500/80','bg-amber-500/80','bg-emerald-500/80'][p.colorIdx]">
                            <span x-text="['🟣','🔴','🟡','🟢'][p.colorIdx]"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-white truncate" x-text="p.name"></p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs font-black"
                                :class="i === currentPlayerIdx ? 'text-white' : 'text-slate-500'"
                                x-text="p.position === 0 ? 'Start' : 'Kotak ' + p.position"></p>
                            <div class="w-20 h-1.5 bg-white/10 rounded-full mt-1">
                                <div class="h-full rounded-full transition-all duration-500"
                                    :class="['bg-indigo-400','bg-rose-400','bg-amber-400','bg-emerald-400'][p.colorIdx]"
                                    :style="'width:' + Math.min(p.position, 100) + '%'"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Message Log --}}
            <div class="bg-white/5 rounded-2xl border border-white/8 overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/5">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Log Kejadian</p>
                </div>
                <div class="msg-log p-3 space-y-1.5">
                    <template x-for="(msg, i) in messages.slice().reverse().slice(0, 12)" :key="i">
                        <p class="msg-item text-xs text-slate-400 leading-snug" x-text="msg"></p>
                    </template>
                    <p x-show="messages.length === 0" class="text-xs text-slate-600 italic">Lempar dadu untuk mulai...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════ QUESTION MODAL ═══════════════════════════ --}}
<div x-show="qState.active" x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    class="fixed inset-0 z-[80] flex items-center justify-center p-4 modal-backdrop">

    <div x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="bg-slate-900 border border-white/10 rounded-3xl p-6 max-w-md w-full shadow-2xl shadow-black/50 relative">

        {{-- Context badge --}}
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0"
                :class="qState.isLadder ? 'bg-emerald-900/60 border border-emerald-700/50' : 'bg-red-900/60 border border-red-700/50'">
                <span x-text="qState.isLadder ? '🪜' : '🐍'"></span>
            </div>
            <div>
                <p class="font-black text-white text-base leading-tight"
                    x-text="qState.isLadder ? 'Tangga Ditemukan!' : 'Kepala Ular!'"></p>
                <p class="text-xs font-medium leading-snug"
                    :class="qState.isLadder ? 'text-emerald-400' : 'text-red-400'"
                    x-text="qState.isLadder
                        ? 'Jawab benar → naik ke kotak ' + qState.targetSquare + ' 🎉'
                        : 'Jawab benar → selamat! Salah → meluncur ke kotak ' + qState.targetSquare + ' 😱'"></p>
            </div>
        </div>

        {{-- Question --}}
        <div class="bg-white/5 rounded-2xl p-4 mb-5 border border-white/8">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Pertanyaan</p>
            <p class="text-sm font-bold text-white leading-relaxed" x-text="qState.question?.q"></p>
        </div>

        {{-- Options --}}
        <div class="space-y-2 mb-5" x-show="!qState.answered">
            <template x-for="(opt, i) in qState.question?.options" :key="i">
                <button class="answer-btn" @click="answerQuestion(i)">
                    <span class="text-slate-500 mr-2" x-text="['A','B','C','D'][i] + '.'"></span>
                    <span x-text="opt"></span>
                </button>
            </template>
        </div>

        {{-- Result --}}
        <div x-show="qState.answered" class="space-y-2 mb-5">
            <template x-for="(opt, i) in qState.question?.options" :key="i">
                <div class="answer-btn"
                    :class="{
                        'correct': i === qState.question?.a,
                        'wrong':   i === qState.selected && i !== qState.question?.a,
                    }">
                    <span class="text-slate-500 mr-2" x-text="['A','B','C','D'][i] + '.'"></span>
                    <span x-text="opt"></span>
                    <span x-show="i === qState.question?.a" class="ml-2 text-emerald-400">✓</span>
                    <span x-show="i === qState.selected && i !== qState.question?.a" class="ml-2 text-red-400">✗</span>
                </div>
            </template>
            {{-- Result message --}}
            <div class="mt-4 p-3 rounded-xl text-center font-bold text-sm"
                :class="qState.correct
                    ? 'bg-emerald-900/40 border border-emerald-700/40 text-emerald-300'
                    : 'bg-red-900/30 border border-red-800/40 text-red-300'"
                x-text="qState.resultMsg"></div>
            <button @click="confirmAnswer()"
                class="w-full py-3 rounded-xl font-black text-white text-sm bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 transition-all mt-2">
                Lanjutkan →
            </button>
        </div>

        {{-- Player info in modal --}}
        <p class="text-center text-[10px] text-slate-600 font-medium">
            Giliran: <span class="text-slate-400 font-bold" x-text="players[currentPlayerIdx]?.name"></span>
        </p>
    </div>
</div>

{{-- ═══════════════════════════ WIN SCREEN ═══════════════════════════ --}}
<div x-show="phase === 'finished'" x-cloak
    x-transition:enter="transition ease-out duration-400"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    class="fixed inset-0 z-[90] flex items-center justify-center p-4 modal-backdrop">

    <div class="relative bg-slate-900 border border-white/10 rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl">
        {{-- Confetti --}}
        <template x-for="c in 18" :key="c">
            <div class="confetti-piece"
                :style="`left:${Math.random()*90+5}%;top:0;background:${['#818cf8','#f87171','#fbbf24','#34d399','#a78bfa','#fb923c'][Math.floor(Math.random()*6)]};animation-delay:${Math.random()*0.5}s;animation-duration:${1+Math.random()}s`">
            </div>
        </template>

        <div class="text-6xl mb-4">🏆</div>
        <h2 class="text-3xl font-black text-white mb-2">Selamat!</h2>
        <p class="text-slate-400 mb-1">Pemenangnya adalah</p>
        <p class="text-2xl font-black mb-1"
            :class="['text-indigo-400','text-rose-400','text-amber-400','text-emerald-400'][winner?.colorIdx ?? 0]"
            x-text="winner?.name"></p>
        <p class="text-slate-500 text-sm mb-8">🎉 Sudah berhasil mencapai kotak 100!</p>

        <div class="flex gap-3">
            <button @click="phase = 'setup'"
                class="flex-1 py-3 bg-white/8 border border-white/10 text-white font-bold rounded-xl hover:bg-white/15 transition-all text-sm">
                Setup Baru
            </button>
            <button @click="restartGame()"
                class="flex-1 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-black rounded-xl hover:from-emerald-500 hover:to-teal-500 transition-all text-sm">
                Main Lagi
            </button>
        </div>
    </div>
</div>

<script>
// ── Game constants ──
const LADDERS = { 4:14, 9:31, 20:38, 28:84, 40:59, 51:67, 63:81, 71:91 };
const SNAKES  = { 17:7, 54:34, 62:19, 64:60, 87:24, 93:73, 95:75, 99:78 };

const LADDER_BOTTOMS = new Set(Object.keys(LADDERS).map(Number));
const LADDER_TOPS    = new Set(Object.values(LADDERS));
const SNAKE_HEADS    = new Set(Object.keys(SNAKES).map(Number));
const SNAKE_TAILS    = new Set(Object.values(SNAKES));

const QUESTIONS = [
    { q:'Apa kepanjangan dari IP?', options:['Internet Protocol','Internal Port','Input Program','Interface Panel'], a:0 },
    { q:'Berapa jumlah bit dalam sebuah IPv4?', options:['16 bit','32 bit','64 bit','128 bit'], a:1 },
    { q:'Port berapa yang digunakan protokol HTTP?', options:['21','22','80','443'], a:2 },
    { q:'Apa kepanjangan dari LAN?', options:['Large Area Network','Local Area Network','Linked Area Network','Layered Area Network'], a:1 },
    { q:'Berapa layer dalam model OSI?', options:['4','5','7','8'], a:2 },
    { q:'Perangkat jaringan apa yang bekerja di Layer 3 OSI?', options:['Hub','Switch','Router','Repeater'], a:2 },
    { q:'Apa fungsi utama protokol DNS?', options:['Mengamankan koneksi','Menerjemahkan domain ke IP','Mengatur bandwidth','Menyimpan file'], a:1 },
    { q:'Tipe kabel apa yang menggunakan cahaya untuk transmisi data?', options:['UTP','STP','Coaxial','Fiber Optik'], a:3 },
    { q:'Apa itu MAC address?', options:['Memory Access Code','Media Access Control','Master Application Code','Machine Active Credential'], a:1 },
    { q:'Mana protokol lebih aman, HTTP atau HTTPS?', options:['HTTP','HTTPS','Sama saja','Tergantung browser'], a:1 },
    { q:'Apa kepanjangan dari HTML?', options:['HyperText Markup Language','High Transfer Markup Language','HyperText Machine Language','Home Tool Markup Language'], a:0 },
    { q:'Simbol selector class di CSS adalah?', options:['#','.','@','&'], a:1 },
    { q:'Apa kepanjangan dari SQL?', options:['Structured Query Language','Simple Query Language','Standard Query Logic','System Query Level'], a:0 },
    { q:'Perintah SQL untuk mengambil data adalah?', options:['INSERT','UPDATE','SELECT','DELETE'], a:2 },
    { q:'Apa kepanjangan dari PHP?', options:['Personal Home Page','Private Hypertext Processor','PHP: Hypertext Preprocessor','Primary Homepage Program'], a:2 },
    { q:'Tag HTML untuk membuat hyperlink adalah?', options:['<link>','<url>','<a>','<href>'], a:2 },
    { q:'Tipe data yang hanya menyimpan true/false disebut?', options:['Integer','String','Boolean','Float'], a:2 },
    { q:'Cara mencetak "Hello World" di Python?', options:['console.log("Hello World")','echo "Hello World"','print("Hello World")','System.out.println("Hello World")'], a:2 },
    { q:'Apa yang dimaksud dengan "Bug" dalam pemrograman?', options:['Fitur tersembunyi','Kesalahan dalam kode','Jenis variabel','Tipe data khusus'], a:1 },
    { q:'Apa kepanjangan dari GUI?', options:['General User Interface','Graphic Unit Integration','Graphical User Interface','Global Unified Interface'], a:2 },
    { q:'Berapa nilai maksimum 1 byte?', options:['127','255','256','512'], a:1 },
    { q:'Nilai biner 1010 dalam desimal adalah?', options:['8','9','10','11'], a:2 },
    { q:'Apa kepanjangan dari RAM?', options:['Random Access Memory','Rapid Action Module','Read After Memory','Remote Application Management'], a:0 },
    { q:'Protokol yang mengatur pengiriman email adalah?', options:['FTP','SSH','SMTP','HTTP'], a:2 },
    { q:'Apa itu firewall dalam jaringan?', options:['Perangkat untuk mempercepat koneksi','Sistem keamanan untuk memfilter lalu lintas jaringan','Jenis kabel jaringan','Protokol transfer file'], a:1 },
    { q:'Dalam pemrograman OOP, apa itu "class"?', options:['Nilai yang tersimpan','Cetak biru/template untuk membuat objek','Nama fungsi','Jenis variabel'], a:1 },
    { q:'Apa kepanjangan dari URL?', options:['Uniform Resource Locator','Universal Reference Link','Unified Remote Location','User Request Language'], a:0 },
    { q:'Perintah Linux untuk melihat isi direktori adalah?', options:['dir','list','ls','show'], a:2 },
    { q:'Apa kepanjangan dari VPN?', options:['Virtual Private Network','Very Protected Network','Verified Personal Node','Virtual Public Network'], a:0 },
    { q:'Berapa jumlah warna kombinasi dalam kode warna Hex #FFFFFF?', options:['16 juta','256','65536','1 juta'], a:0 },
];

// ── Dot positions for dice faces ──
// positions 1-9 (top-left to bottom-right of 3x3 grid)
const DICE_DOTS = {
    1: [5],
    2: [3,7],
    3: [3,5,7],
    4: [1,3,7,9],
    5: [1,3,5,7,9],
    6: [1,3,4,6,7,9],
};

function snakeLadder() {
    return {
        phase: 'setup',
        playerCount: 2,
        playerNames: ['Pemain 1', 'Pemain 2', 'Pemain 3', 'Pemain 4'],
        players: [],
        currentPlayerIdx: 0,
        diceResult: null,
        isRolling: false,
        isAnimating: false,
        messages: [],
        winner: null,
        qState: {
            active: false,
            question: null,
            selected: null,
            answered: false,
            correct: false,
            isLadder: false,
            isSnake: false,
            landedOn: 0,
            targetSquare: 0,
            resultMsg: '',
        },

        get boardRows() {
            const rows = [];
            for (let displayRow = 0; displayRow < 10; displayRow++) {
                const gameRow   = 9 - displayRow;
                const base      = gameRow * 10;
                const ltr       = (gameRow % 2 === 0);
                const row       = [];
                for (let col = 0; col < 10; col++) {
                    row.push(ltr ? base + col + 1 : base + 10 - col);
                }
                rows.push(row);
            }
            return rows;
        },

        init() {},

        startGame() {
            this.players = [];
            for (let i = 0; i < this.playerCount; i++) {
                this.players.push({
                    name: this.playerNames[i].trim() || ('Pemain ' + (i+1)),
                    colorIdx: i,
                    position: 0,
                });
            }
            this.currentPlayerIdx = 0;
            this.diceResult = null;
            this.messages = [];
            this.winner = null;
            this.phase = 'playing';
            this.addMsg('🎮 Game dimulai! Giliran ' + this.players[0].name + ' pertama.');
        },

        restartGame() {
            this.players.forEach(p => p.position = 0);
            this.currentPlayerIdx = 0;
            this.diceResult = null;
            this.messages = [];
            this.winner = null;
            this.phase = 'playing';
            this.addMsg('🔄 Game direset! Semua kembali ke start.');
        },

        rollDice() {
            if (this.isRolling || this.isAnimating) return;
            this.isRolling = true;
            let count = 0;
            const interval = setInterval(() => {
                this.diceResult = Math.floor(Math.random() * 6) + 1;
                count++;
                if (count >= 10) {
                    clearInterval(interval);
                    const finalVal = Math.floor(Math.random() * 6) + 1;
                    this.diceResult = finalVal;
                    this.isRolling = false;
                    this.addMsg(`🎲 ${this.currentPlayer().name} lempar dadu → ${finalVal}`);
                    setTimeout(() => this.movePlayer(finalVal), 300);
                }
            }, 60);
        },

        movePlayer(steps) {
            this.isAnimating = true;
            const p       = this.currentPlayer();
            let newPos    = p.position + steps;

            // Bounce back if overshoot 100
            if (newPos > 100) {
                newPos = 100 - (newPos - 100);
                this.addMsg(`↩️ Terlalu jauh! Kembali ke kotak ${newPos}`);
            }

            p.position = newPos;

            // Win check
            if (newPos === 100) {
                this.winner = p;
                this.isAnimating = false;
                setTimeout(() => { this.phase = 'finished'; }, 400);
                this.addMsg(`🏆 ${p.name} MENANG di kotak 100!`);
                return;
            }

            // Check snake / ladder
            if (LADDER_BOTTOMS.has(newPos)) {
                setTimeout(() => {
                    this.isAnimating = false;
                    this.openQuestion(true, newPos, LADDERS[newPos]);
                }, 400);
            } else if (SNAKE_HEADS.has(newPos)) {
                setTimeout(() => {
                    this.isAnimating = false;
                    this.openQuestion(false, newPos, SNAKES[newPos]);
                }, 400);
            } else {
                this.addMsg(`📍 ${p.name} bergerak ke kotak ${newPos}`);
                this.isAnimating = false;
                // Roll again on 6
                if (this.diceResult === 6) {
                    this.addMsg(`🎉 ${p.name} dapat 6, lempar lagi!`);
                    this.diceResult = null;
                } else {
                    this.endTurn();
                }
            }
        },

        openQuestion(isLadder, landedOn, target) {
            const pool = [...QUESTIONS];
            const q    = pool[Math.floor(Math.random() * pool.length)];
            this.qState = {
                active: true,
                question: q,
                selected: null,
                answered: false,
                correct: false,
                isLadder,
                isSnake: !isLadder,
                landedOn,
                targetSquare: target,
                resultMsg: '',
            };
            const p = this.currentPlayer();
            if (isLadder) {
                this.addMsg(`🪜 ${p.name} mendarat di tangga (${landedOn} → ${target})! Harus jawab soal.`);
            } else {
                this.addMsg(`🐍 ${p.name} mendapat kepala ular (${landedOn} → ${target})! Harus jawab soal.`);
            }
        },

        answerQuestion(idx) {
            if (this.qState.answered) return;
            const correct = idx === this.qState.question.a;
            const p       = this.currentPlayer();
            this.qState.selected  = idx;
            this.qState.answered  = true;
            this.qState.correct   = correct;

            if (this.qState.isLadder) {
                if (correct) {
                    this.qState.resultMsg = `✅ Benar! ${p.name} naik ke kotak ${this.qState.targetSquare}!`;
                } else {
                    this.qState.resultMsg = `❌ Salah! ${p.name} tetap di kotak ${this.qState.landedOn}.`;
                }
            } else {
                if (correct) {
                    this.qState.resultMsg = `✅ Benar! ${p.name} selamat dari ular!`;
                } else {
                    this.qState.resultMsg = `❌ Salah! ${p.name} meluncur ke kotak ${this.qState.targetSquare}!`;
                }
            }
        },

        confirmAnswer() {
            const p = this.currentPlayer();
            if (this.qState.isLadder) {
                if (this.qState.correct) {
                    p.position = this.qState.targetSquare;
                    this.addMsg(`🎉 ${p.name} naik tangga ke kotak ${this.qState.targetSquare}!`);
                    // Check win after ladder
                    if (p.position === 100) {
                        this.winner = p;
                        this.qState.active = false;
                        setTimeout(() => { this.phase = 'finished'; }, 300);
                        return;
                    }
                } else {
                    this.addMsg(`😞 ${p.name} gagal naik, tetap di kotak ${this.qState.landedOn}.`);
                }
            } else {
                if (this.qState.correct) {
                    this.addMsg(`✅ ${p.name} selamat dari ular di kotak ${this.qState.landedOn}!`);
                } else {
                    p.position = this.qState.targetSquare;
                    this.addMsg(`😱 ${p.name} meluncur ke kotak ${this.qState.targetSquare}!`);
                }
            }
            this.qState.active = false;
            if (this.diceResult === 6 && this.qState.correct) {
                this.addMsg(`🎉 ${p.name} dapat 6, lempar lagi!`);
                this.diceResult = null;
            } else {
                this.endTurn();
            }
        },

        endTurn() {
            this.diceResult = null;
            this.currentPlayerIdx = (this.currentPlayerIdx + 1) % this.players.length;
            this.addMsg(`➡️ Giliran ${this.currentPlayer().name}`);
        },

        currentPlayer() {
            return this.players[this.currentPlayerIdx];
        },

        isLadderBottom(n) { return LADDER_BOTTOMS.has(n); },
        isLadderTop(n)    { return LADDER_TOPS.has(n);    },
        isSnakeHead(n)    { return SNAKE_HEADS.has(n);    },
        isSnakeTail(n)    { return SNAKE_TAILS.has(n);    },

        getCellIcon(n) {
            if (n === 1)   return '🚀';
            if (n === 100) return '⭐';
            if (LADDER_BOTTOMS.has(n)) return '🪜';
            if (SNAKE_HEADS.has(n))    return '🐍';
            return '';
        },

        getCellTitle(n) {
            if (n === 1)   return 'START';
            if (n === 100) return 'FINISH!';
            if (LADDER_BOTTOMS.has(n)) return `Tangga → Kotak ${LADDERS[n]}`;
            if (SNAKE_HEADS.has(n))    return `Kepala Ular → Kotak ${SNAKES[n]}`;
            if (LADDER_TOPS.has(n))    return `Ujung tangga dari bawah`;
            if (SNAKE_TAILS.has(n))    return `Ekor ular`;
            return `Kotak ${n}`;
        },

        isDotVisible(face, pos) {
            if (!face || !DICE_DOTS[face]) return false;
            return DICE_DOTS[face].includes(pos);
        },

        addMsg(text) {
            const ts = new Date().toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
            this.messages.push(`[${ts}] ${text}`);
        },
    };
}
</script>
</body>
</html>
