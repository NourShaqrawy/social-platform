<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['city', 'country', 'latitude', 'longitude'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function groupPosts()
    {
        return $this->hasMany(GroupPost::class);
    }
}
