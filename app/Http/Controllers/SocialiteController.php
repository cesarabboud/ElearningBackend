<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class SocialiteController extends Controller
{
    //
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $loginDetails = $request->only('email', 'password');
        if(Auth::check()){
            error_log('test 1 '.Auth::user()->name);
        }
        //token ta ya3moul request 3al api

        if (Auth::attempt($loginDetails)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            error_log($token);
            if(Auth::check()){
                error_log('id of user logged in = '.Auth::id());
            }
            return response()->json(
                ['username'=>Auth::user()->name,
                    'message' => 'login successful',
                    'role'=>Auth::user()->role_id,
                    'code' => 200,
                    'token'=>$token
                ]);
        }
        return response()->json(['message' => 'wrong login details', 'code' => 501]);
    }
}
