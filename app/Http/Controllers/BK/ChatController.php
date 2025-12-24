<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\BKChatRoom;
use App\Models\BKChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->hasAnyRole(['Guru BK', 'guru bk'])) {
            $rooms = BKChatRoom::with(['siswa', 'messages' => fn($q) => $q->latest()->limit(1)])
                ->where('guru_bk_user_id', $user->id)
                ->orderBy('last_message_at', 'desc')
                ->get();
            return view('pages.bk.chat.index', compact('rooms'));
        } elseif ($user->hasAnyRole(['Siswa', 'siswa'])) {
            $gurusBK = User::role(['Guru BK', 'guru bk'])->get();
            $rooms = BKChatRoom::with(['guruBK', 'messages' => fn($q) => $q->latest()->limit(1)])
                ->where('siswa_user_id', $user->id)
                ->get();
            return view('pages.siswa.chat.index', compact('rooms', 'gurusBK'));
        }
        
        abort(403);
    }

    public function show(BKChatRoom $room)
    {
        $userId = Auth::id();
        if ($room->siswa_user_id !== $userId && $room->guru_bk_user_id !== $userId) {
            abort(403);
        }

        $messages = $room->messages()->with('sender')->oldest()->get();
        
        // Mark as read
        $room->messages()->where('sender_id', '!=', $userId)->update(['is_read' => true]);

        return response()->json([
            'room' => $room->load(['siswa', 'guruBK']),
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request, BKChatRoom $room)
    {
        $request->validate([
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:20480', // 20MB limit
        ]);
        
        $userId = Auth::id();
        $type = 'text';
        $filePath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $mime = $file->getMimeType();
            
            if (str_contains($mime, 'image')) {
                $type = 'image';
            } elseif (str_contains($mime, 'video')) {
                $type = 'video';
            } else {
                $type = 'file';
            }

            $filePath = $file->store('chat_files', 'public');
        }

        $message = BKChatMessage::create([
            'chat_room_id' => $room->id,
            'sender_id' => $userId,
            'message' => $request->message,
            'type' => $type,
            'file_path' => $filePath,
        ]);

        $room->update(['last_message_at' => now()]);

        return response()->json($message->load('sender'));
    }

    public function startChat($guru_bk_user_id)
    {
        $user = Auth::user();
        
        $room = BKChatRoom::firstOrCreate([
            'siswa_user_id' => $user->id,
            'guru_bk_user_id' => $guru_bk_user_id
        ]);

        $route = $user->hasAnyRole(['Siswa', 'siswa']) ? 'siswa.chat.index' : 'bk.chat.index';
        return redirect()->route($route, ['room_id' => $room->id]);
    }

    public function getUnreadCount()
    {
        $userId = Auth::id();
        $count = BKChatMessage::whereHas('room', function($q) use ($userId) {
            $q->where('siswa_user_id', $userId)
              ->orWhere('guru_bk_user_id', $userId);
        })
        ->where('sender_id', '!=', $userId)
        ->where('is_read', false)
        ->count();

        return response()->json(['unread_count' => $count]);
    }
}
