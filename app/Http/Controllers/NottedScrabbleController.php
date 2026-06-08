<?php

namespace App\Http\Controllers;

use App\Models\NottedScrabbleRoom;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NottedScrabbleController extends Controller
{
    private array $letterScores = [
        'A' => 1, 'I' => 1, 'N' => 1, 'R' => 1, 'S' => 1, 'T' => 1, 'U' => 1,
        'D' => 2, 'G' => 2, 'K' => 2, 'L' => 2, 'M' => 2,
        'B' => 3, 'H' => 3, 'O' => 3, 'P' => 3,
        'C' => 4, 'E' => 4, 'J' => 4, 'W' => 4,
        'F' => 5, 'V' => 5, 'Y' => 5, 'Z' => 8, 'Q' => 10, 'X' => 10,
    ];

    public function rooms(Request $request): JsonResponse
    {
        $user = $request->user();

        $rooms = NottedScrabbleRoom::with(['host:id,name', 'guest:id,name'])
            ->where(function ($query) use ($user) {
                $query->where('status', 'waiting')
                    ->orWhere('host_user_id', $user->id)
                    ->orWhere('guest_user_id', $user->id);
            })
            ->latest('last_activity_at')
            ->take(16)
            ->get()
            ->map(fn (NottedScrabbleRoom $room) => $this->roomSummary($room, $user));

        return response()->json(['rooms' => $rooms]);
    }

    public function create(Request $request): JsonResponse
    {
        $room = NottedScrabbleRoom::create([
            'code' => $this->uniqueCode(),
            'host_user_id' => $request->user()->id,
            'status' => 'waiting',
            'state' => [
                'message' => 'Menunggu pemain kedua',
                'logs' => [$this->logText($request->user()->name . ' membuat room Scrabble.')],
            ],
            'last_activity_at' => now(),
        ]);

        $room->load(['host:id,name', 'guest:id,name']);

        return response()->json([
            'room' => $this->roomSummary($room, $request->user()),
            'state' => $this->publicState($room, $request->user()),
        ]);
    }

    public function join(Request $request, NottedScrabbleRoom $room): JsonResponse
    {
        $user = $request->user();

        if ($room->hasPlayer($user)) {
            return response()->json([
                'room' => $this->roomSummary($room->load(['host:id,name', 'guest:id,name']), $user),
                'state' => $this->publicState($room, $user),
            ]);
        }

        if ($room->status !== 'waiting' || $room->guest_user_id) {
            return response()->json(['message' => 'Room sudah penuh atau permainan sudah berjalan.'], 422);
        }

        $room = DB::transaction(function () use ($room, $user) {
            $locked = NottedScrabbleRoom::whereKey($room->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== 'waiting' || $locked->guest_user_id) {
                abort(422, 'Room sudah penuh atau permainan sudah berjalan.');
            }

            $host = User::findOrFail($locked->host_user_id);
            $locked->guest_user_id = $user->id;
            $locked->status = 'active';
            $locked->state = $this->newGameState($host, $user);
            $locked->last_activity_at = now();
            $locked->save();

            return $locked;
        });

        $room->load(['host:id,name', 'guest:id,name']);

        return response()->json([
            'room' => $this->roomSummary($room, $user),
            'state' => $this->publicState($room, $user),
        ]);
    }

    public function state(Request $request, NottedScrabbleRoom $room): JsonResponse
    {
        $this->authorizePlayer($room, $request->user());

        return response()->json([
            'room' => $this->roomSummary($room->load(['host:id,name', 'guest:id,name']), $request->user()),
            'state' => $this->publicState($room, $request->user()),
        ]);
    }

    public function action(Request $request, NottedScrabbleRoom $room): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|string|in:submit,swap,pass,restart',
            'placements' => 'nullable|array|max:7',
            'placements.*.row' => 'required_with:placements|integer|min:0|max:14',
            'placements.*.col' => 'required_with:placements|integer|min:0|max:14',
            'placements.*.tile_id' => 'required_with:placements|string',
            'tile_ids' => 'nullable|array|max:7',
            'tile_ids.*' => 'string',
        ]);

        $user = $request->user();
        $room = DB::transaction(function () use ($room, $user, $validated) {
            $locked = NottedScrabbleRoom::whereKey($room->id)->lockForUpdate()->firstOrFail();
            $this->authorizePlayer($locked, $user);

            if ($validated['action'] === 'restart') {
                if (!$locked->guest_user_id) {
                    abort(422, 'Room belum memiliki lawan.');
                }

                $locked->status = 'active';
                $locked->winner_user_id = null;
                $locked->state = $this->newGameState(User::findOrFail($locked->host_user_id), User::findOrFail($locked->guest_user_id));
                $locked->last_activity_at = now();
                $locked->save();

                return $locked;
            }

            if ($locked->status !== 'active') {
                abort(422, 'Permainan belum aktif.');
            }

            $state = $locked->state;
            $slot = $this->slotForUser($state, $user);

            if ($slot === null || ($state['current_slot'] ?? null) !== $slot) {
                abort(422, 'Belum giliran kamu.');
            }

            $state = match ($validated['action']) {
                'submit' => $this->submitWord($state, $slot, $validated['placements'] ?? []),
                'swap' => $this->swapTiles($state, $slot, $validated['tile_ids'] ?? []),
                'pass' => $this->passTurn($state, $slot),
                default => $state,
            };

            if (($state['status'] ?? 'active') === 'finished') {
                $winner = collect($state['players'])->sortByDesc('score')->first();
                $locked->status = 'finished';
                $locked->winner_user_id = $winner['user_id'] ?? null;
            }

            $locked->state = $state;
            $locked->last_activity_at = now();
            $locked->save();

            return $locked;
        });

        $room->load(['host:id,name', 'guest:id,name']);

        return response()->json([
            'room' => $this->roomSummary($room, $user),
            'state' => $this->publicState($room, $user),
        ]);
    }

    private function newGameState(User $host, User $guest): array
    {
        $bag = $this->shuffle($this->buildBag());
        $players = [
            ['slot' => 0, 'user_id' => $host->id, 'name' => $host->name, 'rack' => [], 'score' => 0],
            ['slot' => 1, 'user_id' => $guest->id, 'name' => $guest->name, 'rack' => [], 'score' => 0],
        ];

        for ($slot = 0; $slot < 2; $slot++) {
            [$players[$slot]['rack'], $bag] = $this->drawTiles($bag, [], 7);
        }

        return [
            'board' => array_fill(0, 15, array_fill(0, 15, null)),
            'bag' => $bag,
            'players' => $players,
            'current_slot' => 0,
            'turn' => 1,
            'passes' => 0,
            'status' => 'active',
            'message' => 'Giliran ' . $host->name,
            'logs' => [$this->logText('Scrabble dimulai. Kata pertama wajib melewati kotak tengah.')],
        ];
    }

    private function submitWord(array $state, int $slot, array $placements): array
    {
        if (count($placements) === 0) {
            abort(422, 'Letakkan minimal satu tile.');
        }

        $playerIndex = $this->playerIndex($state, $slot);
        $rack = $state['players'][$playerIndex]['rack'];
        $rows = collect($placements)->pluck('row')->unique()->values();
        $cols = collect($placements)->pluck('col')->unique()->values();

        if ($rows->count() > 1 && $cols->count() > 1) {
            abort(422, 'Tile harus berada dalam satu baris atau satu kolom.');
        }

        $horizontal = $rows->count() === 1;
        $rackById = collect($rack)->keyBy('id');
        $board = $state['board'];
        $placedTiles = [];

        foreach ($placements as $placement) {
            if ($board[$placement['row']][$placement['col']] !== null) {
                abort(422, 'Kotak sudah terisi.');
            }

            if (!$rackById->has($placement['tile_id'])) {
                abort(422, 'Tile tidak ada di rack.');
            }

            $tile = $rackById->get($placement['tile_id']);
            $placedTiles[] = ['row' => $placement['row'], 'col' => $placement['col'], 'tile' => $tile];
            $board[$placement['row']][$placement['col']] = $tile;
        }

        if ($this->isBoardEmpty($state['board']) && !$this->coversCenter($placedTiles)) {
            abort(422, 'Kata pertama wajib melewati kotak tengah.');
        }

        $wordData = $this->extractMainWord($board, $placedTiles, $horizontal);

        if (strlen($wordData['word']) < 2) {
            abort(422, 'Kata minimal 2 huruf.');
        }

        if (!$this->isConnected($state['board'], $placedTiles) && !$this->isBoardEmpty($state['board'])) {
            abort(422, 'Kata baru harus tersambung dengan tile di papan.');
        }

        $usedIds = collect($placements)->pluck('tile_id')->all();
        $rack = array_values(array_filter($rack, fn ($tile) => !in_array($tile['id'], $usedIds, true)));
        [$rack, $state['bag']] = $this->drawTiles($state['bag'], $rack, 7 - count($rack));

        $score = $wordData['score'];
        $state['board'] = $board;
        $state['players'][$playerIndex]['rack'] = $rack;
        $state['players'][$playerIndex]['score'] += $score;
        $state['passes'] = 0;
        $state['logs'] = $this->prependLog($state, $state['players'][$playerIndex]['name'] . ' membuat kata "' . $wordData['word'] . '" +' . $score . ' poin.');

        if (count($rack) === 0 && count($state['bag']) === 0) {
            $state['status'] = 'finished';
            $state['message'] = 'Permainan selesai';

            return $state;
        }

        return $this->advanceTurn($state, $slot);
    }

    private function swapTiles(array $state, int $slot, array $tileIds): array
    {
        if (count($tileIds) === 0) {
            abort(422, 'Pilih tile yang ingin ditukar.');
        }

        if (count($state['bag']) < count($tileIds)) {
            abort(422, 'Tile di bag tidak cukup untuk ditukar.');
        }

        $playerIndex = $this->playerIndex($state, $slot);
        $rack = $state['players'][$playerIndex]['rack'];
        $swap = [];
        $keep = [];

        foreach ($rack as $tile) {
            if (in_array($tile['id'], $tileIds, true)) {
                $swap[] = $tile;
            } else {
                $keep[] = $tile;
            }
        }

        if (count($swap) !== count($tileIds)) {
            abort(422, 'Ada tile yang tidak ditemukan.');
        }

        [$keep, $state['bag']] = $this->drawTiles($state['bag'], $keep, count($swap));
        $state['bag'] = $this->shuffle(array_merge($state['bag'], $swap));
        $state['players'][$playerIndex]['rack'] = $keep;
        $state['passes'] = 0;
        $state['logs'] = $this->prependLog($state, $state['players'][$playerIndex]['name'] . ' menukar ' . count($swap) . ' tile.');

        return $this->advanceTurn($state, $slot);
    }

    private function passTurn(array $state, int $slot): array
    {
        $player = $this->playerBySlot($state, $slot);
        $state['passes'] = ($state['passes'] ?? 0) + 1;
        $state['logs'] = $this->prependLog($state, $player['name'] . ' pass.');

        if ($state['passes'] >= 4) {
            $state['status'] = 'finished';
            $state['message'] = 'Permainan selesai karena pass beruntun.';

            return $state;
        }

        return $this->advanceTurn($state, $slot);
    }

    private function advanceTurn(array $state, int $slot): array
    {
        $state['current_slot'] = $slot === 0 ? 1 : 0;
        $state['turn'] = ($state['turn'] ?? 1) + 1;
        $state['message'] = 'Giliran ' . $this->playerBySlot($state, $state['current_slot'])['name'];

        return $state;
    }

    private function extractMainWord(array $board, array $placedTiles, bool $horizontal): array
    {
        $fixed = $placedTiles[0];
        $row = $fixed['row'];
        $col = $fixed['col'];
        $dr = $horizontal ? 0 : 1;
        $dc = $horizontal ? 1 : 0;

        while ($row - $dr >= 0 && $col - $dc >= 0 && $board[$row - $dr][$col - $dc] !== null) {
            $row -= $dr;
            $col -= $dc;
        }

        $word = '';
        $score = 0;

        while ($row < 15 && $col < 15 && $board[$row][$col] !== null) {
            $word .= $board[$row][$col]['letter'];
            $score += $board[$row][$col]['score'];
            $row += $dr;
            $col += $dc;
        }

        return ['word' => $word, 'score' => $score];
    }

    private function isConnected(array $originalBoard, array $placedTiles): bool
    {
        foreach ($placedTiles as $placement) {
            foreach ([[1, 0], [-1, 0], [0, 1], [0, -1]] as [$dr, $dc]) {
                $row = $placement['row'] + $dr;
                $col = $placement['col'] + $dc;

                if ($row >= 0 && $row < 15 && $col >= 0 && $col < 15 && $originalBoard[$row][$col] !== null) {
                    return true;
                }
            }
        }

        return false;
    }

    private function coversCenter(array $placedTiles): bool
    {
        return collect($placedTiles)->contains(fn ($tile) => $tile['row'] === 7 && $tile['col'] === 7);
    }

    private function isBoardEmpty(array $board): bool
    {
        foreach ($board as $row) {
            foreach ($row as $cell) {
                if ($cell !== null) {
                    return false;
                }
            }
        }

        return true;
    }

    private function publicState(NottedScrabbleRoom $room, User $user): array
    {
        $state = $room->state ?? [];
        $slot = $this->slotForUser($state, $user);

        if (!$state || !isset($state['players'])) {
            return [
                'status' => $room->status,
                'message' => $state['message'] ?? 'Menunggu pemain kedua',
                'players' => [],
                'logs' => $state['logs'] ?? [],
            ];
        }

        $players = collect($state['players'])->map(function ($player) use ($slot) {
            $isMe = $player['slot'] === $slot;

            return [
                'slot' => $player['slot'],
                'user_id' => $player['user_id'],
                'name' => $player['name'],
                'score' => $player['score'],
                'is_me' => $isMe,
                'rack' => $isMe ? $player['rack'] : [],
                'rack_count' => count($player['rack']),
            ];
        })->values();

        return [
            'status' => $state['status'] ?? $room->status,
            'board' => $state['board'] ?? array_fill(0, 15, array_fill(0, 15, null)),
            'players' => $players,
            'my_slot' => $slot,
            'current_slot' => $state['current_slot'] ?? null,
            'is_my_turn' => $slot !== null && ($state['current_slot'] ?? null) === $slot,
            'bag_count' => count($state['bag'] ?? []),
            'turn' => $state['turn'] ?? 1,
            'message' => $state['message'] ?? '',
            'logs' => $state['logs'] ?? [],
        ];
    }

    private function roomSummary(NottedScrabbleRoom $room, User $user): array
    {
        return [
            'id' => $room->id,
            'code' => $room->code,
            'status' => $room->status,
            'host' => $room->host?->only(['id', 'name']),
            'guest' => $room->guest?->only(['id', 'name']),
            'is_mine' => $room->hasPlayer($user),
            'can_join' => $room->status === 'waiting' && !$room->guest_user_id && $room->host_user_id !== $user->id,
            'updated_at' => $room->last_activity_at?->diffForHumans(),
        ];
    }

    private function buildBag(): array
    {
        $distribution = [
            'A' => 9, 'I' => 9, 'N' => 6, 'R' => 6, 'S' => 6, 'T' => 6, 'U' => 6,
            'D' => 4, 'G' => 4, 'K' => 4, 'L' => 4, 'M' => 4,
            'B' => 3, 'H' => 3, 'O' => 3, 'P' => 3,
            'C' => 2, 'E' => 2, 'J' => 2, 'W' => 2,
            'F' => 1, 'V' => 1, 'Y' => 1, 'Z' => 1, 'Q' => 1, 'X' => 1,
        ];

        $bag = [];
        $id = 1;

        foreach ($distribution as $letter => $amount) {
            for ($i = 0; $i < $amount; $i++) {
                $bag[] = ['id' => 's' . $id++, 'letter' => $letter, 'score' => $this->letterScores[$letter]];
            }
        }

        return $bag;
    }

    private function drawTiles(array $bag, array $rack, int $amount): array
    {
        for ($i = 0; $i < $amount; $i++) {
            if (count($bag) === 0) {
                break;
            }

            $rack[] = array_pop($bag);
        }

        return [$rack, $bag];
    }

    private function authorizePlayer(NottedScrabbleRoom $room, User $user): void
    {
        if (!$room->hasPlayer($user)) {
            abort(403, 'Kamu bukan pemain di room ini.');
        }
    }

    private function slotForUser(array $state, User $user): ?int
    {
        foreach (($state['players'] ?? []) as $player) {
            if (($player['user_id'] ?? null) === $user->id) {
                return $player['slot'];
            }
        }

        return null;
    }

    private function playerIndex(array $state, int $slot): int
    {
        foreach ($state['players'] as $index => $player) {
            if ($player['slot'] === $slot) {
                return $index;
            }
        }

        abort(422, 'Pemain tidak ditemukan.');
    }

    private function playerBySlot(array $state, int $slot): array
    {
        return $state['players'][$this->playerIndex($state, $slot)];
    }

    private function prependLog(array $state, string $text): array
    {
        $logs = $state['logs'] ?? [];
        array_unshift($logs, $this->logText($text));

        return array_slice($logs, 0, 30);
    }

    private function logText(string $text): array
    {
        return ['id' => (string) Str::uuid(), 'text' => $text, 'time' => now()->format('H:i')];
    }

    private function shuffle(array $items): array
    {
        shuffle($items);

        return $items;
    }

    private function uniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (NottedScrabbleRoom::where('code', $code)->exists());

        return $code;
    }
}
