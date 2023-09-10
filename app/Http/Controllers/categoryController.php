<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class categoryController extends Controller
{
    //
    //test done
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'=>'required'
        ]);
        if($request->has('name')){
            $existingCategory = Category::where('name', $request->name)->first();
            if(!$existingCategory){
                if($request->name!=''){
                    $obj = new Category();
                    $obj->name = $request->name;
                    $obj->save();
                    return response()->json(['message'=>'category added !']);
                }
                return response()->json(['message'=>'empty field!']);
            }
            return response()->json(['message'=>'category already exists']);
        }
        return response()->json(['message'=>'check your request!']);

    }
    public function getCategories(){
        if(Auth::check()){
            $allCategories = Category::distinct()->pluck('name');
            //dd(count($allCategories->toArray()));
            //dd(response()->json(['msg'=>'test'])->content());
            return response()->json(['categories'=>$allCategories]);
        }
        return response()->json(['message'=>'Unauthorized',401]);

    }
}
