<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Video;
use Cloudinary\Cloudinary;

class VideoController extends Controller
{
    public function index()
{
    $videos = Video::where('user_id', Auth::id())->latest()->get();

    return response()->json([
        'videos' => $videos,
    ]);
}

public function show($id)
{
    $video = Video::where('user_id', Auth::id())->findOrFail($id);

    return response()->json([
        'video' => $video,
    ]);
}

    /**
     * رفع فيديو إلى Cloudinary باستخدام env('CLOUDINARY_URL') مباشرة
     */
    public function upload(Request $request)
    {
        // ✅ التحقق من صحة المدخلات
        $request->validate([
            'title'       => 'required|string|max:255',
            'video'       => 'required|mimetypes:video/mp4,video/avi,video/mov|max:51200',
            'description' => 'nullable|string',
        ]);

        // ✅ تهيئة كائن Cloudinary باستخدام متغير البيئة مباشرة
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

        // ✅ رفع الفيديو باستخدام uploadApi
        $uploaded = $cloudinary->uploadApi()->upload(
            $request->file('video')->getRealPath(),
            [
                'resource_type' => 'video',
                'folder'        => 'user_videos',
            ]
        );

        // ✅ استخراج الرابط الآمن
        $videoUrl = $uploaded['secure_url'] ?? null;

        // ✅ إنشاء سجل في قاعدة البيانات
        $video = Video::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'video_path'  => $videoUrl,
            'thumbnail'   => null,
            'duration'    => null,
            'description' => $request->description,
        ]);

        // ✅ إرجاع استجابة JSON
        return response()->json([
            'message' => '✅ تم رفع الفيديو بنجاح باستخدام Cloudinary API',
            'video'   => $video,
        ]);
    }
    public function update(Request $request, $id)
{
    $request->validate([
        'title'       => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $video = Video::where('user_id', Auth::id())->findOrFail($id);

    $video->update([
        'title'       => $request->title ?? $video->title,
        'description' => $request->description ?? $video->description,
    ]);

    return response()->json([
        'message' => '✅ تم تحديث بيانات الفيديو بنجاح',
        'video'   => $video,
    ]);
}
public function destroy($id)
{
    $video = Video::where('user_id', Auth::id())->findOrFail($id);

    $video->delete();

    return response()->json([
        'message' => '🗑️ تم حذف الفيديو بنجاح',
    ]);
}

}
