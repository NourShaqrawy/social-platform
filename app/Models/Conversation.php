<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'created_by'];

    public function creator()      { return $this->belongsTo(User::class, 'created_by'); }
    public function participants() { return $this->belongsToMany(User::class, 'participants')->withTimestamps(); }
    public function messages()     { return $this->hasMany(Message::class); }
}

