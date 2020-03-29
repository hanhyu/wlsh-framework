<?php

namespace App\Controllers;

use App\Library\DI;
use Swoole\Coroutine;
use Swoole\Http\Response;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-7-25
 * Time: 上午10:38
 */
class ErrorController
{
    /**
     * @var Response
     */
    private Response $response;
    protected $cid;

    public function __construct()
    {
        $this->cid      = Coroutine::getCid();
        $this->response = DI::get('response_obj' . $this->cid);
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
     * UserDomain: hanhyu
     * Date: 19-5-24
     * Time: 下午4:09
     */
    public function routerAction(): void
    {
        $server = DI::get('server_obj');

        //todo 不同协议获取的fd参数是不同的
        $fd = DI::get('fd_int' . $this->cid);

        if (!empty($fd) and $server->isEstablished($fd)) {
            $server->push($fd, ws_response(400, '', '请求的接口不存在'));
        } else {
            $this->response->end(http_response(400, '请求的接口不存在'));
        }
    }

    /**
     * 接口请求的method
     * UserDomain: hanhyu
     * Date: 19-5-24
     * Time: 下午4:09
     */
    public function methodAction(): void
    {
        $fd     = DI::get('fd_int' . $this->cid);
        $server = DI::get('server_obj');

        if ($server->isEstablished($fd)) {
            $server->push($fd, ws_response(400, '', '请求的方法不正确'));
        } else {
            $this->response->end(http_response(400, '请求的方法不正确'));
        }
    }

}
