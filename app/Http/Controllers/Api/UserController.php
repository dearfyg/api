<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\Token;
use Illuminate\Support\Facades\Redis;
class UserController extends Controller
{
    //注册接口
    public function regdo(){
        //接值
        $pwd = request()->post("pwd");
        $pwd1 = request()->admin_pwd_confirmation;
        $user_name = request()->user_name;
        $user_email = request()->user_email;
        //验证器
        //参数不为空
        if($pwd==""||$pwd1==""||$user_name==""||$user_email==""){
            $response = [
                "code" => "50001",
                "msg" =>"参数不可为空,参数错误",
            ];
            return $response;
        }
        //密码长度
        if(strlen($pwd)<6){
            $response = [
                "code" => "50002",
                "msg" =>"密码长度必须大于六位",
            ];
            return $response;
        }
        //密码和确认密码
        if($pwd!=$pwd1){
            $response = [
                "code" => "50003",
                "msg" =>"俩次输入密码不符",
            ];
            return $response;
        }
        $name = User::where("user_name",$user_name)->first();
        //判断是否有用户名
        if($name){
            $response = [
                "code" => "50004",
                "msg" =>"用户名已存在",
            ];
            return $response;
        }
        $email = User::where("user_email",$user_email)->first();
        if($email){
            $response = [
                "code" => "50005",
                "msg" =>"邮箱号已存在",
            ];
            return $response;
        }
        //密码加密
        $pwd = password_hash($pwd,PASSWORD_DEFAULT);
        //入库
        $user = New User;
        $user->user_name = $user_name;
        $user->password = $pwd;
        $user->user_email = $user_email;
        $user->save();
        if($user){
            //写入注册时间
            $user->reg_time = time();
            $user->save();
            //成功跳转登录页面
            $response = [
                "code" => "0",
                "msg" =>"注册成功",
            ];
            return $response;
        }else{
            $response = [
                "code" => "50000",
                "msg" =>"注册失败",
            ];
            return $response;
        }
    }
    //登录接口
    public function logindo(){
        //接值
        $password = request()->password;
        $user_name = request()->user_name;
        //通过用户名查询数据库中是否有此数据
        $userInfo= User::where("user_name",$user_name)->first();
        //查询到后判断密码是否一致
        if($userInfo){
            if(password_verify($password,$userInfo->password)){
                //登录成功更新以下字段：last_login	最后登录时间 last_ip		最后登录的客户端IP
                $userInfo->last_login=time();
                $userInfo->last_ip=request()->getClientIp();
                $userInfo->save();
                //更新成功则登录
                if($userInfo){
                    //token
                    $token = $userInfo->user_name.$userInfo->user_id.time();
                    $token = substr(md5($token),0,10). substr(md5($token),10,26);
                    //将token和uid存入数据库
//                    $data = [
//                        "uid"=>$userInfo->user_id,
//                        "token"=>$token
//                    ];
//                    $res = Token::insert($data);
                        Redis::set($token,$userInfo->user_id);
                        //设置键的过期时间
                        Redis::expire($token,7200);
                        $response = [
                            "code"=>"0",
                            "msg"=>"登录成功",
                            "token"=>$token,
                        ];
                        return $response;
                }
            }else{
                //否则提示密码错误
                $response = [
                    "code"=>"50007",
                    "msg"=>"账号或密码错误"
                ];
                return $response;
            }
        }else{
            //否则提示密码错误
            $response = [
                "code"=>"50006",
                "msg"=>"账号或密码错误"
            ];
            return $response;
        }
    }
    //个人中心接口
    public function center(){
        $token = request()->token;
        //使用token对比数据库中数据
//        $res = Token::where("token","$token")->first();
        //redis中查询
        $uid = Redis::get($token);
        //判断
        if($uid){
            //查询数据库
            $userInfo = User::find($uid);
            //成功
            $response=[
                "code"=>"0",
                "msg"=>"个人中心"
            ];
            return $response;
        }else{
            //失败
            $response=[
                "code"=>"50008",
                "msg"=>"token不符"
            ];
            return $response;
        }
    }
    //订单中心
    public function order(){
        $arr = [
            "124897465157",
            "156486746123",
            "156789786145"
        ];
        $response = [
            'code'=>"0",
            'msg'=>"ok",
            "order"=>$arr
        ];
        return $response;
    }
    //发送签名
    public function sign(){
        //设置一个key保持安全
        $key = "12262115";
        $data ="施恩";
        $sign = sha1($data.$key);
        echo "<pre>". print_r($sign)."<pre>";
        //将数据和签名一起发送
        $url = "http://api.com/serect?data=".$data."&sign=".$sign;
        $response = file_get_contents($url);
        echo $response;
    }
    //非对称加密
    public function encrypt(){
        //发送的数据
        $data = "天王盖地虎";
        echo "口令：".$data."<hr>";
        //公钥
        $key_content = file_get_contents(storage_path("keys/b_pub.key"));
        //获取公钥证书
        $pub_key = openssl_get_publickey($key_content);
        //加密
        openssl_public_encrypt($data,$en_data,$pub_key);
        //base64编码
        $en_data= base64_encode($en_data);
        $data = [
            "data"=>$en_data,
        ];
        //调用的接口
        $url = "http://api.com/encrypt1";
        //调用post传值
        $response = $this->post($url,$data);
        //把response转为数组
        $response =json_decode($response,true);
        //解密数据
        //base64转码
        $ba_data =base64_decode($response["data"]);
        //获取私钥
        $priv_content = file_get_contents(storage_path("keys/priv.key"));
        //证书
        $priv_key = openssl_get_privatekey($priv_content);
        //解密
        openssl_private_decrypt($ba_data,$de_data,$priv_key);
        echo "回令：".$de_data;

    }
    //curlpost传值
    public function post($url,$data){
        //初始化
        $init = curl_init();
        //设置必要参数
        curl_setopt($init,CURLOPT_URL,$url);//url
        curl_setopt($init,CURLOPT_RETURNTRANSFER,1);//返回
        curl_setopt($init,CURLOPT_POST,1);//post传输
        curl_setopt($init,CURLOPT_POSTFIELDS,$data);//post传输的数据
        //执行
        $response = curl_exec($init);
        //错误号和错误信息
        $errno = curl_errno($init);
        $error = curl_error($init);
        //如果有错误号输出错误信息
        if($errno){
            echo $error;die;
        }
        //关闭
        curl_close($init);
        return $response;
    }
}
