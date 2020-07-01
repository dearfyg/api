<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
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
    //验证签名
    public function serect(){
        //key值
        $key = "12262115";
        //接收数据
        $data = request()->data;
        //签名
        $sign = request()->sign;
        //目前的签名
        $now_sign = sha1($data.$key);
        //判断签名是否一致
        if($sign==$now_sign){
            echo "验签通过";
        }else{
            echo "验签失败";
        }
    }
    //对称加密
    public function encrypt(){
        //数据
        $data = "我是野爹";
        //key
        $key = "1910-api";
        //加密方法
        $method = "AES-192-CBC";
        //初始化向量
        $vi="ABCDEFGHIJKLMNOP";
        //加密
        $encryp = openssl_encrypt($data,$method,$key,OPENSSL_RAW_DATA,$vi);
        //sign
        $sign = sha1($encryp.$key);
        //url
        $url = "http://api.com/encrypt";
        //传输的数据
        $data = [
            'data'=>$encryp,
            'sign'=>$sign
        ];
        //post传值
        $response = $this->post($url,$data);
        echo $response;
    }
    //非对称加密
    public function encrypt1(){
        //数据
        $data = "野爹";
        //获取公钥
        $pub_key = file_get_contents(storage_path('keys/pub.key'));
        //内容
        $pub_key = openssl_get_publickey($pub_key);
        //加密
        openssl_public_encrypt($data,$en_data,$pub_key);
        echo "加密后的数据：".$en_data."<hr>";
        //获取私钥
        $priv_key = file_get_contents(storage_path("keys/priv.key"));
        $priv_key = openssl_get_privatekey($priv_key);
        //反加密
        openssl_private_decrypt($en_data,$de_data,$priv_key);
        echo "反加密后的内容：".$de_data;
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
