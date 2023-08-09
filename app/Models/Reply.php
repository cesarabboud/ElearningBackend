<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    public function getReview(){
        return $this->belongsTo(Review::class,'review_id','id');

    }
    public function getUser(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
