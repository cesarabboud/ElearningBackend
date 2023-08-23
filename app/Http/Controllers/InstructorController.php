<?php

namespace App\Http\Controllers;

use App\Models\CoursesOwned;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class InstructorController extends Controller
{
    //
    public function getInstructorProfileInfoStudents(){
        //students
        $studentsOfInstructor = User::select('users.*')
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->join('courses_owneds', 'courses_owneds.order_id', '=', 'orders.id')
            ->join('courses', 'courses.id', '=', 'courses_owneds.course_id')
            ->where('courses.user_id', Auth::id())
            ->distinct()
            ->get();
        return $studentsOfInstructor->count()>0 ? response()->json(['mystudents'=>$studentsOfInstructor])
            : response()->json(['mystudents'=>$studentsOfInstructor,'message'=>'no students']);
    }
}
