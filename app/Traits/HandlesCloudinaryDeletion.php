<?php

namespace App\Traits;

use Cloudinary\Cloudinary;

trait HandlesCloudinaryDeletion
{
    /**
     * حذف ملف من Cloudinary (صورة أو فيديو)
     *
     * @param string $mediaUrl
     * @param string $resourceType 'image' أو 'video'
     * @param string $folder
     * @return bool
     */
    public function deleteFromCloudinary(string $mediaUrl, string $resourceType = 'image', string $folder = 'group_posts'): bool
    {
        $publicId = $this->extractPublicId($mediaUrl, $folder);

        try {
            $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

            $result = $cloudinary->uploadApi()->destroy($publicId, [
                'resource_type' => $resourceType,
            ]);

            return $result['result'] === 'ok';
        } catch (\Exception $e) {
            // يمكنك تسجيل الخطأ أو التعامل معه حسب الحاجة
            return false;
        }
    }

    /**
     * استخراج public_id من رابط Cloudinary
     *
     * @param string $url
     * @param string $folder
     * @return string
     */
    private function extractPublicId(string $url, string $folder): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', $path);
        $filename = end($segments);
        $nameOnly = pathinfo($filename, PATHINFO_FILENAME);
        return $folder . '/' . $nameOnly;
    }
}
