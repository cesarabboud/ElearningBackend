<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Models\CoursesOwned;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class StudentController extends Controller
{
    //

    public function getTopRated(){
        $Top3Rated = Course::orderBy('rating', 'desc')->with('getCategory')->take(3)->get();
        return $Top3Rated;
    }
    public function getTopRated2(){
        $Top3Rated = Course::orderBy('rating', 'desc')->with('getCategory')->with('getUser')->take(3)->get();
        return response()->json(['courses'=>$Top3Rated]);
    }
    public function GetHomeScreenData(){
        $categories = Category::inRandomOrder()->take(3)->get();
        $pdfs = Course::where('type','pdf')->get();
        $videos = Course::where('type','video')->get();
        $topRated = $this->getTopRated();
        //dd(json_decode($randomcourses,true));
        $instuctors = User::inRandomOrder()->where('role_id',3)->take(3)->get();
        return response()->json(['categories'=>$categories,'topRated'=>$topRated,'uname'=>Auth::user()->name,'mentors'=>$instuctors]);
    }
    public function getLoggedInUserName(){
        return response()->json(['uname'=>Auth::user()->name]);
    }
    public function getRecentUploads(){
        $recentRows = Course::orderBy('created_at', 'desc')->take(3)->get();
        return response()->json(['courses'=>$recentRows]);
    }
    public function getCourses(){
        $courses = Course::orderBy('created_at', 'desc')->with('getCategory')->with('getUser')->take(5)->get();
        return response()->json(['courses'=>$courses]);
    }
    public function canReview($cid){
        $user_id = Auth::id();
        error_log($user_id);
        $course = Course::find($cid);
        if($course){
            $isOwned = CoursesOwned::whereHas('getOrder', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
                ->where('course_id', $course->id)
                ->exists();
                return response()->json(['message'=> $isOwned ? 'owned' : 'unowned']);
        }
        return response()->json(['message'=>'no course found']);
    }

    public function getMentors(){
        $mentorsList = User::where('role_id', 3)
            ->with(['getCourses.getCategory']) // Eager load courses and their categories
            ->get();

        // Transform the data to include categories
        $mentorsWithCategories = $mentorsList->map(function ($mentor) {
            $mentorCourses = $mentor->getCourses->map(function ($course) {
                return [
                    'course_id' => $course->id,
                    'title' => $course->title,
                    'category_id' => $course->getCategory->id,
                    'catName' => $course->getCategory->name,
                ];
            });

            return [
                'id' => $mentor->id,
                'name' => $mentor->name,
                'profilepicture' => $mentor->profilepicture,
                'courses' => $mentorCourses,
            ];
        });

        return response()->json(['mentors' => $mentorsWithCategories, 'count' => $mentorsList->count()]);
    }
}
