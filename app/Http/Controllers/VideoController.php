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



public function deleteFromCloudinary($publicId)
{
    try {
        // ✅ تهيئة Cloudinary باستخدام متغير البيئة
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

        // ✅ تنفيذ عملية الحذف
        $result = $cloudinary->uploadApi()->destroy($publicId, [
            'resource_type' => 'video',
        ]);

        // ✅ التحقق من نجاح العملية
        if ($result['result'] === 'ok') {
            return response()->json([
                'message' => '🗑️ تم حذف الفيديو من Cloudinary بنجاح',
            ]);
        } else {
            return response()->json([
                'message' => '⚠️ لم يتم حذف الفيديو من Cloudinary',
                'result'  => $result,
            ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'message' => '❌ حدث خطأ أثناء محاولة الحذف',
            'error'   => $e->getMessage(),
        ], 500);
    }
}



public function destroy_from_cloud($id)
{
    // ✅ جلب الفيديو الخاص بالمستخدم
    $video = Video::where('user_id', Auth::id())->findOrFail($id);

    // ✅ استخراج public_id من رابط Cloudinary
    $publicId = $this->extractPublicId($video->video_path);

    // ✅ حذف من Cloudinary
    try {
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
        $result = $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'video']);

        if ($result['result'] !== 'ok') {
            return response()->json([
                'message' => '⚠️ لم يتم حذف الفيديو من Cloudinary',
                'cloudinary_result' => $result,
            ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'message' => '❌ خطأ أثناء حذف الفيديو من Cloudinary',
            'error'   => $e->getMessage(),
        ], 500);
    }

    // ✅ حذف من قاعدة البيانات
    $video->delete();

    return response()->json([
        'message' => '🗑️ تم حذف الفيديو من Cloudinary وقاعدة البيانات بنجاح',
    ]);
}

private function extractPublicId($url) //public_id دالة مساعدة لاستخراج 
{
    $path = parse_url($url, PHP_URL_PATH); // /demo/video/upload/v123456/user_videos/myvideo.mp4
    $segments = explode('/', $path);
    $filename = end($segments); // myvideo.mp4
    $nameOnly = pathinfo($filename, PATHINFO_FILENAME); // myvideo
    return 'user_videos/' . $nameOnly;
}



}
