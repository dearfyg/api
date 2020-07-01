<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get("/goods","Goods\GoodsController@details");
Route::get("/test","Goods\GoodsController@redis");

Route::get("/index/reg","Login\IndexController@reg");//注册
Route::any("/index/regdo","Login\IndexController@regdo");//注册方法

Route::get("/index/login","Login\IndexController@login");//登录
Route::any("/index/logindo","Login\IndexController@logindo");//登录方法

Route::get("/index/user","Login\IndexController@user");//用户个人中心





Route::post("/api/regdo","Api\UserController@regdo");//接口注册方法
Route::post("/api/logindo","Api\UserController@logindo")->middleware("total");//接口登录方法

Route::middleware(['login',"total"])->prefix('api')->group(function () {
    Route::get("/center","Api\UserController@center");//个人中心
    Route::get("/order","Api\UserController@order");//订单
});


Route::get("sign","Api\UserController@sign");//签名
Route::get("encrypt","Api\UserController@encrypt");//非对称加密



/*
 * 测试
 * */
Route::prefix("test")->group(function(){
    Route::get("/sign","Api\TestController@sign");//签名
    Route::get("/serect","Api\TestController@serect");//验签
    Route::get("/encrypt","Api\TestController@encrypt");//对称加密
    Route::get("/encrypt1","Api\TestController@encrypt1");//非对称加密
});

