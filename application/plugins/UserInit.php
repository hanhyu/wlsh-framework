<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-11-1
 * Time: 下午5:50
 */

namespace App\Plugins;

use ProgramException;
use Yaf\{
    Registry,
    Plugin_Abstract,
    Request_Abstract,
    Response_Abstract,
};
use Exception;
use Swoole\Coroutine;

class UserInit extends Plugin_Abstract
{
    /**
     *
     * @param Request_Abstract  $request
     * @param Response_Abstract $response
     *
     * @return bool|void
     * @throws Exception
     * @todo 协程模式下不支持反射路由
     */
    public function routerStartup(Request_Abstract $request, Response_Abstract $response)
    {
        $uri    = $request->getRequestUri() ?? '0';
        $method = $request->getMethod() ?? '0';

        $request_uri = explode('/', $request->getRequestUri());
        /**
         * $arr[1] module
         * $arr[2] controller
         * $arr[3] action
         */
        if ($uri) {
            if ($request_uri[1] == 'task') return;

            $router = Registry::get('router_filter_config')->toArray();

            if (!isset($router[$uri])) { //请求的路由错误
                $uri = '/Error/router';
            } else if ($method !== $router[$uri]['method']) { //请求的方法不正确
                $uri = '/Error/method';
            } else {
                if ($router[$uri]['auth']) $this->authToken();
                $uri = $router[$uri]['action'];
            }
            //默认转发请求的路由
            $request->setRequestUri($uri);
        }
    }

    /**
     *
     * @param Request_Abstract  $request
     * @param Response_Abstract $response
     *
     * @return bool|void
     */
    public function routerShutdown(Request_Abstract $request, Response_Abstract $response)
    {
        if (!empty($request->getRequestUri())) {
            $request_uri = explode('/', $request->getRequestUri());
            switch (count($request_uri)) {
                case 5:
                    $request->controller = $request_uri[2] . '\\' . $request_uri[3];
                    $request->action     = $request_uri[4];
                    break;
                case 4:
                    $request->controller = $request_uri[2];
                    $request->action     = $request_uri[3];
                    break;
                default:
                    //$request->controller = ucfirst($request_uri[1]);
                    $request->controller = $request_uri[1];
                    $request->action     = $request_uri[2];
            }

        }

        /*可以在这个钩子函数routerShutdown中做拦截处理，获取当前URI，以当前URI做KEY，判断是否存在该KEY的缓存，
        若存在则停止解析，直接输出页面，缓存数据页。
        或做防重复操作提交*/
        //todo 权限检查在这里，在此处加入路由权限组的钩子方法
    }

    /**
     *分发循环开始之前被触发
     *
     * @param Request_Abstract  $request
     * @param Response_Abstract $response
     *
     * @return bool|void
     */
    public function dispatchLoopStartup(Request_Abstract $request, Response_Abstract $response)
    {
    }

    /**
     * 分发之前触发
     *
     * @param Request_Abstract  $request
     * @param Response_Abstract $response
     *
     * @return bool|void
     */
    public function preDispatch(Request_Abstract $request, Response_Abstract $response)
    {
    }

    /**
     * 分发结束之后触发
     *
     * @param Request_Abstract  $request
     * @param Response_Abstract $response
     *
     * @return bool|void
     */
    public function postDispatch(Request_Abstract $request, Response_Abstract $response)
    {
    }

    /**
     * 分发循环结束之后触发
     *
     * @param Request_Abstract  $request
     * @param Response_Abstract $response
     *
     * @return bool|void
     */
    public function dispatchLoopShutdown(Request_Abstract $request, Response_Abstract $response)
    {
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
        $headers = Registry::get('request_' . $cid)->header;
        $token   = $headers['authorization'] ?? '0';

        $res = validate_token($token);
        if (!empty($res)) {
            throw new ProgramException($res['msg'], $res['code']);
        }
    }

}