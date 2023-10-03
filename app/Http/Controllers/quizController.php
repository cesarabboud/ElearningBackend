<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class quizController extends Controller
{
    //
    public function postQuiz($cid,Request $request){
        $course = Course::find($cid);
        if($course){
            $quizToSubmit = new Quiz();
            $quizToSubmit->grade = $request->grade;
            $quizToSubmit->pass = $quizToSubmit->grade > 2.5;
            $quizToSubmit->duration = $request->duration;
            $quizToSubmit->DateAdded = Carbon::now()->addHours(3);
            $quizToSubmit->user_id = Auth::id();
            $quizToSubmit->course_id = $course->id;
            $quizToSubmit->save();
            return response()->json(['grade saved!']);
        }
        return response()->json(['msg'=>'course not found or grade not sent!']);
    }
}
