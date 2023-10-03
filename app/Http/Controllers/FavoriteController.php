<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Favorite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    //
    public function addToFav($cid){
        $course = Course::find($cid);
        $fav = Favorite::where('course_id','=',$course->id)->where('user_id','=',Auth::id())->first();

        if($course && !$fav){
            $fav = new Favorite();
            $fav->user_id =Auth::id();
            $fav->course_id = $course->id;
            $fav->dateAdded = Carbon::now()->addHours(3);
            $fav->save();
            $userFav = User::find(Auth::id())->getFavorites()->with('getCategory')->with('getUser')->get();
            return response()->json(['msg'=>'added to fav!','userFav'=>$userFav]);
        }
        return response()->json(['msg'=>'course already added!']);
    }
    public function deleteFromFav($cid){
        $course = Course::find($cid);
        if($course){
            $fav = Favorite::where('course_id','=',$course->id)->where('user_id','=',Auth::id())->first();
            if($fav){
                $fav->delete();
                $userFav = User::find(Auth::id())->getFavorites()->with('getCategory')->with('getUser')->get();
                return response()->json(['msg'=>'deleted from fav!','userFav'=>$userFav]);
            }
            return response()->json(['msg'=>'fav not found']);
        }
        return response()->json(['msg'=>'course not found']);
    }
    public function clearFav(){
        $loggedInUserFav = Favorite::where('user_id','=',Auth::id())->get();
        if($loggedInUserFav){
            foreach ($loggedInUserFav as $it){
                $it->delete();
            }
            return response()->json(['msg'=>'fav cleared !']);
        }
    }

    public function getMyFav(){
        $fav = User::find(Auth::id())->getFavorites()->with('getCategory')->with('getUser')->get();
        if($fav){
            return response()->json(['fav'=>$fav,'count'=>$fav->count()]);
        }
    }

    public function checkIfInFav($cid){
        $course = Course::find($cid);
        if($course){
            $isInFav = Favorite::where('user_id',Auth::id())->where('course_id',$course->id)->first();
            if($isInFav!==null){
                return 1;
            }
            return 0;
        }
        return response()->json(['msg'=>'course not found']);
    }
}
