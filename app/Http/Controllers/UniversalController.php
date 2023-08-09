<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
class UniversalController extends Controller
{
    //

    public function deleteMyAccount(){
        if(Auth::check()){
            $myAccount = User::find(Auth::id());
            Session::flush();
            Auth::logout();
            $myAccount->delete();
            return response()->json(['message'=>'Your Account was deleted']);
        }
        return response()->json(['message'=>'cannot perform deletion']);
    }

    public function logOut(){
        Session::flush();
        Auth::logout();
    }
}
