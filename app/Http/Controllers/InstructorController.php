<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
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
    public function uploadPDF(Request $request){
        $request->validate([
            'myfile'=>'required|mimes:xlsx,csv,txt,xlx,xls,pdf,doc,docx,ppt,pptx,mp4|max:30720',
            'image'=>'required|mimes:jpeg,png,gif,jpg'
        ]);
        //for file
        $originalname = $request->file('myfile')->getClientOriginalName();
        error_log("the original image name is:" . $originalname);
        $filename= time().'.'.$request->file('myfile')->getClientOriginalExtension();
        error_log("the new file name is:" . $filename);
       $request->file('myfile')->move(public_path('uploads'),$filename);
        $tosave= 'uploads/'.$filename;

       //for img
        $originalpicname = $request->file('image')->getClientOriginalName();
        error_log($originalpicname);
        $picname= time().'.'.$request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(public_path('uploads'),$picname);
        $pictosave= 'uploads/'.$picname;
        error_log($pictosave);
       error_log($request->title);
       error_log($request->category);
       $course = new Course();
       $course->type=$request->type;
       $course->title=$request->title;
       $course->description = $request->description;
       $course->user_id= 3;
       $course->thumbnail= $pictosave;
       $course->price= $request->price;

       $course->size= $request->size;
       $course->rating= 0;
       $course->link = $tosave;
       $c = Category::where('name','=',$request->category)->first();
       $course->category_id= $c->id;
       error_log('link :'.$course->link);
       $course->save();
       error_log('course saved');
       return response()->json(['msg'=>'course saved!']);
    }
}
