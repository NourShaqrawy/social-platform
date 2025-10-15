<?php

namespace App\Traits;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

trait HandlesVideoUpload
{
    /**
     * رفع فيديو إلى Cloudinary
     *
     * @param UploadedFile $video
     * @param string $folder
     * @return string|null
     */
    public function uploadVideoToCloudinary(UploadedFile $video, string $folder = 'group_posts'): ?string
    {
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

        $uploaded = $cloudinary->uploadApi()->upload(
            $video->getRealPath(),
            [
                'resource_type' => 'video',
                'folder'        => $folder,
            ]
        );

        return $uploaded['secure_url'] ?? null;
    }
}
