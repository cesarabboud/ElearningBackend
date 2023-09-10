<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItems;
use App\Models\Course;
use App\Models\CoursesOwned;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class CartController extends Controller
{
    //
    //test done
    public function displayCart()
    {
        $cartCourses = [];
        if (Auth::check()) {
            $cart = Cart::where('user_id', '=', Auth::id())->first();
            if ($cart) {
                error_log('cart is found');
                $cartItems = CartItems::where('cart_id', '=', $cart->id)->get();
                error_log('number of items in cart = ' . $cartItems->count());
                if($cartItems->count()!=0){
                    foreach ($cartItems as $ci){
                        $cartCourses[] = $ci->getCourse;
                    }
                    $prixtotal = collect($cartCourses)->sum('price');
                    error_log($prixtotal);
                    return response()->json([/*'cartitems'=>$cartItems,*/'cartCourses'=>$cartCourses,'prixtotal'=>$prixtotal]);
                }
                return response()->json(['cartCourses'=>$cartCourses,'message'=>'cart is empty!']);

                //return view('shoppingCart')->with('cartproducts', $cart->getProducts)->with('cartitems', $cartitemss);
            } else {
                $newcart = new Cart();
                $newcart->user_id = Auth::id();
                $newcart->save();
                return response()->json(['cartCourses'=>$cartCourses,'message'=>'cart created']);
            }
        }
        return response()->json(['message'=>'no logged in user']);
    }

    public function addItemToCart($id)
    {
        $cartisfound = false;
        // /** @var ShoppingCart|null $cart */
        $cart = null;
        if (Auth::user() != null) {
            //$cart=ShoppingCart::where('user_id','=',Auth::user()->id)->get();
            $carts = Cart::all();
            foreach ($carts as $c) {
                if ($c->user_id == Auth::id()) {
                    $cart = $c;
                    $cartisfound = true;
                    break;
                }
            }
            if ($cartisfound == false) {
                $newcart = new Cart();
                $newcart->user_id = Auth::id();
                $newcart->save();
                $cartitem = new CartItems();
                $cartitem->cart_id = $newcart->id;
                $cartitem->course_id = $id;
                $cartitem->save();
                return response()->json(['message'=>'item added to newly created cart!']);
            }

            $getCartItem = CartItems::where('cart_id', '=', $cart->id)->where('course_id', '=', $id)->first();
            $uId = Auth::id();
            $ownsCourse = CoursesOwned::where('course_id', $id)
                ->whereHas('getOrder.getUser', function ($query) use ($uId) {
                    $query->where('user_id', $uId);
                })
                ->exists();
            if($ownsCourse === false){
                error_log('course not bought before');
            }
            else{
                error_log('ownsCourse'.$ownsCourse);
            }

            if (!$getCartItem && !$ownsCourse) {
                $cartitem = new CartItems();
                $cartitem->cart_id = $cart->id;
                $cartitem->course_id = $id;
                $cartitem->save();
                error_log('cartItem saved !');
                return response()->json(['message'=>'item added']);
            }
            return response()->json(['message'=>'item already in cart!']);
        }
    }
    public function removeItemFromCart($id)
    {
        ///** @var ShoppingCart|null $cart */
        $cart = null;
        $cartisfound = false;
        if (Auth::user() != null) {
            //$cart=ShoppingCart::where('user_id','=',Auth::user()->id)->get();
            $carts = Cart::all();
            foreach ($carts as $c) {
                if ($c->user_id == Auth::id()) {
                    $cart = $c;
                    $cartisfound = true;
                    break;
                }
            }
        }
        if ($cartisfound == true) {
            $course = Course::find($id);
            $cartitem = CartItems::where('course_id', '=', $course->id)->where('cart_id', '=', $cart->id)->first();
            if($cartitem){
                error_log($cartitem->count());
                $cartitem->delete();
                error_log($cartitem->count());
                return response()->json(['cii'=>$cartitem,'message'=>'course removed from cart having id = '.$cartitem->course_id.' !']);
            }
            return response()->json(['message'=>'course not found']);
        }
    }
    //test done
    public function RemoveAll()
    {
        error_log('test1');
        if(Auth::check()){
            $cart = Cart::where('user_id', '=', Auth::id())->first();
            if($cart){
                $cartitems = CartItems::where('cart_id', '=', $cart->id)->get();
                if($cartitems->count()>0){
                    foreach ($cartitems as $ci) {
                        if ($ci->cart_id == $cart->id) {
                            error_log('test');
                            $ci->delete();
                        }
                    }
                    return response()->json(['message'=>'cart cleared!']);
                }
                return response()->json(['message'=>'no items in cart to be cleared !']);
            }
            return response()->json(['message'=>'you have no cart']);
        }
        return response()->json(['message'=>'no user logged in']);


        //return redirect(Route('shoppingCart'));
    }
    //user checkouts and cart is cleared (order is saved)
    public function clearCart() // + checkout (payment remaining)
    {
        $cart = Cart::where('user_id', '=', Auth::id())->first();
        $cartitems = CartItems::where('cart_id', '=', $cart->id)->get();
        $o = new Order();
        $o->orderDate = Carbon::now();
        $o->user_id = Auth::id();
        $o->save();

        foreach ($cartitems as $ci) {
            if ($ci->cart_id == $cart->id) {
                $ordcourse = new CoursesOwned();
                $ordcourse->order_id = $o->id;
                $ordcourse->course_id =$ci->course_id;
                $ordcourse->save();
            }
        }

        foreach ($cartitems as $ci) {
            if ($ci->cart_id == $cart->id) {
                $ci->delete();
            }
        }
        return response()->json(['message'=>'cart is now empty !']);
    }
    public function getCartItemsNbr(){
        $cart = Cart::where('user_id', '=', Auth::id())->first();
        $cartitems = CartItems::where('cart_id', '=', $cart->id)->get();
        if($cartitems->count()>0){
            return response()->json(['nbr'=>$cartitems->count()]);
        }
        return response()->json(['nbr'=>0]);
    }
}
