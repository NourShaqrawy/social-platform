<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'video_path', 'thumbnail', 'duration', 'description'];
// public $timestamps = false;

    public function user()       { return $this->belongsTo(User::class); }
    public function comments()   { return $this->morphMany(Comment::class, 'commentable'); }
    public function likes()      { return $this->morphMany(Like::class, 'likeable'); }
    public function bookmarks()  { return $this->morphMany(Bookmark::class, 'bookmarkable'); }
    public function tags()       { return $this->morphToMany(Tag::class, 'taggable');
     }
}
