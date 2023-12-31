<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    public function getUser(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function getAnswers(){
        return $this->hasMany(Answer::class);
    }
    public function getAnswersCountAttribute()
    {
        return $this->getAnswers()->count();
    }
}
