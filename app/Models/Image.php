<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'thumbnail',
        'width',
        'height',
        'description',
    ];

    /**
     * العلاقة مع المستخدم مالك الصورة
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * يمكنك لاحقًا إضافة علاقات أخرى مثل:
     * - belongsTo(Post::class) إذا كانت الصورة مرتبطة بمنشور
     * - morphTo() إذا كانت الصورة متعددة الاستخدامات (Polymorphic)
     */
}
