<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feeling extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'emoji', 'description'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function groupPosts()
    {
        return $this->hasMany(GroupPost::class);
    }
}
