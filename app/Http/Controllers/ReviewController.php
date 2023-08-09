<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use mysql_xdevapi\Exception;
use function PHPUnit\Framework\throwException;

class ReviewController extends Controller
{
    //
    //test done
    public function getCourseReviews($id){
        $course = Course::find($id);
        $arrayData = json_decode($course->getReviews,true);
        if($arrayData == null){
            throw new Exception('invalid JSON format');
        }
        return $arrayData;
    }
    //test done
    public function postReview($cid,Request $request){
        $course = Course::find($cid);

        if( $course!=null && $request->desc!='' && $request->rating!=null /*&& Auth::check()*/ ){
            $review = new Review();
            $review->content = $request->desc;
            $review->rating = $request->rating;
            $review->course_id=$course->id;
            $review->user_id=1;
            $review->save();
            $reviewsSum=0;
            $nbReviews = $course->getReviews->count();
            if($nbReviews>1){
                foreach ($course->getReviews as $r){
                    $reviewsSum+=$r->rating;
                }
                $course->rating= floor($reviewsSum/$nbReviews);
            }
            else{
                $course->rating=$review->rating;
            }
            $course->save();

            return response()->json(['message'=>'review uploaded successfully! '.$nbReviews]);

        }
        return response()->json(['message'=>'check your inputs!']);
    }
    //test done
    public function deleteReview($id){
        $rev = Review::find($id);
        if($rev){
            $rev->delete();
            return response()->json(['message'=>'record deleted!']);
        }
        return response()->json(['message'=>'no record found!']);
    }
    //test done
    public function likeReview($id){
        $rev = Review::find($id);
        if($rev){
            $rev->likes+=1;
            $rev->save();
            return response()->json(['message'=>'liked!']);
        }
        return response()->json(['message'=>'not found']);
    }
    //test done

    public function dislikeReview($id){
        $rev = Review::find($id);
        if($rev){
            if($rev->likes>0){
                $rev->likes-=1;
                $rev->save();
                return response()->json(['message'=>'disliked!']);
            }
            return response()->json(['message'=>'cannot dislike']);
        }
        return response()->json(['message'=>'not found']);
    }
    //check my reviews
    //test done
    public function getMyReviews(){
        if(Auth::check()){
            $reviewsList = Review::where('user_id','=',Auth::id())->get();
            if($reviewsList->count()>0){
                $reviewsListArray=json_decode($reviewsList,true);
                if($reviewsListArray == null){
                    throw new Exception('invalid JSON format');
                }
                return $reviewsListArray;
            }
            return response()->json(['message'=>'No reviews found']);
        }
        return response()->json(['message'=>'no user logged in']);
    }
    // test done (bya3mela l admin)
    public function getSpecificUserReviews($uid){
        $user = User::find($uid);
        if ($user) {
            // Check if the user has reviews
            if ($user->getReviews->count() > 0) {
                //return $user->getReviews;
                return response()->json(['reviews'=>$user->getReviews]);
            } else {
                return response()->json(['message' => 'No reviews found for this user.']);
            }
        }
        return response()->json(['message' => 'User not found.']);

    }
}
