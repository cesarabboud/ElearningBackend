<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CoursesOwned;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isEmpty;

class CourseController extends Controller
{
    //
    //get my pdfs
    public function getPDFs(){
        $orderByUser = Order::where('user_id','=',Auth::id())->get();
        $PDFsOwned=[];
        foreach ($orderByUser as $o){
            foreach ($o->getCoursesOwned as $c){
                if($c->getCourse->type == 'pdf'){
                    $PDFsOwned[]=$c->getCourse;
                }
            }
        }
        //dd(count($coursesOwned));
        if(count($PDFsOwned)>0){
            return response()->json(['pdfs'=>$PDFsOwned,'nbpdfs'=>count($PDFsOwned)]);
        }
        return response()->json(['pdfs'=>$PDFsOwned,'message'=>'no PDFs bought']);
    }
    // get my videos
    public function getVideos(){
        $orderByUser = Order::where('user_id','=',Auth::id())->get();
        $videosOwned=[];
        foreach ($orderByUser as $o){
            foreach ($o->getCoursesOwned as $c){
                if($c->getCourse->type == 'mp4'){
                    $videosOwned[]=$c->getCourse;
                }
            }
        }
        //dd(count($coursesOwned));
        if(count($videosOwned)>0){
            return response()->json(['videos'=>$videosOwned,'nbVideos'=>count($videosOwned)]);
        }
        return response()->json(['message'=>'no videos bought']);
    }


    public function getCourseDetails ($cid) {

        $course = Course::with('getCategory')->with('getReviews')->find($cid);
        return response()->json(['course'=>$course,'nbrev'=>$course->getReviews->count()]);

    }
    public function getnbVideos(){
        $orderByUser = Order::where('user_id','=',Auth::id())->get();
        $videosOwned=[];
        foreach ($orderByUser as $o){
            foreach ($o->getCoursesOwned as $c){
                if($c->getCourse->type == 'mp4'){
                    $videosOwned[]=$c;
                }
            }
        }
        //dd(count($coursesOwned));
        return(count($videosOwned));
    }
    public function getTypes(){
        $uniqueTypes = Course::distinct()->pluck('type');
        return response()->json(['uniqueTypes'=>$uniqueTypes]);
    }
    public function searchCourseByName(Request $request){
        $coursesList = Course::where('title','like','%'.$request->title.'%')->with('getCategory')->get();
        if($coursesList->count()>0){
            return response()->json(['courses'=>$coursesList,'nbcourses'=>$coursesList->count()]);
        }
        return response()->json(['msg'=>'no courses found !']);
    }
    public function searchCourseByFilters(Request $request){

        $categoryNames = $request->input('category', []);
//        dd($categoryNames);
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');
        if(!empty($categoryNames)){
            $categoryIds = Category::whereIn('name', $categoryNames)->pluck('id');
        }
          $coursesQuery = Course::query();
//

        if (!empty($categoryNames)) {
            $coursesQuery->whereIn('category_id', $categoryIds)->get();
        }



        if ($request->has('rating')) {
            if($request->sign === 'Greater Or Equal'){
                $coursesQuery->where('rating', '>=', $request->rating);
            }
            else if($request->sign === 'Greater'){
                $coursesQuery->where('rating', '>', $request->rating);
            }
            else if($request->sign === 'Less Or Equal'){
                $coursesQuery->where('rating', '<=', $request->rating);
            }
            else if($request->sign === 'Less'){
                $coursesQuery->where('rating', '<', $request->rating);
            }
            else if($request->sign === 'Equal'){
                $coursesQuery->where('rating', '=', $request->rating);
            }
        }

        if ($minPrice !== null && $maxPrice !== null) {
            $coursesQuery->whereBetween('price', [$minPrice, $maxPrice]);
        }

        $typeNames = $request->input('types',[]);
        if(!empty($typeNames)){
            $coursesQuery->whereIn('type',$typeNames);
        }



        $coursesList = $coursesQuery->with('getCategory')->with('getUser')->get();

        if ($coursesList->count()) {
            return response()->json(['courses' => $coursesList,'nbcourses'=>$coursesList->count()]);
        } else {
            return response()->json(['message' => 'No courses found.']);
        }

    }
}
