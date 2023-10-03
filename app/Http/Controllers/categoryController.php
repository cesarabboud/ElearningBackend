<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
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

    public function getAllCat(){
        $catList = Category::withCount('getCourses')->get();
        return response()->json(['catList'=>$catList,'count'=>$catList->count()]);
    }
    public function getCoursesByCat($catId){
        $cat = Category::find($catId);
        if($cat){
            $courses = Course::where('category_id',$cat->id)->with('getCategory')->with('getUser')->get();
            $groupedCourses = [];

            foreach ($courses as $course) {
                $type = $course->type;

                if (!isset($groupedCourses[$type])) {
                    $groupedCourses[$type] = [];
                }

                $groupedCourses[$type][] = $course;
            }
            return response()->json(['courses'=>$groupedCourses,'count'=>$courses->count()]);
        }
        return response()->json(['msg'=>'category not found!']);
    }
}
