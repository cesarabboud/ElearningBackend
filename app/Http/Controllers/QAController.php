<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Support\Facades\Auth;
use function Termwind\renderUsing;

class QAController extends Controller
{
    //
    //test done
    public function getAllQuestions(){
        $allQ = Question::with('getUser')->with('getAnswers')->get();
        $allQ->each(function ($question) {
            // Check if the question has correct answers
            $question->correctlyAnswered = $question->getAnswers->where('answered', 1)->count() > 0 ? true : false ;
        });
        $allQ->each(function ($question) {
            $question->append('answers_count');
        });


        return response()->json(['questions'=>$allQ]);
    }
    //test done
    public function askQuestion(Request $request){
        $request->validate([
           'question'=>'required'
        ]);
        $quest = new Question();
        $quest->question = $request->question;
        $quest->user_id = Auth::id() ;
        $quest->save();
        return response()->json(['message'=>Question::all()]);
    }
    //test done
    public function getAnswersOfQuestion($id){
        $quest = Question::find($id);
        if($quest){
            $answers = $quest->getAnswers()->with('getUser')->get();
            if($answers->count()>0){
                return response()->json(['answers'=>$answers]);
            }
            return response()->json(['msg'=>'No answers yet.']);

        }

    }
    //test done
    public function answeredOrNot($qid){
        $hasCorrectAnswer = Question::find($qid)
            ->getAnswers()
            ->where('answered', 1)
            ->exists();

        if ($hasCorrectAnswer) {
            return true;
        }
            return false;
    }
    //test done
    public function respondToQuestion($qid,Request $request){
        $quest = Question::find($qid);
        if($quest){
            if($request->answer !=='' || $request->answer !== null){
                $answer = new Answer();
                $answer->answer=$request->answer;
                $answer->question_id = $quest->id;
                $answer->user_id = 6 ;
                $answer->save();
                return response()->json(['msg'=>'answer added']);
            }
            return response()->json(['msg'=>'answer field is empty']);
        }
        return response()->json(['msg'=>'no question found !']);
    }

    public function searchQuestions(Request $request){
        error_log($request->input('name'));
        $questions = Question::with('getAnswers')->with('getUser')
            ->where('question','like', '%' .$request->name . '%')->get();

        $questions->each(function ($question) {
            // to see if the question has correct answers
            $question->correctlyAnswered = $question->getAnswers->where('answered', 1)->count() > 0 ;
        });
        $questions->each(function ($question) {
            $question->append('answers_count');
        });
        if($questions->count()){
            return response()->json(['questions'=>$questions]);
        }
        return response()->json(['questions'=>$questions,'msg'=>'no questions']);
    }
}
