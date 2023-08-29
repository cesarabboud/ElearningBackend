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
        $categories = Category::inRandomOrder()->take(3)->get();
        $pdfs = Course::where('type','pdf')->get();
        $videos = Course::where('type','video')->get();
        $topRated = $this->getTopRated();
        //dd(json_decode($randomcourses,true));

        return response()->json(['categories'=>$categories,'topRated'=>$topRated]);
    }

    public function getRecentUploads(){
        $recentRows = Course::orderBy('created_at', 'desc')->take(3)->get();
        return response()->json(['recentrows'=>$recentRows]);
    }
    public function getCourses(){
        $courses = Course::with('getCategory')->with('getUser')->get();
        return response()->json(['courses'=>$courses]);
    }
}
