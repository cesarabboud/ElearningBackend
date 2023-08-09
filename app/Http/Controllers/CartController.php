<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItems;
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
        if (Auth::check()) {
            $cart = Cart::where('user_id', '=', Auth::id())->first();
            if ($cart) {
                error_log('cart is found');
                $cartItems = CartItems::where('cart_id', '=', $cart->id)->get();
                error_log('number of items in cart = ' . $cartItems->count());
                if($cartItems->count()!=0){
                    return response()->json(['cartitems'=>$cartItems]);
                }
                return response()->json(['message'=>'cart is empty!']);

                //return view('shoppingCart')->with('cartproducts', $cart->getProducts)->with('cartitems', $cartitemss);
            } else {
                $newcart = new Cart();
                $newcart->user_id = Auth::id();
                $newcart->save();
                return response()->json(['message'=>'cart created']);
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
            //$getCartItem!=null
            if (!$getCartItem) {
                $cartitem = new CartItems();
                $cartitem->cart_id = $cart->id;
                $cartitem->course_id = $id;
                $cartitem->save();
                return response()->json(['message'=>'item added']);
            }

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
            $cartitem = CartItems::where('course_id', '=', $id)->where('cart_id', '=', $cart->id)->first();
            $cartitem->delete();
        }
    }
    //test done
    public function RemoveAll()
    {
        error_log('test1');
        if(Auth::check()){
            $cart = Cart::where('user_id', '=', Auth::id())->first();

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
    }
}
