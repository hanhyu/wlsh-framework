<?php

namespace App\controllers;

use Swoole\Coroutine;
use Swoole\Http\Response;
use Yaf\Registry;
use Yaf\Controller_Abstract;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-7-25
 * Time: 上午10:38
 */
class Error extends Controller_Abstract
{
    /**
     * @var Response
     */
    private $response;

    public function init()
    {
        $cid            = Coroutine::getCid();
        $this->response = Registry::get('response_' . $cid);
    }

    /**
     * @param $exception
     */
    public function errorAction($exception): void
    {
        $msg = $exception->getMessage() ?? '';
        $this->response->end(http_response(400, $msg));
    }

    /**
     * 触发此路由条件：请求的接口路由不正确
     * User: hanhyu
     * Date: 19-5-24
     * Time: 下午4:09
     */
    public function routerAction(): void
    {
        $fd     = $this->getRequest()->getParam('fd');
        $server = Registry::get('server');

        if ($server->isEstablished($fd)) {
            $server->push($fd, ws_response(400, '', '请求的接口不存在'));
        } else {
            $this->response->end(http_response(400, '请求的接口不存在'));
        }
    }

    /**
     * 接口请求的method
     * User: hanhyu
     * Date: 19-5-24
     * Time: 下午4:09
     */
    public function methodAction(): void
    {
        $fd     = $this->getRequest()->getParam('fd');
        $server = Registry::get('server');

        if ($server->isEstablished($fd)) {
            $server->push($fd, ws_response(400, '', '请求的方法不正确'));
        } else {
            $this->response->end(http_response(400, '请求的方法不正确'));
        }
    }


}