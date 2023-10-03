<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Course;
use App\Models\CoursesOwned;
use App\Models\Order;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Reply;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    public function getStudents(){
        $students = User::where('role_id','=',2)->get();
        if($students->count()!=0){
            return response()->json(['students'=>$students,'studentscount'=>$students->count()]);
        }
        return response()->json(['message'=>'no students found']);
    }
    public function getInstructors(){
        $instructors = User::where('role_id','=',3)->get();
        if($instructors->count()!=0){
            return response()->json(['instructors'=>$instructors,'instructorscount'=>$instructors->count()]);
        }
        return response()->json(['message'=>'no Instructors found']);
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
                if($c->getCourse->type == 'mp4'){
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
            $coursesOwnedByStudent = Order::where('user_id', $student->id)
                ->with('getCoursesOwned.getCourse') // Eager load the courses relationship
                ->with('getCoursesOwned.getCourse.getCategory')
                ->with('getCoursesOwned.getCourse.getUser')
                ->get()
                ->pluck('getCoursesOwned')
                ->flatten()
                ->groupBy(function ($courseOwned) {
                    return $courseOwned->getCourse->type;
                });
            $courseCountsByType = [];
            // Loop through the grouped courses and count them for each type
            foreach ($coursesOwnedByStudent as $type => $courses) {
                $courseCountsByType[$type] = count($courses);
            }
            $tot = 0;
            foreach ($courseCountsByType as $val){
                $tot += $val;
            }

            return response()->json(['pp'=>$student->profilepicture,'nbCourses'=>$tot,'reviews'=>$student->getReviews()->with('getUser')->with('getCourse')->get()]);
        }
        return response()->json(['message'=>'student not found']);
    }


    public function getStats(){
        $getNbStudents = User::where('role_id','=',2)->count();
        $getNbTeachers = User::where('role_id','=',3)->count();
        $getNbCourses = Course::all()->count();
        $getNbReviews = Review::all()->count();
        $getNbQuestions = Question::all()->count();
        $getNbAnswers = Answer::all()->count();
        $arr = [$getNbStudents,$getNbTeachers,$getNbCourses,$getNbReviews,$getNbQuestions,$getNbAnswers];
        return  response()->json(['arr'=>$arr]);
        return response()->json(['nbS'=>$getNbStudents,'nbT'=>$getNbTeachers,
            'nbC'=>$getNbCourses,'nbR'=>$getNbReviews,
            'nbQ'=>$getNbQuestions]);
    }

    public function deleteCourse($cid){
        $course = Course::find($cid);
        if($course!=null){
            $course->delete();
            return response()->json(['message','course deleted successfully']);
        }
        return response()->json(['message'=>'course not found!']);
    }

    public function deleteUser($id){
        error_log('hi from delete user');
        $userToDelete = User::find($id);
        if($userToDelete!=null){
            $userToDelete->delete();
            return response()->json(['message'=>'user deleted']);
        }
        return response()->json(['message'=>'the user you are trying to delete is not found']);
    }

    public function getAvgCoursesPricesByType(){

        $pdfAvg = Course::where('type', 'pdf')->avg('price');
        $pptxAvg = Course::where('type', 'pptx')->avg('price');
        $docxAvg = Course::where('type', 'docx')->avg('price');
        $mp4Avg = Course::where('type', 'mp4')->avg('price');

        $pricesArr = [round($pdfAvg,2),round($pptxAvg,2),round($docxAvg,2),round($mp4Avg,2)];
        $types = ['pdf','pptx','docx','mp4'];
        return response()->json(['types'=>$types,'prices'=>$pricesArr]);
    }
    public function getPercentages(){
        $getTotalCourses = Course::all()->count();
        $pdfAvg = round((Course::where('type', 'pdf')->count()*100)/$getTotalCourses,2);
        $pptxAvg = round((Course::where('type', 'pptx')->count()*100)/$getTotalCourses,2);
        $docxAvg = round((Course::where('type', 'docx')->count()*100)/$getTotalCourses,2);
        $mp4Avg = round((Course::where('type', 'mp4')->count()*100)/$getTotalCourses,2);
//        $mp4Avg2 = (Course::where('type', 'video')->count()*100)/$getTotalCourses;
        return [$pdfAvg,$pptxAvg,$docxAvg,$mp4Avg];

    }

    public function getLeaders()
    {
        $leaders = User::has('getQuizzes')
            ->join('quizzes', 'users.id', '=', 'quizzes.user_id')
            ->selectRaw('users.id, users.name, ROUND(AVG(quizzes.grade), 1) as averageScore')
            ->groupBy('users.id', 'users.name')
            ->orderBy('averageScore', 'desc')
            ->take(3)
            ->get();

        return response()->json(['leaders'=>$leaders]);

    }
    public function getAverageQuizScores()
    {
        $users = User::has('getQuizzes')
            ->join('quizzes', 'users.id', '=', 'quizzes.user_id')
            ->selectRaw('users.id, users.name,users.profilepicture, ROUND(AVG(quizzes.grade), 1) as averageScore')
            ->groupBy('users.id', 'users.name', 'users.profilepicture')
            ->orderBy('averageScore', 'desc')
            ->get();

        return response()->json(['students'=>$users]);

    }

    public function getMentorCourses($uId){
        $mentorCourses= Course::where('user_id',$uId)->with('getCategory')->with('getUser')->get()->groupBy('type');
        return response()->json(['courses'=>$mentorCourses]);
    }

    public function studentsPerformance(){
        $users = User::has('getQuizzes')
            ->join('quizzes', 'users.id', '=', 'quizzes.user_id')
            ->selectRaw('users.id, users.name, users.profilepicture, ROUND(AVG(quizzes.grade)) as averageScore')
            ->groupBy('users.id', 'users.name', 'users.profilepicture')
            ->orderBy('averageScore', 'desc')
            ->get();
        $studentCountWithAverageGrade0 = User::has('getQuizzes')
            ->join('quizzes', 'users.id', '=', 'quizzes.user_id')
            ->selectRaw('users.id, users.name, users.profilepicture, ROUND(AVG(quizzes.grade)) as averageScore')
            ->groupBy('users.id', 'users.name', 'users.profilepicture')
            ->havingRaw('ROUND(AVG(quizzes.grade)) = ?', [0])
            ->count();
        $studentCountWithAverageGrade1 = User::has('getQuizzes')
            ->join('quizzes', 'users.id', '=', 'quizzes.user_id')
            ->selectRaw('users.id, users.name, users.profilepicture, ROUND(AVG(quizzes.grade)) as averageScore')
            ->groupBy('users.id', 'users.name', 'users.profilepicture')
            ->havingRaw('ROUND(AVG(quizzes.grade)) = ?', [1])
            ->count();
        $studentCountWithAverageGrade2 = User::has('getQuizzes')
            ->join('quizzes', 'users.id', '=', 'quizzes.user_id')
            ->selectRaw('users.id, users.name, users.profilepicture, ROUND(AVG(quizzes.grade)) as averageScore')
            ->groupBy('users.id', 'users.name', 'users.profilepicture')
            ->havingRaw('ROUND(AVG(quizzes.grade)) = ?', [2])
            ->count();
        $studentCountWithAverageGrade3 = User::has('getQuizzes')
            ->join('quizzes', 'users.id', '=', 'quizzes.user_id')
            ->selectRaw('users.id, users.name, users.profilepicture, ROUND(AVG(quizzes.grade)) as averageScore')
            ->groupBy('users.id', 'users.name', 'users.profilepicture')
            ->havingRaw('ROUND(AVG(quizzes.grade)) = ?', [3])
            ->count();
        $studentCountWithAverageGrade4 = User::has('getQuizzes')
            ->join('quizzes', 'users.id', '=', 'quizzes.user_id')
            ->selectRaw('users.id, users.name, users.profilepicture, ROUND(AVG(quizzes.grade)) as averageScore')
            ->groupBy('users.id', 'users.name', 'users.profilepicture')
            ->havingRaw('ROUND(AVG(quizzes.grade)) = ?', [4])
            ->count();
        $studentCountWithAverageGrade5 = User::has('getQuizzes')
            ->join('quizzes', 'users.id', '=', 'quizzes.user_id')
            ->selectRaw('users.id, users.name, users.profilepicture, ROUND(AVG(quizzes.grade)) as averageScore')
            ->groupBy('users.id', 'users.name', 'users.profilepicture')
            ->havingRaw('ROUND(AVG(quizzes.grade)) = ?', [5])
            ->count();
        $percentages1and0 = (($studentCountWithAverageGrade0+$studentCountWithAverageGrade1)*100)/$users->count();
        $percentages2 = ($studentCountWithAverageGrade2*100)/$users->count();
        $percentages3 = ($studentCountWithAverageGrade3*100)/$users->count();
        $percentages4and5 = (($studentCountWithAverageGrade4+$studentCountWithAverageGrade5)*100)/$users->count();
        $res = [$percentages1and0,$percentages2,$percentages3,$percentages4and5];
        return response()->json(['percentagesArr'=>$res]);
    }
}
