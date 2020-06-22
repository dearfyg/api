<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\User;
class IndexController extends Controller
{
    //注册页面
    public function reg(){
        return view("login.reg");
    }
    //注册方法
    public function regdo(){
        //接值
        $data = request()->except("_token");
        //验证器
        request()->validate([
            'user_name'=>'unique:p_users',
            'user_email'=>'unique:p_users',
            'pwd'=>"regex:/^\w{6,}$/",
            'admin_pwd_confirmation'=>"same:pwd"
        ],[
            'user_name.unique'=>"用户名已存在",
            'user_email.unique'=>'邮箱已存在',
            'pwd.regex'=>'密码必须大于6个字符长度',
            'admin_pwd_confirmation.same'=>"俩次输入密码不符"
        ]);
        //密码加密
        $password = password_hash($data["pwd"],PASSWORD_DEFAULT);
        //入库
        $user = New User;
        $user->user_name = $data["user_name"];
        $user->password = $password;
        $user->user_email = $data["user_email"];
        $user->save();
        if($user){
            //写入注册时间
            $user->reg_time = time();
            $user->save();
            //成功跳转登录页面
            return redirect("/");
        }
    }
}
