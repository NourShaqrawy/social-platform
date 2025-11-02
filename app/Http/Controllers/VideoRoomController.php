<?php

namespace App\Http\Controllers;

use App\Models\VideoRoom;
use Illuminate\Http\Request;
// app/Http/Controllers/VideoRoomController.php
class VideoRoomController extends Controller
{
    public function create(Request $request)
    {
        $caller = $request->user();
        $receiverId = $request->input('receiver_id');

        $roomName = 'room_' . uniqid();

        $room = VideoRoom::create([
            'room_name' => $roomName,
            'caller_id' => $caller->id,
            'receiver_id' => $receiverId,
        ]);

        return response()->json([
            'room' => $roomName,
            'url' => "https://meet.jit.si/$roomName"
        ]);
    }

    public function getRoom(Request $request)
    {
        $user = $request->user();

        $room = VideoRoom::where('receiver_id', $user->id)
            ->latest()
            ->first();

        if (!$room) {
            return response()->json(['message' => 'لا توجد مكالمات حالية'], 404);
        }

        return response()->json([
            'room' => $room->room_name,
            'url' => "https://meet.jit.si/{$room->room_name}"
        ]);
    }
}
