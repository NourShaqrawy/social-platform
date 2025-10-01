<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'owner_id', 'privacy'];

    public function owner()    { return $this->belongsTo(User::class, 'owner_id'); }
    public function members()  { return $this->belongsToMany(User::class, 'group_members')->withPivot('role')->withTimestamps(); }
    public function posts()    { return $this->hasMany(GroupPost::class); }
}
