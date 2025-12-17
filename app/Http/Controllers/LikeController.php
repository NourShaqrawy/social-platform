<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;

class LikeController extends Controller
{
    /**
     * إضافة لايك
     */
    public function store(Request $request)
    {
        $request->validate([
            'likeable_id'   => 'required|integer',
            'likeable_type' => 'required|string',
        ]);

        $like = Like::firstOrCreate([
            'user_id'       => auth()->id(),
            'likeable_id'   => $request->likeable_id,
            'likeable_type' => $request->likeable_type,
        ]);

        return response()->json([
            'message' => '✅ تم إضافة اللايك بنجاح',
            'like'    => $like,
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'likeable_id'   => 'required|integer',
            'likeable_type' => 'required|string',
        ]);

        $deleted = Like::where('user_id', auth()->id())
            ->where('likeable_id', $request->likeable_id)
            ->where('likeable_type', $request->likeable_type)
            ->delete();

        return response()->json([
            'message' => $deleted ? '🗑️ تم حذف اللايك' : '⚠️ لا يوجد لايك للحذف',
        ]);
    }
}
