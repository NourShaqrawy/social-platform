<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class VideoController extends Controller
{
    // ✅ عرض جميع الفيديوهات
    public function index()
    {
        return response()->json(Video::with('user')->latest()->get());
    }

    // ✅ رفع فيديو جديد
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'video'       => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg|max:102400',
            'description' => 'nullable|string',
        ]);

        $path = $request->file('video')->store('videos', 'public');

        $video = Video::create([
            'user_id'    => Auth::id(),
            'title'      => $request->title,
            'video_path' => $path,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'تم رفع الفيديو بنجاح.',
            'video'   => $video,
            'url'     => asset('storage/' . $video->video_path)
        ]);
    }

    // ✅ عرض فيديو واحد
    public function show(Video $video)
    {
        return response()->json([
            'video' => $video,
            'url'   => asset('storage/' . $video->video_path)
        ]);
    }

    // ✅ تعديل بيانات الفيديو
    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $video->update($request->only(['title', 'description']));

        return response()->json(['message' => 'تم تعديل الفيديو.', 'video' => $video]);
    }

    // ✅ حذف الفيديو من التخزين والقاعدة
    public function destroy(Video $video)
    {
        Storage::disk('public')->delete($video->video_path);
        $video->delete();

        return response()->json(['message' => 'تم حذف الفيديو.']);
    }
}
