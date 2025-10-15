<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content', 'media_url', 'visibility', 'feeling_id', 'location_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function feeling()
    {
        return $this->belongsTo(Feeling::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
