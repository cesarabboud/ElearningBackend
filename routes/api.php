<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\testingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\UniversalController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('userss',[testingController::class,'getallUsers']);
Route::get('deleteUser/{id}',[testingController::class,'deleteUser']);

/* Routes for login/register */

Route::post('login',[AuthController::class,'login']);
Route::post('register',[AuthController::class,'register']);
Route::get('getLoggedInUser',[AuthController::class,'getLoggedInUser']);
Route::post('logout',[UniversalController::class,'logOut'])->middleware('auth:sanctum');
Route::group(['middleware' => ['auth:sanctum']], function () {

});
Route::get('getLoggedInUserDetails',[UniversalController::class,'getLoggedInUserDetails'])->middleware('auth:sanctum');
//----
Route::get('getCourseRev/{id}',[ReviewController::class,'getCourseReviews']);
Route::get('getSpecificUserRev/{id}',[ReviewController::class,'getSpecificUserReviews']);
Route::get('deleteReview/{id}',[ReviewController::class,'deleteReview']);
Route::post('postReview/{id}',[ReviewController::class,'postReview']);
Route::get('likeReview/{id}',[ReviewController::class,'likeReview']);
Route::get('dislikeReview/{id}',[ReviewController::class,'dislikeReview']);
Route::get('getMyReviews',[ReviewController::class,'getMyReviews']);


Route::get('getrevreplies/{id}',[ReplyController::class,'getReviewReplies']);
Route::get('getReviewReplies/{id}',[ReplyController::class,'getReviewReplies']);
Route::get('dislikeReply/{id}',[ReplyController::class,'dislikeReply']);
Route::get('likeReply/{id}',[ReplyController::class,'likeReply']);
Route::post('deleteReply/{id}',[ReplyController::class,'deleteReply']);
Route::post('addReply/{id}',[ReplyController::class,'addReply']);


Route::post('storeCategory',[categoryController::class,'storeCategory']);
Route::get('allCategories',[categoryController::class,'getCategories'])->middleware('auth:sanctum');



Route::get('getRepliesOfReview/{id}',[AdminController::class,'getRepliesOfReview']);
Route::get('getStudentDetails/{id}',[AdminController::class,'getStudentDetails']);
Route::get('getAllCourses',[AdminController::class,'getAllCourses']);
Route::get('getAllStudents',[AdminController::class,'getStudents']);
Route::get('getAllTeachers',[AdminController::class,'getInstructors']);
Route::get('getPDFs',[CourseController::class,'getPDFs']);
Route::get('getVideos',[CourseController::class,'getVideos']);

Route::get('displayCart',[CartController::class,'displayCart']);
Route::get('removeAll',[CartController::class,'RemoveAll']);

Route::get('HomeScr',[StudentController::class,'GetHomeScreenData']);
Route::get('getTopRated',[StudentController::class,'getTopRated']);
Route::get('getInstructorProfileInfo',[InstructorController::class,'getInstructorProfileInfoStudents'])->middleware('auth:sanctum');
Route::get('deleteAcc',[UniversalController::class,'deleteMyAccount'])->middleware('auth:sanctum');
Route::get('deleteAccUser/{id}',[AdminController::class,'deleteUser'])->middleware('auth:sanctum');
