<?php


namespace App\Traits;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

trait HandlesImageUpload
{
    public function uploadImageToCloudinary(UploadedFile $image, string $folder = 'group_posts'): ?string
    {
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

        $uploaded = $cloudinary->uploadApi()->upload(
            $image->getRealPath(),
            [
                'resource_type' => 'image',
                'folder'        => $folder,
            ]
        );

        return $uploaded['secure_url'] ?? null;
    }
}
