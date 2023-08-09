<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\Reply;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    public function getStudents(){
        $students = User::where('role_id','=',3);
        if($students->count()!=0){
            return response()->json(['students'=>$students]);
        }
        return response()->json(['message'=>'no students found']);
    }
    public function getInstructors(){
        $instructors = User::where('role_id','=',2);
        if($instructors->count()!=0){
            return response()->json(['students'=>$instructors]);
        }
        return response()->json(['message'=>'no students found']);
    }

    public function getAllCourses(){
        $courses = Course::all();
        if($courses->count()!=0){
            return response()->json(['courses'=>$courses]);
        }
        return response()->json(['message'=>'no courses found']);
    }
    public function getAllReviews(){
        $allReviews = Review::all();
        if($allReviews->count()!=0){
            return response()->json(['reviews'=>$allReviews]);
        }
        return response()->json(['message'=>'No reviews!']);
    }
    public function getRepliesOfReview($rid){
        $review = Review::find($rid);
        if($review!=null){
            $replies = Reply::where('review_id','=',$review->id)->get();
            error_log($replies->count());
            if($replies->count()>0){
                return response()->json(['replies'=>$replies]);
            }
            return response()->json(['message'=>'no replies for this review']);
        }
        return response()->json(['message'=>'no review found']);
    }
    public function getnbPDFs($studentid){
        $orderByUser = Order::where('user_id','=',$studentid)->get();
        $PDFsOwned=[];
        foreach ($orderByUser as $o){
            foreach ($o->getCoursesOwned as $c){
                if($c->getCourse->type == 'pdf'){
                    $PDFsOwned[]=$c;
                }
            }
        }
        return count($PDFsOwned);
    }
    public function getnbVideos($studentid){
        $orderByUser = Order::where('user_id','=',$studentid)->get();
        $videosOwned=[];
        foreach ($orderByUser as $o){
            foreach ($o->getCoursesOwned as $c){
                if($c->getCourse->type == 'video'){
                    $videosOwned[]=$c;
                }
            }
        }
        //dd(count($coursesOwned));
        return(count($videosOwned));
    }
    public function getStudentDetails($sid){
        $student = User::find($sid);
        if($student!=null){
            $nbPDFsTaken = $this->getnbPDFs($student->id);
            $nbVideosTaken = $this->getnbVideos($student->id);
            return response()->json(['student'=>$student,'nbofPDFs'=>$nbPDFsTaken,'nbVidsTaken'=>$nbVideosTaken]);
        }
        return response()->json(['message'=>'student not found']);
    }

    public function deleteCourse($cid){
        $course = Course::find($cid);
        if($course!=null){
            $course->delete();
            return response()->json(['message','course deleted successfully']);
        }
        return response()->json(['message'=>'course not found!']);
    }

    public function deleteUser($uid){
        $userToDelete = User::find($uid);
        if($userToDelete!=null){
            $userToDelete->delete();
            return response()->json(['message'=>'user deleted']);
        }
        return response()->json(['message'=>'the user you are trying to delete is not found']);
    }


}
