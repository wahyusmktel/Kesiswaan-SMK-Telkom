<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BKChatMessage;
use App\Models\BKChatRoom;
use App\Models\BKPembinaanRutin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BKController extends Controller
{
    /**
     * Get counseling history for student.
     */
    public function getHistory(Request $request)
    {
        $user = $request->user();
        $masterSiswa = $user->masterSiswa;

        if (!$masterSiswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan'], 404);
        }

        $history = BKPembinaanRutin::with('guruBK')
            ->where('master_siswa_id', $masterSiswa->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    /**
     * Get chat rooms for current user.
     */
    public function getChatRooms(Request $request)
    {
        $user = $request->user();
        
        $query = BKChatRoom::with(['siswa.masterSiswa', 'guruBK.masterGuru']);

        if ($user->hasRole('siswa')) {
            $query->where('siswa_user_id', $user->id);
        } elseif ($user->hasRole('guru_bk')) {
            $query->where('guru_bk_user_id', $user->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $rooms = $query->orderBy('last_message_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $rooms,
        ]);
    }

    /**
     * Get messages for a specific chat room.
     */
    public function getMessages(Request $request, $roomId)
    {
        $room = BKChatRoom::find($roomId);

        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Room tidak ditemukan'], 404);
        }

        // Authorization check
        $user = $request->user();
        if ($room->siswa_user_id !== $user->id && $room->guru_bk_user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $messages = $room->messages()->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    /**
     * Send a new message.
     */
    public function sendMessage(Request $request, $roomId)
    {
        $room = BKChatRoom::find($roomId);

        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Room tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required_without:file|string|nullable',
            'file' => 'nullable|file|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $filePath = null;
        $type = 'text';

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('bk-chat', 'public');
            $type = 'file';
        }

        $msg = BKChatMessage::create([
            'chat_room_id' => $room->id,
            'sender_id' => $request->user()->id,
            'message' => $request->message,
            'type' => $type,
            'file_path' => $filePath,
        ]);

        $room->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => $msg,
        ]);
    }
}
