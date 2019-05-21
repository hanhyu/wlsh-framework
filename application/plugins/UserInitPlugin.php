<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-11-1
 * Time: 下午5:50
 */

namespace App\Plugins;

use Yaf\Registry;

class UserInitPlugin extends \Yaf\Plugin_Abstract
{
    /**
     *
     * @param \Yaf\Request_Abstract  $request
     * @param \Yaf\Response_Abstract $response
     *
     * @return bool|void
     * @throws \Exception
     */
    public function routerStartup(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
        $uri    = $request->getRequestUri() ?? '0';
        $method = $request->getMethod() ?? '0';

        /**
         * $arr[1] module
         * $arr[2] controller
         * $arr[3] action
         */
        if ($uri) {
            $router = Registry::get('router_filter_config')->toArray();
            if (!isset($router[$uri])) { //请求的路由没有配置
                $request->setRequestUri('/Error/fail');
            } else if ($method !== $router[$uri]['method']) { //请求的方法是否正确
                $request->setRequestUri('/Error/fail');
            } else if ('*' === $router[$uri]['action']) { //默认转发请求的路由
                $this->authToken($router, $uri);
                $request->setRequestUri($uri);
            } else { //转发指定的路由
                $this->authToken($router, $uri);
                $request->setRequestUri($router[$uri]['action']);
            }
        }
    }

    /**
     *
     * @param \Yaf\Request_Abstract  $request
     * @param \Yaf\Response_Abstract $response
     *
     * @return bool|void
     */
    public function routerShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
        if (!empty($request->getRequestUri())) {
            //标记uri中是否存在下划线
            $flg = 0;
            if (strpos($request->getRequestUri(), '_')) $flg = 1;
            $request_uri = explode('/', $request->getRequestUri());

            if (count($request_uri) > 3) {
                if ($flg) {
                    $request->module     = convert_string($request_uri[1], true);
                    $request->controller = convert_string($request_uri[2], true);
                    $request->action     = convert_string($request_uri[3], false);
                } else {
                    $request->controller = ucfirst($request_uri[2]);
                }
            } else {
                if ($flg) {
                    $request->controller = convert_string($request_uri[1], true);
                    $request->action     = convert_string($request_uri[2], false);
                } else {
                    $request->controller = ucfirst($request_uri[1]);
                }
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
     * @param \Yaf\Request_Abstract  $request
     * @param \Yaf\Response_Abstract $response
     *
     * @return bool|void
     */
    public function dispatchLoopStartup(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
    }

    /**
     * 分发之前触发
     *
     * @param \Yaf\Request_Abstract  $request
     * @param \Yaf\Response_Abstract $response
     *
     * @return bool|void
     */
    public function preDispatch(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
    }

    /**
     * 分发结束之后触发
     *
     * @param \Yaf\Request_Abstract  $request
     * @param \Yaf\Response_Abstract $response
     *
     * @return bool|void
     */
    public function postDispatch(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
    }

    /**
     * 分发循环结束之后触发
     *
     * @param \Yaf\Request_Abstract  $request
     * @param \Yaf\Response_Abstract $response
     *
     * @return bool|void
     */
    public function dispatchLoopShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
    }

    /**
     *
     * 请求授权的token,过滤掉配置中不需要授权认证的路由与合法性
     *
     * @param $router
     * @param $uri
     *
     * @throws \Exception
     */
    private function authToken($router, $uri): void
    {
        //该接口是否需要token认证
        if ($router[$uri]['auth']) {
            //验证授权token的合法性与过期时间
            $headers = Registry::get('request')->header;
            $token   = $headers['authorization'] ?? '0';

            $res = validate_token($token);
            if ($res != '0') {
                throw new \Exception($res, 300);
            }
        }
    }

}