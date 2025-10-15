<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Image;
use Cloudinary\Cloudinary;

class ImageController extends Controller
{

    public function __construct(){}
    public function index()
    {
        $images = Image::where('user_id', Auth::id())->latest()->get();

        return response()->json([
            'images' => $images,
        ]);
    }

   public function show($id)
{
    // جلب الصورة بدون شرط المستخدم
    $image = Image::findOrFail($id);

    // التحقق من ملكية الصورة
    if ($image->user_id !== Auth::id()) {
        return response()->json([
            'message' => '❌ لا يمكن للمستخدم عرض هذه الصورة لأنها لا تخصه'
        ], 403); // كود 403 يعني "ممنوع الوصول"
    }

    // إرجاع الصورة إن كانت ملكًا للمستخدم
    return response()->json([
        'image' => $image,
    ]);
}


    /**
     * رفع صورة إلى Cloudinary باستخدام env('CLOUDINARY_URL') مباشرة
     */
    public function upload(Request $request)
    {
        // ✅ التحقق من صحة المدخلات
        $request->validate([
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'description' => 'nullable|string',
        ]);

        // ✅ تهيئة كائن Cloudinary
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

        // ✅ رفع الصورة
        $uploaded = $cloudinary->uploadApi()->upload(
            $request->file('image')->getRealPath(),
            [
                'resource_type' => 'image',
                'folder'        => 'user_images',
            ]
        );

        // ✅ استخراج الرابط الآمن
        $imageUrl = $uploaded['secure_url'] ?? null;

        // ✅ إنشاء سجل في قاعدة البيانات
        $image = Image::create([
            'user_id'     => Auth::id(),
            'image_path'  => $imageUrl,
            'thumbnail'   => null,
            'width'       => null,
            'height'      => null,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => '✅ تم رفع الصورة بنجاح باستخدام Cloudinary API',
            'image'   => $image,
        ]);
    }

  public function destroy($id)
{
    // جلب الصورة بدون شرط المستخدم
    $image = Image::findOrFail($id);

    // التحقق من ملكية الصورة
    if ($image->user_id !== Auth::id()) {
        return response()->json([
            'message' => '❌ لا يمكن للمستخدم حذف هذه الصورة لأنها لا تخصه'
        ], 403); // كود 403 يعني "ممنوع الوصول"
    }

    // حذف الصورة
    $image->delete();

    return response()->json([
        'message' => '🗑️ تم حذف الصورة بنجاح',
    ]);
}


  public function destroy_from_cloud($id)
{
    // جلب الصورة بدون شرط المستخدم
    $image = Image::findOrFail($id);

    // التحقق من ملكية الصورة
    if ($image->user_id !== Auth::id()) {
        return response()->json([
            'message' => '❌ لا يمكن للمستخدم حذف هذه الصورة لأنها لا تخصه'
        ], 403); // كود 403 يعني "ممنوع الوصول"
    }

    // استخراج public_id من رابط Cloudinary
    $publicId = $this->extractPublicId($image->image_path);

    // محاولة الحذف من Cloudinary
    try {
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
        $result = $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);

        if ($result['result'] !== 'ok') {
            return response()->json([
                'message' => '⚠️ لم يتم حذف الصورة من Cloudinary',
                'cloudinary_result' => $result,
            ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'message' => '❌ خطأ أثناء حذف الصورة من Cloudinary',
            'error'   => $e->getMessage(),
        ], 500);
    }

    // حذف من قاعدة البيانات
    $image->delete();

    return response()->json([
        'message' => '🗑️ تم حذف الصورة من Cloudinary وقاعدة البيانات بنجاح',
    ]);
}


    private function extractPublicId($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', $path);
        $filename = end($segments);
        $nameOnly = pathinfo($filename, PATHINFO_FILENAME);
        return 'user_images/' . $nameOnly;
    }
}
