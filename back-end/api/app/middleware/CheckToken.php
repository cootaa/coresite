<?php

/**
 * 检查token中间件
 * 
 * @author: dr
 * @createTime: 2024-02-20
 */

namespace app\middleware;
use think\Exception;
use think\facade\Request;
use app\controller\Common;

class CheckToken
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //toke 合法性验证
        $header = $request->header();

        //判读请求头里有没有token
        if(!isset($header['token'])){
            return json(['code'=>440,'msg'=>'request must with token']);
        }

        //获取token
        $token = $header['token'];

        try {
            // token 合法
            $token = Common::checkToken($token);
        }catch (\Exception $e){
            return json(['code'=>440,'msg'=>'invalid token']);
        }

        return $next($request);
    }
}