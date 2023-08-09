<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public function getCategory(){
        return $this->belongsTo(Category::class,'category_id','id');
        //belongs to many : esem el table + foreign key
    }

    public function getUser(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function getReviews(){
        return $this->hasMany(Review::class);
    }
    public function getCartItems(){
        return $this->hasMany(CartItems::class);
    }

    public function getCoursesOwned(){
        return $this->hasMany(CoursesOwned::class);
    }
}
