<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\VideoRoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/logout',   [AuthController::class, 'logout'])->middleware('auth:sanctum');





Route::middleware('auth:sanctum')->group(function () {
    Route::post('/videos', [VideoController::class, 'upload']);
    Route::get('/videos', [VideoController::class, 'index']);
    Route::get('/videos/{id}', [VideoController::class, 'show']);
    Route::put('/videos/{id}', [VideoController::class, 'update']);
    Route::delete('/videos/{id}', [VideoController::class, 'destroy']);
    Route::delete('/videos/cloud/{id}', [VideoController::class, 'destroy_from_cloud']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('groups', GroupController::class);
});

use App\Http\Controllers\ImageController;

Route::middleware('auth:sanctum')->group(function () {
    // 📄 عرض جميع الصور الخاصة بالمستخدم
    Route::get('/images', [ImageController::class, 'index']);

    // 🔍 عرض صورة واحدة حسب ID
    Route::get('/images/{id}', [ImageController::class, 'show']);

    // 📥 رفع صورة إلى Cloudinary
    Route::post('/images/upload', [ImageController::class, 'upload']);

    // 🗑️ حذف صورة من قاعدة البيانات فقط
    Route::delete('/images/{id}', [ImageController::class, 'destroy']);

    // 🧹 حذف صورة من Cloudinary وقاعدة البيانات
    Route::delete('/images/{id}/destroy-from-cloud', [ImageController::class, 'destroy_from_cloud']);
});

use App\Http\Controllers\GroupPostController;

Route::middleware('auth:sanctum')->group(function () {
    // 📥 رفع منشور جديد داخل مجموعة
    Route::get('/group-posts', [GroupPostController::class, 'index']);
    Route::get('/group-posts/{id}', [GroupPostController::class, 'show']);

    Route::post('/group-posts', [GroupPostController::class, 'store']);
    Route::put('/group-posts/{id}', [GroupPostController::class, 'update']);

    Route::delete('/group-posts/{id}', [GroupPostController::class, 'destroy']);
    Route::delete('/group-posts/cloud/{id}', [GroupPostController::class, 'destroy_from_cloud']);
});




Route::middleware('auth:sanctum')->group(function () {

    // 📃 عرض كل منشورات المستخدم
    Route::get('/posts', [PostController::class, 'index']);

    // 🔍 عرض منشور واحد
    Route::get('/posts/{id}', [PostController::class, 'show']);

    // 📥 إنشاء منشور جديد
    Route::post('/posts', [PostController::class, 'store']);

    // ✏️ تعديل منشور
    Route::put('/posts/{id}', [PostController::class, 'update']);

    // 🗑️ حذف منشور فقط
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // 🗑️ حذف منشور والوسائط من Cloudinary
    Route::delete('/posts/cloud/{id}', [PostController::class, 'destroy_from_cloud']);
});

// routes/api.php

    Route::post('/video-room', [VideoRoomController::class, 'create']);
    Route::get('/video-room', [VideoRoomController::class, 'getRoom']);

    


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginSuccessMail;

// Route::post('/login', function (Request $request) {
//     $credentials = $request->only('email', 'password');

//     if (Auth::attempt($credentials)) {
//         $user = Auth::user();

//         // إرسال البريد الإلكتروني
//         Mail::to($user->email)->send(new LoginSuccessMail($user));

//         // توليد التوكن إذا كنت تستخدم Sanctum
//         $token = $user->createToken('API Token')->plainTextToken;

//         return response()->json([
//             'message' => 'تم تسجيل الدخول بنجاح',
//             'user' => $user,
//             'token' => $token,
//         ]);
//     }

//     return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
// });
