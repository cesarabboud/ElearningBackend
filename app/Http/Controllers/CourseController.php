<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursesOwned;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                    $PDFsOwned[]=$c;
                }
            }
        }
        //dd(count($coursesOwned));
        if(count($PDFsOwned)>0){
            return response()->json(['pdfs'=>$PDFsOwned,'nbpdfs'=>count($PDFsOwned)]);
        }
        return response()->json(['message'=>'no PDFs bought']);
    }
    // get my videos
    public function getVideos(){
        $orderByUser = Order::where('user_id','=',Auth::id())->get();
        $videosOwned=[];
        foreach ($orderByUser as $o){
            foreach ($o->getCoursesOwned as $c){
                if($c->getCourse->type == 'video'){
                    $videosOwned[]=$c;
                }
            }
        }
        //dd(count($coursesOwned));
        if(count($videosOwned)>0){
            return response()->json(['videos'=>$videosOwned]);
        }
        return response()->json(['message'=>'no videos bought']);
    }


    public function getCourseDetails ($cid) {

        $course = Course::with('getCategory')->with('getReviews')->find($cid);
        return response()->json(['course'=>$course,'nbrev'=>$course->getReviews->count()]);

    }
}
