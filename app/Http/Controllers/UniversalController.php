<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Session;
use Laravel\Sanctum\PersonalAccessToken;
class UniversalController extends Controller
{
    //


    public function deleteMyAccount(){
        if(Auth::check()){
            error_log('ok delete');
            $myAccount = User::find(Auth::id());
            Session::flush();
            Auth::user()->tokens()->delete();
            $myAccount->delete();
            return response()->json(['message'=>'Your Account was deleted']);
        }
        return response()->json(['message'=>'cannot perform deletion']);
    }

    public function logOut(){
        Session::flush();
        error_log('id before logout :'.Auth::id());
        Auth::user()->tokens()->delete();
        return response()->json(['message'=>'logged out from app !']);
    }

    public function getLoggedInUserDetails(){
            error_log(Auth::id());
            return response()->json(['user'=>Auth::user()]);
//        $token='86ea86d0ba3eae0cb195c515da7bd42c3b6f8c207aceca8d2b74a98303317757';
//        error_log($token);
//        $hashedTokens = PersonalAccessToken::pluck('token');
//        return response()->json(['hashed'=>$hashedTokens]);
//        if(Sanctum::hasToken())
//        $tokenToFind = PersonalAccessToken::where('token','=',$token)->first();
//        error_log($tokenToFind->count());
//        $uid = $tokenToFind->tokenable_id;
//        $user = User::find($uid);
//        return response()->json(['tok'=>$tokenToFind,'user'=>$user]);
        //return response()->json(['msg'=>'hi']);
    }
}
