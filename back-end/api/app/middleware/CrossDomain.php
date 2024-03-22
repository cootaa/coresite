<?php

/**
 * 允许跨域中间件
 * 
 * @author: dr
 * @createTime: 2024-02-20
 */

declare (strict_types = 1);
namespace app\middleware;

use Closure;
use think\Config;
use think\Request;
use think\Response;

class CrossDomain
{
    protected $cookieDomain;
    protected $header = [
        'Access-Control-Allow-Origin'     => '*',
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => '*',
    ];
    public function __construct(Config $config)
    {
        $this->cookieDomain = $config->get('cookie.domain', '');
    }
    /**
     * 允许跨域请求
     * @access public
     * @param Request $request
     * @param Closure $next
     * @param array   $header
     * @return Response
     */
    public function handle($request, Closure $next, ?array $header = [])
    {
        $header = !empty($header) ? array_merge($this->header, $header) : $this->header;
        if (!isset($header['Access-Control-Allow-Origin'])) {
            $origin = $request->header('origin');
            if ($origin && ('' == $this->cookieDomain || strpos($origin, $this->cookieDomain))) {
                $header['Access-Control-Allow-Origin'] = $origin;
            } else {
                $header['Access-Control-Allow-Origin'] = '*';
            }
        }
        if ($request->method(true) == 'OPTIONS') {
            return Response::create()->code(204)->header($header);
        }
        return $next($request)->header($header);
    }
}