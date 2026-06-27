<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\PrakerinKonsultasiMessage;
use App\Models\PrakerinPenempatan;
use App\Models\PrakerinRombel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KonsultasiController extends Controller
{
    public function index(Request $request)
    {
        [$rombels, $activeRombel] = $this->availableRombels($request);
        abort_if(! $activeRombel, 403, 'Anda belum terhubung ke rombel prakerin aktif.');

        $participants = $this->participants($activeRombel);
        $receiverId = $request->integer('receiver_id') ?: null;
        $receiver = $receiverId ? $participants->firstWhere('id', $receiverId) : null;

        $messages = PrakerinKonsultasiMessage::with(['sender.masterSiswa', 'sender.masterGuru', 'receiver'])
            ->where('prakerin_rombel_id', $activeRombel->id)
            ->when($receiver, function ($query) use ($receiver) {
                $query->where('type', 'private')
                    ->where(function ($private) use ($receiver) {
                        $private->where(function ($q) use ($receiver) {
                            $q->where('sender_id', Auth::id())->where('receiver_id', $receiver->id);
                        })->orWhere(function ($q) use ($receiver) {
                            $q->where('sender_id', $receiver->id)->where('receiver_id', Auth::id());
                        });
                    });
            }, fn ($query) => $query->where('type', 'group'))
            ->oldest()
            ->get();

        return view('pages.prakerin.konsultasi.index', compact('rombels', 'activeRombel', 'participants', 'receiver', 'messages'));
    }

    public function store(Request $request)
    {
        [$rombels, $activeRombel] = $this->availableRombels($request);
        abort_if(! $activeRombel, 403);

        $data = $request->validate([
            'type' => 'required|in:group,private',
            'receiver_id' => 'nullable|required_if:type,private|exists:users,id',
            'message' => 'required|string|max:3000',
        ]);

        if (($data['type'] ?? 'group') === 'private') {
            $allowed = $this->participants($activeRombel)->pluck('id')->contains((int) $data['receiver_id']);
            abort_unless($allowed, 403);
        }

        PrakerinKonsultasiMessage::create([
            'prakerin_rombel_id' => $activeRombel->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $data['type'] === 'private' ? $data['receiver_id'] : null,
            'type' => $data['type'],
            'message' => $data['message'],
        ]);

        return back();
    }

    private function availableRombels(Request $request): array
    {
        $user = Auth::user();

        if ($user->masterSiswa) {
            $penempatan = PrakerinPenempatan::with('rombelPkl')
                ->where('master_siswa_id', $user->masterSiswa->id)
                ->where('status', 'aktif')
                ->first();
            $rombels = $penempatan?->rombelPkl ? collect([$penempatan->rombelPkl]) : collect();
        } else {
            $rombels = PrakerinRombel::whereHas('pembimbingInternal', fn ($q) => $q->where('master_guru_id', $user->masterGuru?->id))
                ->where('status', 'aktif')
                ->orderBy('nama_rombel')
                ->get();
        }

        $activeRombel = $request->filled('rombel_id')
            ? $rombels->firstWhere('id', (int) $request->rombel_id)
            : $rombels->first();

        return [$rombels, $activeRombel];
    }

    private function participants(PrakerinRombel $rombel)
    {
        $studentUsers = PrakerinPenempatan::with('siswa.user')
            ->where('prakerin_rombel_id', $rombel->id)
            ->where('status', 'aktif')
            ->get()
            ->pluck('siswa.user')
            ->filter();

        $teacherUser = $rombel->pembimbingInternal?->guru?->user;

        return $studentUsers
            ->when($teacherUser, fn ($items) => $items->push($teacherUser))
            ->unique('id')
            ->values()
            ->reject(fn (User $user) => $user->id === Auth::id());
    }
}
