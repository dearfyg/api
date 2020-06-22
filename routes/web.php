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

Route::get("/index/user","Login\IndexController@user")->middleware('login');//用户个人中心