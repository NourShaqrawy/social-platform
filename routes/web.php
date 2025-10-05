<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

Route::get('/test-upload', function () {
    $uploaded = Cloudinary::uploadApi()->upload(

        public_path('sample.mp4'),
        [
            'resource_type' => 'video',
            'folder' => 'user_videos',
        ]
    );

    return 'تم رفع الفيديو: ';
});

