<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    public function getCourse(){
        return $this->belongsTo(Course::class,'course_id','id');
    }
    public function getUser(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
