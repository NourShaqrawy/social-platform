<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];

    public function posts()       { return $this->morphedByMany(Post::class, 'taggable'); }
    public function videos()      { return $this->morphedByMany(Video::class, 'taggable'); }
    public function groupPosts()  { return $this->morphedByMany(GroupPost::class, 'taggable'); }
}

