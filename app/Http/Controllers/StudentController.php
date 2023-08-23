<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class StudentController extends Controller
{
    //

    public function getTopRated(){
        $Top3Rated = Course::orderBy('rating', 'desc')->take(3)->get();
        return $Top3Rated;
    }

    public function GetHomeScreenData(){
        $categories = Category::all();
        $pdfs = Course::where('type','pdf')->get();
        $videos = Course::where('type','video')->get();
        $topRated = $this->getTopRated();
        //dd(json_decode($randomcourses,true));

        return response()->json(['loggedinemail'=>Auth::user()->email,'categories'=>$categories,'topRated'=>$topRated]);
    }

}
