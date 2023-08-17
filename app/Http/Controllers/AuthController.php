<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Testing\Fluent\Concerns\Has;
use mysql_xdevapi\DocResult;
class AuthController extends Controller
{
    //
    public function register(Request $request){
        //return response()->json(['messagee'=>'ok']);
        error_log('welcome 2 ');
        if($request->name!='' && $request->email!='' && $request->password!=''){
            $user = new User([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
                'profilepicture'=>'https://www.transparentpng.com/download/user/gray-user-profile-icon-png-fP8Q1P.png',
                'role_id'=>$request->role_id
            ]);
            error_log('testinggg');
            error_log($request->input('password'));
            $user->save();



            $credentials = $request->only('email', 'password');
            return Auth::attempt($credentials) ?
                response()->json(['username'=>Auth::user()->name,'message'=>'logged in successfully','code'=>200]):
                response()->json(['message'=>'unsuccessful login','code'=>401]);
        }
        return response()->json(['message'=>'check your input fields !']);
        /*if (Auth::attempt($credentials)) {
            return response()->json(['username'=>Auth::user()->name,'message'=>'logged in successfully','code'=>200]);
        }
        return response()->json(['message'=>'unsuccessful login','code'=>401]);*/


    }
    public function login(Request $request){

        $loginDetails = $request->only('email', 'password');
        if(Auth::check()){
            error_log('test 1 '.Auth::user()->name);
        }

        if (Auth::attempt($loginDetails)) {
            if(Auth::check()){
                error_log('id of user logged in = '.Auth::id());
            }
            return response()->json(
               ['username'=>Auth::user()->name,
                'message' => 'login successful',
                'role'=>Auth::user()->role_id,
                'code' => 200]);
        } else {
            return response()->json(['message' => 'wrong login details', 'code' => 501]);

        }


    }
}
