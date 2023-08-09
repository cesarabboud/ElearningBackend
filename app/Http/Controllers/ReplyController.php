<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ReplyController extends Controller
{
    //
    //test done
    public function getReviewReplies($rid){
        $review = Review::find($rid);
        if($review!=null){
            if ($review->getReplies->count()>0){
                return $review->getReplies;
            }
            return response()->json(['message'=>'no replies!']);
        }
        return response()->json(['message'=>'no review found!']);
    }
    //test done
    public function likeReply($replyId){
        $replytoLike = Reply::find($replyId);
        if($replytoLike){
            $replytoLike->likes+=1;
            $replytoLike->save();
            return response()->json(['message'=>'reply liked !']);
        }
        return response()->json(['message'=>'no reply found']);
    }
    //test done
    public function dislikeReply($replyId){
        $replytoLike = Reply::find($replyId);
        if($replytoLike){
            if($replytoLike->likes>0){
                $replytoLike->likes-=1;
                $replytoLike->save();
                return response()->json(['message'=>'reply disliked !']);
            }
            return response()->json(['message'=>'likes ='. $replytoLike->likes]);
        }
        return response()->json(['message'=>'no reply found']);
    }
    //test done
    public function deleteReply($replyId){
        $replyToDelete = Reply::find($replyId);
        if($replyToDelete){
            $replyToDelete->delete();
            return response()->json(['msg'=>'reply deleted']);
        }
        return response()->json(['msg'=>'no reply found']);
    }
    //test done
    public function addReply($rid,Request $request){
        $reviewToReply = Review::find($rid);
        if($reviewToReply!=null){
            $reply = new Reply();
            if($request->response!=''){
                $reply->response = $request->response;
                $reply->review_id=$reviewToReply->id;
                $reply->user_id=1;
                $reply->save();
                return response()->json(['message'=>'reply submitted']);
            }
            return response()->json(['message'=>'no reply submitted!']);
        }
        return response()->json(['message'=>'no review found']);

    }
}
