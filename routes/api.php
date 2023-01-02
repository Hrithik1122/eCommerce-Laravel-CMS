<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post("placeorder","API\ApiController@postplaceorder");
Route::post("addreview","API\ApiController@postreview");
Route::any("userregister","API\ApiController@userregister");
Route::any("editprofile","API\ApiController@editprofile");
Route::any("forgotpassword","API\ApiController@forgotpassword");
Route::get("categoryoffer","API\ApiController@categoryoffer");
Route::any("bestselling/{user_id}","API\ApiController@bestselling");
Route::any("mainoffers","API\ApiController@mainoffers");
Route::get("viewproduct/{id}/{user_id}","API\ApiController@viewproduct");
Route::any("productfilter","API\productfilterController@productfilter");
Route::any("productfilter1","API\productfilterControllercopy@productfilter");
Route::any("addwish","API\ApiController@addwish");
Route::get("login","API\ApiController@Showlogin");
Route::any("verifiedcoupon","API\ApiController@verifiedcoupon1");
Route::any("getwishlist","API\ApiController@getwishlist");
Route::any("getcolorls","API\productfilterController@getcolorls");
Route::get("gettax","API\ApiController@taxlist");
Route::get("vieworder/{id}","API\OrderController@vieworder");
Route::get("order_history/{id}","API\OrderController@order_history");
Route::any("addcart","API\CartDataController@addcart");
Route::get("getcart/{id}","API\CartDataController@getcart");
Route::get("offers/{user_id}/{page_no}","API\ApiController@showoffers");
Route::any("searchproduct","API\ApiController@searchproduct");
Route::get("page/{id}","API\ApiController@viewpage");
Route::get("removecart/{id}","API\CartDataController@removecart");
Route::get("addcomplain","API\ApiController@addcomplain");
Route::get("save_token","API\ApiController@save_token");
Route::get("gethelp/{id}","API\ApiController@gethelp");
Route::get("order_cancle_by_user","API\ApiController@order_cancle_by_user");