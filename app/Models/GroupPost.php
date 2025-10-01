<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupPost extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'user_id', 'content', 'media_url'];

    public function group()      { return $this->belongsTo(Group::class); }
    public function user()       { return $this->belongsTo(User::class); }
    public function comments()   { return $this->morphMany(Comment::class, 'commentable'); }
    public function likes()      { return $this->morphMany(Like::class, 'likeable'); }
    public function bookmarks()  { return $this->morphMany(Bookmark::class, 'bookmarkable'); }
    public function tags()       { return $this->morphToMany(Tag::class, 'taggable'); }
}
