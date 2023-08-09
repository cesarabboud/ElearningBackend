<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public function getUser(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function getCourse(){
        return $this->belongsTo(Course::class,'course_id','id');
    }
    public function getReplies(){
        return $this->hasMany(Reply::class);
    }
}
