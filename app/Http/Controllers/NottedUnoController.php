<?php

namespace App\Http\Controllers;

use App\Models\NottedUnoRoom;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NottedUnoController extends Controller
{
    private array $colors = ['red', 'yellow', 'green', 'blue'];

    public function rooms(Request $request): JsonResponse
    {
        $user = $request->user();

        $rooms = NottedUnoRoom::with(['host:id,name', 'guest:id,name'])
            ->where(function ($query) use ($user) {
                $query->where('status', 'waiting')
                    ->orWhere('host_user_id', $user->id)
                    ->orWhere('guest_user_id', $user->id);
            })
            ->latest('last_activity_at')
            ->take(16)
            ->get()
            ->map(fn (NottedUnoRoom $room) => $this->roomSummary($room, $user));

        return response()->json(['rooms' => $rooms]);
    }

    public function create(Request $request): JsonResponse
    {
        $room = NottedUnoRoom::create([
            'code' => $this->uniqueCode(),
            'host_user_id' => $request->user()->id,
            'status' => 'waiting',
            'state' => [
                'logs' => [$this->logText($request->user()->name . ' membuat room.')],
                'message' => 'Menunggu pemain kedua',
            ],
            'last_activity_at' => now(),
        ]);

        $room->load(['host:id,name', 'guest:id,name']);

        return response()->json([
            'room' => $this->roomSummary($room, $request->user()),
            'state' => $this->publicState($room, $request->user()),
        ]);
    }

    public function join(Request $request, NottedUnoRoom $room): JsonResponse
    {
        $user = $request->user();

        if ($room->host_user_id === $user->id || $room->guest_user_id === $user->id) {
            return response()->json([
                'room' => $this->roomSummary($room->load(['host:id,name', 'guest:id,name']), $user),
                'state' => $this->publicState($room, $user),
            ]);
        }

        if ($room->status !== 'waiting' || $room->guest_user_id) {
            return response()->json(['message' => 'Room sudah penuh atau permainan sudah berjalan.'], 422);
        }

        $room = DB::transaction(function () use ($room, $user) {
            $locked = NottedUnoRoom::whereKey($room->id)->lockForUpdate()->firstOrFail();

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

    public function state(Request $request, NottedUnoRoom $room): JsonResponse
    {
        $this->authorizePlayer($room, $request->user());

        return response()->json([
            'room' => $this->roomSummary($room->load(['host:id,name', 'guest:id,name']), $request->user()),
            'state' => $this->publicState($room, $request->user()),
        ]);
    }

    public function action(Request $request, NottedUnoRoom $room): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|string|in:play,draw,pass,uno,restart',
            'card_id' => 'nullable|string',
            'color' => 'nullable|string|in:red,yellow,green,blue',
        ]);

        $user = $request->user();
        $room = DB::transaction(function () use ($room, $user, $validated) {
            $locked = NottedUnoRoom::whereKey($room->id)->lockForUpdate()->firstOrFail();
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

            if ($slot === null || $state['current_slot'] !== $slot) {
                abort(422, 'Belum giliran kamu.');
            }

            match ($validated['action']) {
                'play' => $state = $this->playCard($state, $slot, $validated['card_id'] ?? null, $validated['color'] ?? null),
                'draw' => $state = $this->drawAction($state, $slot),
                'pass' => $state = $this->passAction($state, $slot),
                'uno' => $state = $this->unoAction($state, $slot),
            };

            if (($state['status'] ?? 'active') === 'finished') {
                $winner = collect($state['players'])->firstWhere('slot', $state['winner_slot']);
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
        $deck = $this->shuffle($this->buildDeck());
        $players = [
            ['slot' => 0, 'user_id' => $host->id, 'name' => $host->name, 'cards' => []],
            ['slot' => 1, 'user_id' => $guest->id, 'name' => $guest->name, 'cards' => []],
        ];

        for ($i = 0; $i < 7; $i++) {
            foreach ($players as $index => $player) {
                $players[$index]['cards'][] = array_pop($deck);
            }
        }

        $first = array_pop($deck);
        while (($first['color'] ?? null) === 'wild') {
            array_unshift($deck, $first);
            $first = array_pop($deck);
        }

        return [
            'players' => $players,
            'deck' => $deck,
            'discard' => [$first],
            'current_slot' => 0,
            'direction' => 1,
            'current_color' => $first['color'],
            'turn_drawn' => false,
            'uno_called' => [0 => false, 1 => false],
            'status' => 'active',
            'winner_slot' => null,
            'message' => 'Giliran ' . $host->name,
            'logs' => [$this->logText('Permainan dimulai. ' . $host->name . ' jalan dulu.')],
        ];
    }

    private function playCard(array $state, int $slot, ?string $cardId, ?string $color): array
    {
        if (!$cardId) {
            abort(422, 'Kartu tidak valid.');
        }

        $playerIndex = $this->playerIndex($state, $slot);
        $cardIndex = collect($state['players'][$playerIndex]['cards'])->search(fn ($card) => $card['id'] === $cardId);

        if ($cardIndex === false) {
            abort(422, 'Kartu tidak ditemukan.');
        }

        $card = $state['players'][$playerIndex]['cards'][$cardIndex];

        if (!$this->canPlay($state, $card)) {
            abort(422, 'Kartu tidak cocok.');
        }

        if (($card['color'] ?? null) === 'wild' && !$color) {
            abort(422, 'Pilih warna untuk kartu wild.');
        }

        array_splice($state['players'][$playerIndex]['cards'], $cardIndex, 1);
        $state['discard'][] = $card;
        $state['current_color'] = $card['color'] === 'wild' ? $color : $card['color'];
        $state['turn_drawn'] = false;
        $state['logs'] = $this->prependLog($state, $state['players'][$playerIndex]['name'] . ' memainkan ' . $this->cardText($card) . '.');

        if (count($state['players'][$playerIndex]['cards']) === 1 && empty($state['uno_called'][$slot])) {
            $state = $this->drawToPlayer($state, $slot, 2);
            $state['logs'] = $this->prependLog($state, $state['players'][$playerIndex]['name'] . ' lupa UNO. Penalti 2 kartu.');
        }

        $state['uno_called'][$slot] = false;

        if (count($state['players'][$playerIndex]['cards']) === 0) {
            $state['status'] = 'finished';
            $state['winner_slot'] = $slot;
            $state['message'] = $state['players'][$playerIndex]['name'] . ' menang';
            $state['logs'] = $this->prependLog($state, $state['players'][$playerIndex]['name'] . ' memenangkan UNO Stella.');

            return $state;
        }

        return $this->applyEffect($state, $slot, $card);
    }

    private function drawAction(array $state, int $slot): array
    {
        if (!empty($state['turn_drawn'])) {
            abort(422, 'Kamu sudah mengambil kartu pada giliran ini.');
        }

        $state = $this->drawToPlayer($state, $slot, 1);
        $state['turn_drawn'] = true;
        $player = $this->playerBySlot($state, $slot);
        $state['message'] = 'Kartu diambil. Mainkan jika cocok atau pass.';
        $state['logs'] = $this->prependLog($state, $player['name'] . ' mengambil 1 kartu.');

        return $state;
    }

    private function passAction(array $state, int $slot): array
    {
        if (empty($state['turn_drawn'])) {
            abort(422, 'Ambil kartu dulu sebelum pass.');
        }

        $state['turn_drawn'] = false;
        $state['current_slot'] = $this->nextSlot($state, $slot);
        $state['message'] = 'Giliran ' . $this->playerBySlot($state, $state['current_slot'])['name'];

        return $state;
    }

    private function unoAction(array $state, int $slot): array
    {
        $player = $this->playerBySlot($state, $slot);

        if (count($player['cards']) !== 2) {
            abort(422, 'UNO hanya bisa ditekan saat tersisa 2 kartu.');
        }

        $state['uno_called'][$slot] = true;
        $state['message'] = $player['name'] . ' siap UNO';
        $state['logs'] = $this->prependLog($state, $player['name'] . ' menekan UNO.');

        return $state;
    }

    private function applyEffect(array $state, int $slot, array $card): array
    {
        $type = $card['type'];
        $target = $this->nextSlot($state, $slot);

        if (in_array($type, ['skip', 'reverse'], true)) {
            $state['direction'] *= $type === 'reverse' ? -1 : 1;
            $state['current_slot'] = $slot;
            $state['logs'] = $this->prependLog($state, 'Giliran lawan dilewati.');
        } elseif ($type === 'draw2') {
            $state = $this->drawToPlayer($state, $target, 2);
            $state['current_slot'] = $slot;
            $state['logs'] = $this->prependLog($state, $this->playerBySlot($state, $target)['name'] . ' mengambil 2 kartu.');
        } elseif ($type === 'wild4') {
            $state = $this->drawToPlayer($state, $target, 4);
            $state['current_slot'] = $slot;
            $state['logs'] = $this->prependLog($state, $this->playerBySlot($state, $target)['name'] . ' mengambil 4 kartu.');
        } else {
            $state['current_slot'] = $target;
        }

        $state['message'] = 'Giliran ' . $this->playerBySlot($state, $state['current_slot'])['name'];

        return $state;
    }

    private function drawToPlayer(array $state, int $slot, int $amount): array
    {
        $playerIndex = $this->playerIndex($state, $slot);

        for ($i = 0; $i < $amount; $i++) {
            if (count($state['deck']) === 0) {
                $state = $this->recycleDeck($state);
            }

            if (count($state['deck']) > 0) {
                $state['players'][$playerIndex]['cards'][] = array_pop($state['deck']);
            }
        }

        return $state;
    }

    private function recycleDeck(array $state): array
    {
        $top = array_pop($state['discard']);
        $state['deck'] = $this->shuffle($state['discard']);
        $state['discard'] = [$top];

        return $state;
    }

    private function buildDeck(): array
    {
        $deck = [];
        $id = 1;

        foreach ($this->colors as $color) {
            $deck[] = ['id' => 'm' . $id++, 'color' => $color, 'value' => '0', 'type' => 'number'];

            for ($number = 1; $number <= 9; $number++) {
                $deck[] = ['id' => 'm' . $id++, 'color' => $color, 'value' => (string) $number, 'type' => 'number'];
                $deck[] = ['id' => 'm' . $id++, 'color' => $color, 'value' => (string) $number, 'type' => 'number'];
            }

            foreach (['skip', 'reverse', 'draw2'] as $type) {
                $deck[] = ['id' => 'm' . $id++, 'color' => $color, 'value' => $type, 'type' => $type];
                $deck[] = ['id' => 'm' . $id++, 'color' => $color, 'value' => $type, 'type' => $type];
            }
        }

        for ($i = 0; $i < 4; $i++) {
            $deck[] = ['id' => 'm' . $id++, 'color' => 'wild', 'value' => 'wild', 'type' => 'wild'];
            $deck[] = ['id' => 'm' . $id++, 'color' => 'wild', 'value' => 'wild4', 'type' => 'wild4'];
        }

        return $deck;
    }

    private function canPlay(array $state, array $card): bool
    {
        $top = $state['discard'][count($state['discard']) - 1];

        return $card['color'] === 'wild'
            || $card['color'] === $state['current_color']
            || $card['value'] === $top['value'];
    }

    private function publicState(NottedUnoRoom $room, User $user): array
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
                'is_me' => $isMe,
                'cards' => $isMe ? $player['cards'] : [],
                'cards_count' => count($player['cards']),
            ];
        })->values();

        return [
            'status' => $state['status'] ?? $room->status,
            'players' => $players,
            'my_slot' => $slot,
            'current_slot' => $state['current_slot'] ?? null,
            'is_my_turn' => $slot !== null && ($state['current_slot'] ?? null) === $slot,
            'current_color' => $state['current_color'] ?? 'red',
            'direction' => $state['direction'] ?? 1,
            'deck_count' => count($state['deck'] ?? []),
            'top_card' => collect($state['discard'] ?? [])->last(),
            'turn_drawn' => (bool) ($state['turn_drawn'] ?? false),
            'message' => $state['message'] ?? '',
            'winner_slot' => $state['winner_slot'] ?? null,
            'logs' => $state['logs'] ?? [],
        ];
    }

    private function roomSummary(NottedUnoRoom $room, User $user): array
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

    private function authorizePlayer(NottedUnoRoom $room, User $user): void
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

    private function nextSlot(array $state, int $from): int
    {
        $slots = collect($state['players'])->pluck('slot')->values();
        $index = $slots->search($from);
        $nextIndex = ($index + ($state['direction'] ?? 1) + $slots->count()) % $slots->count();

        return $slots[$nextIndex];
    }

    private function cardText(array $card): string
    {
        $label = [
            'skip' => 'Skip',
            'reverse' => 'Reverse',
            'draw2' => '+2',
            'wild' => 'Wild',
            'wild4' => '+4',
        ][$card['value']] ?? $card['value'];

        return ($card['color'] === 'wild' ? 'Wild' : ucfirst($card['color'])) . ' ' . $label;
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

    private function shuffle(array $cards): array
    {
        shuffle($cards);

        return $cards;
    }

    private function uniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (NottedUnoRoom::where('code', $code)->exists());

        return $code;
    }
}
