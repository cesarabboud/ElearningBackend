<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class orderController extends Controller
{
    //
    public function myOrders(){
        if(Auth::check()){
            $myorders = Order::where('user_id','=',Auth::id())->get();
            if($myorders->count()>0){
                $myordersArray = json_decode($myorders,true);

                return response()->json(['myorders'=>$myordersArray]);
            }
            return response()->json(['message'=>'no orders made !']);
        }
        return response()->json(['message'=>'no user logged in']);
    }
    public function orderDetails($id)
    {
        $obj = Order::find($id);
        $coursesordered = $obj->getCoursesOwned;
        return response()->json(['ownedCourses'=>$coursesordered]);
    }



}
