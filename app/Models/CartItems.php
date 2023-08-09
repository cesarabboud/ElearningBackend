<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItems extends Model
{
    use HasFactory;
    public function getCourse(){
        return $this->belongsTo(Course::class,'course_id','id');
    }
    public function getShoppingCart(){
        return $this->belongsTo(Cart::class,'cart_id','id');
    }
}
