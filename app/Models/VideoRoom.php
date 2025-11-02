<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// app/Models/VideoRoom.php
class VideoRoom extends Model
{
    protected $fillable = ['room_name', 'caller_id', 'receiver_id'];

    public function caller() {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
