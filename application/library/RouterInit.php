<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-11-1
 * Time: 下午5:50
 */

namespace App\Library;

use Exception;
use Swoole\Coroutine;

class RouterInit
{
    //todo 协程模式下不支持反射路由
    /**
     * User: hanhyu
     * Date: 2019/12/4
     * Time: 下午10:05
     *
     * @param string $uri    请求的链接地址
     * @param string $method 请求的方法
     *
     * @throws Exception
     */
    public function routerStartup(string $uri, string $method): void
    {
        $request_uri = explode('/', $uri);
        /**
         * $arr[1] module
         * $arr[2] controller
         * $arr[3] action
         */
        if ($uri) {
            if ('Task' === $request_uri[1] or 'Finish' === $request_uri[1] or 'Close' === $request_uri[1]) {
                $this->routerShutdown($uri);
                return;
            }

            $router = DI::get('router_filter_config_arr');

            if (!isset($router[$uri])) { //请求的路由错误
                $uri = '/Error/router';
            } else if ($method !== $router[$uri]['method']) { //请求的方法不正确
                $uri = '/Error/method';
            } else {
                if ($router[$uri]['auth']) {
                    $this->authToken();
                }
                $uri = $router[$uri]['action'];
            }
            //默认转发请求的路由
            $this->routerShutdown($uri);
        }
    }

    public function routerShutdown($uri): void
    {
        if (!empty($uri)) {
            $request_uri = explode('/', $uri);
            switch (count($request_uri)) {
                case 5:
                    $ctrl   = 'App\Modules\\' . $request_uri[1] . '\Controllers\\' . $request_uri[2] . '\\' . $request_uri[3];
                    $action = $request_uri[4] . 'Action';
                    break;
                case 4:
                    $ctrl   = 'App\Modules\\' . $request_uri[1] . '\Controllers\\' . $request_uri[2];
                    $action = $request_uri[3] . 'Action';
                    break;
                default:
                    $ctrl   = 'App\Controllers\\' . $request_uri[1];
                    $action = $request_uri[2] . 'Action';
            }

            if (class_exists($ctrl)) {
                $class = new $ctrl();
                if (method_exists($class, $action)) {
                    $class->$action();
                }
            }
        }

        /*可以在这个钩子函数routerShutdown中做拦截处理，获取当前URI，以当前URI做KEY，判断是否存在该KEY的缓存，
        若存在则停止解析，直接输出页面，缓存数据页。
        或做防重复操作提交*/
        //todo 权限检查在这里，在此处加入路由权限组的钩子方法
    }

    /**
     * 请求该路由是否需要授权的token
     *
     * @throws Exception
     * @todo 还需要进一步验证token是否属于用户
     */
    private function authToken(): void
    {
        //验证授权token的合法性与过期时间
        $cid     = Coroutine::getCid();
        $headers = DI::get('request_obj' . $cid)->header;
        $token   = $headers['authorization'] ?? '0';

        $res = validate_token($token);
        if (!empty($res)) {
            throw new ProgramException($res['msg'], $res['code']);
        }
    }

}
