<?php
declare(strict_types=1);

namespace App\Library;

use App\Models\Forms\FormsVali;
use Swoole\WebSocket\Server;
use Yaf\Registry;

/**
 * Trait WebsocketTrait
 * @package App\Library
 */
trait WebsocketTrait
{
    /**
     * @var Server
     */
    private $server;
    private $fd;
    private $uri;
    private $data;

    /**
     * 实现aop编程前置方法，供yaf控制器初始化中使用。
     *
     * @param bool $log 在请求的数据长度太长时可以手动设置不记录日志，默认true自动记录。
     */
    public function beforeInit($log = true): void
    {
        $this->server = Registry::get('server');
        $this->fd     = (int)$this->getRequest()->getParam('fd');
        $this->data   = $this->getRequest()->getParam('data');

        Registry::get('atomic')->add(1);
        if ($log) co_log($this->data, "websocket send data:", 'ws');
    }

    /**
     * 接口（表单）参数验证过滤器
     *
     * @param string $forms  请求验证的表单类名
     * @param string $action 请求验证的具体方法名
     *
     * @return array 返回验证过滤后的数据
     * @throws \Exception
     */
    public function validator(string $forms, string $action): array
    {
        try {
            $data = json_decode($this->data, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            $data = [];
        }

        //todo 优化到路由参数中sign的值控制是否需要进行解密操作,数据加密与数据验证操作
        if (isset($data['sign'])) {
            if (!is_string($data['sign'])) {
                throw new \Exception('参数错误', 400);
            }
            $decrypt = private_decrypt($data['sign'], Registry::get('config')->sign->prv_key);
            $data    = json_decode($decrypt, true);
        }

        //如果参数lang_code设置了，则输出对应的信息模板
        if (isset($data['lang_code']) and !empty($data['lang_code'])) {
            FormsVali::setLangCode($data['lang_code']);
        }

        try {
            $class = '\App\Models\Forms\\' . $forms;
            $data  = FormsVali::validate($data, (new $class)::$action());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 400);
        }
        return $data;
    }

}