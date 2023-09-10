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
        $imageUrl = 'https://www.transparentpng.com/download/user/gray-user-profile-icon-png-fP8Q1P.png';
        $imageData = file_get_contents($imageUrl);
        $fileName = time() . '_myImage.png';
        $filePath = public_path('uploads/' . $fileName);
        $tosave = 'uploads/'.$fileName;
        file_put_contents($filePath, $imageData);
        error_log('welcome 2 ');
        $request->validate([
            'name'=>'required',
            'email'=>'required',
            'password'=>'required',
            'role_id'=>'required'
        ]);
            $user = new User([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
                'profilepicture'=>$tosave,
                'role_id'=>$request->role_id
            ]);


            error_log('testinggg');
            error_log($request->input('password'));
            $user->save();
            $token = $user->createToken('auth_token')->plainTextToken;

            error_log('tok: '.$token);
            $credentials = $request->only('email', 'password');
            return Auth::attempt($credentials) ?
                response()->json(['username'=>Auth::user()->name,
                    'message'=>'logged in successfully',
                    'code'=>200,
                    'token'=>$token
                ]):
                response()->json(['message'=>'unsuccessful login','code'=>401]);

        //return response()->json(['message'=>'check your input fields !']);
        /*if (Auth::attempt($credentials)) {
            return response()->json(['username'=>Auth::user()->name,'message'=>'logged in successfully','code'=>200]);
        }
        return response()->json(['message'=>'unsuccessful login','code'=>401]);*/


    }
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
