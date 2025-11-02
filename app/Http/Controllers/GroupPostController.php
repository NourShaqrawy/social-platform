<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GroupPost;
use App\Traits\HandlesImageUpload;
use App\Traits\HandlesVideoUpload;
use App\Traits\HandlesCloudinaryDeletion;
use App\Traits\AssignsFeeling;
use App\Traits\StoresLocation;

class GroupPostController extends Controller
{
    use HandlesImageUpload, HandlesVideoUpload, HandlesCloudinaryDeletion, AssignsFeeling, StoresLocation;

    /**
     * 📃 عرض كل منشورات المستخدم داخل المجموعات
     */
    public function index()
    {
        $posts = GroupPost::with(['feeling', 'location'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'posts' => $posts,
        ]);
    }

    /**
     * 🔍 عرض منشور واحد
     */
    public function show($id)
    {
        $post = GroupPost::with(['feeling', 'location'])->findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'message' => '❌ لا يمكنك عرض هذا المنشور لأنه لا يخصك'
            ], 403);
        }

        return response()->json([
            'post' => $post,
        ]);
    }

    /**
     * 📥 إنشاء منشور جديد داخل مجموعة
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_id'   => 'required|exists:groups,id',
            'content'    => 'required|string',
            'media'      => 'required|file|max:51200',
            'feeling.name' => 'nullable|string',
            'feeling.emoji' => 'nullable|string',
            'feeling.description' => 'nullable|string',
            'location.city' => 'nullable|string',
            'location.country' => 'nullable|string',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
        ]);

        $mediaFile = $request->file('media');
        $mimeType  = $mediaFile->getMimeType();
        $mediaUrl  = null;

        if (str_starts_with($mimeType, 'image/')) {
            $mediaUrl = $this->uploadImageToCloudinary($mediaFile, 'group_posts');
        } elseif (str_starts_with($mimeType, 'video/')) {
            $mediaUrl = $this->uploadVideoToCloudinary($mediaFile, 'group_posts');
        } else {
            return response()->json([
                'message' => '❌ الملف المرفوع يجب أن يكون صورة أو فيديو فقط',
            ], 422);
        }

        $feelingId = null;
        if ($request->filled('feeling.name')) {
            $feeling = $this->assignFeeling(
                $request->input('feeling.name'),
                $request->input('feeling.emoji'),
                $request->input('feeling.description')
            );
            $feelingId = $feeling->id;
        }

        $locationId = null;
        if ($request->filled('location.city') || $request->filled('location.country')) {
            $location = $this->storeLocation($request->input('location'));
            $locationId = $location->id;
        }

        $post = GroupPost::create([
            'group_id'    => $request->group_id,
            'user_id'     => Auth::id(),
            'content'     => $request->content,
            'media_url'   => $mediaUrl,
            'feeling_id'  => $feelingId,
            'location_id' => $locationId,
        ]);

        return response()->json([
            'message' => '✅ تم إنشاء المنشور ورفع الوسائط بنجاح',
            'post'    => $post,
        ]);
    }

    /**
     * ✏️ تعديل منشور
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content'   => 'sometimes|required|string',
            'feeling.name' => 'nullable|string',
            'feeling.emoji' => 'nullable|string',
            'feeling.description' => 'nullable|string',
            'location.city' => 'nullable|string',
            'location.country' => 'nullable|string',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
        ]);

        $post = GroupPost::where('user_id', Auth::id())->findOrFail($id);

        if ($request->filled('feeling.name')) {
            $feeling = $this->assignFeeling(
                $request->input('feeling.name'),
                $request->input('feeling.emoji'),
                $request->input('feeling.description')
            );
            $post->feeling_id = $feeling->id;
        }

        if ($request->filled('location.city') || $request->filled('location.country')) {
            $location = $this->storeLocation($request->input('location'));
            $post->location_id = $location->id;
        }

        $post->content = $request->content ?? $post->content;
        $post->save();

        return response()->json([
            'message' => '✏️ تم تعديل المنشور بنجاح',
            'post'    => $post,
        ]);
    }

    /**
     * 🗑️ حذف منشور
     */
    public function destroy($id)
    {
        $post = GroupPost::where('user_id', Auth::id())->findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => '🗑️ تم حذف المنشور بنجاح',
        ]);
    }

    /**
     * 🗑️ حذف منشور والوسائط من Cloudinary
     */
    public function destroy_from_cloud($id)
    {
        $post = GroupPost::where('user_id', Auth::id())->findOrFail($id);

        if ($post->media_url) {
            $resourceType = str_contains($post->media_url, '/video/upload/') ? 'video' : 'image';
            $this->deleteFromCloudinary($post->media_url, $resourceType, 'group_posts');
        }

        $post->delete();

        return response()->json([
            'message' => '🗑️ تم حذف المنشور والوسائط من Cloudinary بنجاح',
        ]);
    }
}
