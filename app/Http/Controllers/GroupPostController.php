<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GroupPost;
use App\Traits\HandlesImageUpload;
use App\Traits\HandlesVideoUpload;
use App\Traits\HandlesCloudinaryDeletion;
use App\Traits\AssignsFeeling;

class GroupPostController extends Controller
{
         use HandlesImageUpload, HandlesVideoUpload, HandlesCloudinaryDeletion,AssignsFeeling;
    /**
     * 📃 عرض كل منشورات المستخدم داخل المجموعات
     */
    public function index()
    {
        $posts = GroupPost::where('user_id', Auth::id())->latest()->get();

        return response()->json([
            'posts' => $posts,
        ]);
    }

    public function show($id)
{
    // جلب المنشور من قاعدة البيانات
    $post = GroupPost::findOrFail($id);

    // التحقق من ملكية المستخدم
    if ($post->user_id !== Auth::id()) {
        return response()->json([
            'message' => '❌ لا يمكنك عرض هذا المنشور لأنه لا يخصك'
        ], 403);
    }

    // إرجاع المنشور
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
            'group_id' => 'required|exists:groups,id',
            'content'  => 'required|string',
            'media'    => 'required|file|max:51200', // صورة أو فيديو
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

        $post = GroupPost::create([
            'group_id'  => $request->group_id,
            'user_id'   => Auth::id(),
            'content'   => $request->content,
            'media_url' => $mediaUrl,
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
            'media_url' => 'nullable|url',
        ]);

        $post = GroupPost::where('user_id', Auth::id())->findOrFail($id);

        $post->update([
            'content'   => $request->content ?? $post->content,
        ]);

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

    public function destroy_from_cloud($id)
    {
        $post = GroupPost::where('user_id', Auth::id())->findOrFail($id);

        // حذف الوسائط من Cloudinary إن وجدت
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
