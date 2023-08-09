<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class testingController extends Controller
{
    //
    public function getallUsers(){
        $obj = User::all();
        error_log('nbr of users = '.$obj->count());
        return $obj;
    }
    public function deleteUser($id){
        $usertoDelete=User::find($id);
        $usertoDelete->delete();
    }
}
