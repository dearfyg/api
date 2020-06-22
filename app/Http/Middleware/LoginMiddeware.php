<?php

namespace App\Http\Middleware;

use Closure;

class LoginMiddeware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //获取session
        $user = session("name");
        if(!$user){
            //没有则提示登录。让其到登录页面
            return redirect('/index/login')->with("msg","请您先登录");
        }else{
            return $next($request);
        }
    }
}
