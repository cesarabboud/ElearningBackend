<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class categoryController extends Controller
{
    //
    //test done
    public function storeCategory(Request $request)
    {
        if($request->name!='' || $request->name!=null){
            $obj = new Category();
            $obj->name = $request->name;
            $obj->save();
            return response()->json(['message'=>'category added !']);
        }
        return response()->json(['message'=>'empty field!']);
    }
    public function getCategories(){
        $allCategories = Category::all();
        foreach ($allCategories as $a){
            error_log($a['name']);
        }
        //dd(count($allCategories->toArray()));
        //dd(response()->json(['msg'=>'test'])->content());
        return $allCategories->toArray();
    }
}
