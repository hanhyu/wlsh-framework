<?php

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-7-25
 * Time: 上午10:38
 */
class ErrorController extends Yaf\Controller_Abstract
{
    /**
     * @var Swoole\Http\Response
     */
    private $response;

    public function init()
    {
        $this->response = \Yaf\Registry::get('response');
    }

    /**
     * @param $exception
     */
    public function errorAction($exception): void
    {
        echo $exception->getMessage() ?? '';
        //$this->response->end(http_response(400, $msg));
    }

    /**
     * 触发此路由条件：请求的接口方法或接口请求的method不正确
     */
    public function failAction(): void
    {
        $this->response->end(http_response(400, '请求的接口不存在'));
    }

}