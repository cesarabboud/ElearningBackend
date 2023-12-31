<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    public function getUser(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function getCartItems(){
        return $this->hasMany(CartItems::class);
    }
}
