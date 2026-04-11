<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'post'])->latest();

        if ($request->filled('post_id')) {
            $query->where('post_id', $request->input('post_id'));
        }

        $comments = $query->get();

        return response()->json(['comments' => $comments]);
    }

    public function show($id)
    {
        $comment = Comment::with(['user', 'post'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json(['comment' => $comment]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'body' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $request->input('post_id'),
            'body' => $request->input('body'),
        ]);

        return response()->json(['message' => '✅ تم إنشاء التعليق', 'comment' => $comment], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['body' => 'required|string']);

        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);

        $comment->body = $request->input('body');
        $comment->save();

        return response()->json(['message' => '✏️ تم تعديل التعليق', 'comment' => $comment]);
    }

    public function destroy($id)
    {
        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);
        $comment->delete();

        return response()->json(['message' => '🗑️ تم حذف التعليق']);
    }
}

