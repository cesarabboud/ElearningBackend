<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursesOwned extends Model
{
    use HasFactory;
    public function getCourse(){
        return $this->belongsTo(Course::class,'course_id','id');
    }
    public function getOrder(){
        return $this->belongsTo(Order::class,'order_id','id');
    }
}
