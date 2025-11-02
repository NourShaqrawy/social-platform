<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Traits\HandlesImageUpload;
use App\Traits\HandlesVideoUpload;
use App\Traits\HandlesCloudinaryDeletion;
use App\Traits\AssignsFeeling;
use App\Traits\StoresLocation;

class PostController extends Controller
{
    use HandlesImageUpload, HandlesVideoUpload, HandlesCloudinaryDeletion, AssignsFeeling, StoresLocation;

    public function index()
    {
        $posts = Post::with(['feeling', 'location'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json(['posts' => $posts]);
    }

    public function show($id)
    {
        $post = Post::with(['feeling', 'location'])->findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => '❌ لا يمكنك عرض هذا المنشور'], 403);
        }

        return response()->json(['post' => $post]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
            'media' => 'required|file|max:51200',
            'visibility' => 'nullable|in:public,friends,private',
            'feeling.name' => 'nullable|string',
            'feeling.emoji' => 'nullable|string',
            'feeling.description' => 'nullable|string',
            'location.city' => 'nullable|string',
            'location.country' => 'nullable|string',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
        ]);

        $mediaFile = $request->file('media');
        $mimeType = $mediaFile->getMimeType();
        $mediaUrl = str_starts_with($mimeType, 'image/')
            ? $this->uploadImageToCloudinary($mediaFile, 'posts')
            : (str_starts_with($mimeType, 'video/')
                ? $this->uploadVideoToCloudinary($mediaFile, 'posts')
                : null);

        if (!$mediaUrl) {
            return response()->json(['message' => '❌ الملف يجب أن يكون صورة أو فيديو فقط'], 422);
        }

        $feelingId = $request->filled('feeling.name')
            ? $this->assignFeeling(
                $request->input('feeling.name'),
                $request->input('feeling.emoji'),
                $request->input('feeling.description')
            )->id
            : null;

        $locationId = ($request->filled('location.city') || $request->filled('location.country'))
            ? $this->storeLocation($request->input('location'))->id
            : null;

        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'media_url' => $mediaUrl,
            'visibility' => $request->visibility ?? 'public',
            'feeling_id' => $feelingId,
            'location_id' => $locationId,
        ]);

        return response()->json(['message' => '✅ تم إنشاء المنشور', 'post' => $post]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'nullable|string',
            'visibility' => 'nullable|in:public,friends,private',
            'feeling.name' => 'nullable|string',
            'feeling.emoji' => 'nullable|string',
            'feeling.description' => 'nullable|string',
            'location.city' => 'nullable|string',
            'location.country' => 'nullable|string',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
        ]);

        $post = Post::where('user_id', Auth::id())->findOrFail($id);

        if ($request->filled('feeling.name')) {
            $post->feeling_id = $this->assignFeeling(
                $request->input('feeling.name'),
                $request->input('feeling.emoji'),
                $request->input('feeling.description')
            )->id;
        }

        if ($request->filled('location.city') || $request->filled('location.country')) {
            $post->location_id = $this->storeLocation($request->input('location'))->id;
        }

        $post->content = $request->content ?? $post->content;
        $post->visibility = $request->visibility ?? $post->visibility;
        $post->save();

        return response()->json(['message' => '✏️ تم تعديل المنشور', 'post' => $post]);
    }

    public function destroy($id)
    {
        $post = Post::where('user_id', Auth::id())->findOrFail($id);
        $post->delete();

        return response()->json(['message' => '🗑️ تم حذف المنشور']);
    }

    public function destroy_from_cloud($id)
    {
        $post = Post::where('user_id', Auth::id())->findOrFail($id);

        if ($post->media_url) {
            $type = str_contains($post->media_url, '/video/upload/') ? 'video' : 'image';
            $this->deleteFromCloudinary($post->media_url, $type, 'posts');
        }

        $post->delete();

        return response()->json(['message' => '🗑️ تم حذف المنشور والوسائط من Cloudinary']);
    }
}
