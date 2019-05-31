<?php
declare(strict_types=1);

namespace App\library;

use App\Models\Forms\FormsVali;

use Swoole\Atomic;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Server;
use Yaf\Registry;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-12-12
 * Time: 下午6:38
 */
trait ControllersTrait
{
    /**
     * @var Server
     */
    private $server;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Response
     */
    private $response;
    /**
     * @var Atomic
     */
    private $atomic;
    /**
     * @var \Redis
     */
    private $redis;

    /**
     * 实现aop编程前置方法，供yaf控制器初始化中使用。
     *
     * @param bool $log 在请求的数据长度太长时可以手动设置不记录日志，默认true自动记录。
     */
    public function beforeInit($log = true): void
    {
        $this->server   = Registry::get('server');
        $this->request  = Registry::get('request');
        $this->response = Registry::get('response');
        $this->atomic   = Registry::get('atomic');

        if ($log) {
            $req_method  = $this->request->server['request_method'];
            $request_uri = explode('/', $this->request->server['request_uri']);
            $channel     = $request_uri[1] ?? 'system';
            switch ($req_method) {
                case 'GET':
                    co_log($this->request->get, "{$this->request->server['request_uri']} client get data:", $channel);
                    break;
                case 'POST':
                    $content_type = $this->request->header['content-type'] ?? 'x-www-form-urlencoded';
                    $let          = stristr($content_type, 'json');
                    if ($let) {
                        co_log(
                            $this->request->rawContent(),
                            "{$this->request->server['request_uri']} client json data:",
                            $channel
                        );
                    } else {
                        co_log($this->request->post, "{$this->request->server['request_uri']} client post data:", $channel);
                    }
                    break;
                case 'PUT':
                    $data = [];
                    if (!empty($this->request->get)) {
                        $data = $this->request->get;
                    }
                    $content_type = $this->request->header['content-type'] ?? 'x-www-form-urlencoded';
                    $let          = stristr($content_type, 'json');
                    if ($let) {
                        co_log(
                            json_encode($data) . ': request rawContent is' . $this->request->rawContent(),
                            "{$this->request->server['request_uri']} client put data:",
                            $channel
                        );
                    } else {
                        $data += $this->request->post;
                    }
                    co_log($data, "{$this->request->server['request_uri']} client put data:", $channel);
                    break;
                case 'DELETE':
                    co_log($this->request->get, "{$this->request->server['request_uri']} client delete data:", $channel);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * 获取接口传入的参数
     * @return array
     */
    public function getParams(): array
    {
        $this->atomic->add(1);

        $data       = [];
        $req_method = $this->request->server['request_method'];
        switch ($req_method) {
            case 'GET':
                if (!empty($this->request->get)) $data = $this->request->get;
                break;
            case 'POST':
                $content_type = $this->request->header['content-type'] ?? 'x-www-form-urlencoded';
                $let          = stristr($content_type, 'json');
                if ($let) {
                    if (!empty($this->request->rawContent())) {
                        try {
                            $data = json_decode($this->request->rawContent(), true, 512, JSON_THROW_ON_ERROR);
                        } catch (\Throwable $e) {
                            $data = [];
                        }
                    }
                } else {
                    if (!empty($this->request->post)) $data = $this->request->post;
                }
                break;
            case 'PUT':
                if (!empty($this->request->get)) {
                    $data = $this->request->get;
                }
                $content_type = $this->request->header['content-type'] ?? 'x-www-form-urlencoded';
                $let          = stristr($content_type, 'json');
                if ($let) {
                    if (!empty($this->request->rawContent())) {
                        try {
                            $data += json_decode($this->request->rawContent(), true, 512, JSON_THROW_ON_ERROR);
                        } catch (\Throwable $e) {
                            $data = [];
                        }
                    }
                } else {
                    if (!empty($this->request->post)) $data += $this->request->post;
                }
                break;
            case 'DELETE':
                if (!empty($this->request->get)) $data = $this->request->get;
                break;
            default:
                break;
        }
        return $data;
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
        $data = $this->getParams();

        if (empty($data)) {
            throw new \Exception('参数错误', 400);
        }

        //如果是登录接口，则需解密接口数据
        //todo 优化到路由参数中sign的值控制是否需要进行解密操作
        if ($this->request->server['request_uri'] == '/system/user/login') {
            if (!isset($data['login_data']) or !is_string($data['login_data'])) {
                throw new \Exception('参数错误', 400);
            }
            $decrypt = private_decrypt($data['login_data'], Registry::get('config')->sign->prv_key);
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