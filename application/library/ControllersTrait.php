<?php
declare(strict_types=1);

namespace App\library;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-12-12
 * Time: 下午6:38
 */
trait ControllersTrait
{
    /**
     * @var \Swoole\WebSocket\Server
     */
    private $server;
    /**
     * @var \Swoole\Http\Request
     */
    private $request;
    /**
     * @var \Swoole\Http\Response
     */
    private $response;
    /**
     * @var \Swoole\Atomic
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
        $this->server   = \Yaf\Registry::get('server');
        $this->request  = \Yaf\Registry::get('request');
        $this->response = \Yaf\Registry::get('response');
        $this->atomic   = \Yaf\Registry::get('atomic');

        if ($log) {
            $req_method = $this->request->server['request_method'];
            switch ($req_method) {
                case 'GET':
                    co_log($this->request->get, "{$this->request->server['path_info']} client get data:");
                    break;
                case 'POST':
                    $content_type = $this->request->header['content-type'] ?? 'x-www-form-urlencoded';
                    $let          = stristr($content_type, 'json');
                    if ($let) {
                        co_log($this->request->rawContent(), "{$this->request->server['path_info']} client json data:");
                    } else {
                        co_log($this->request->post, "{$this->request->server['path_info']} client post data:");
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
                            "{$this->request->server['path_info']} client put data:"
                        );
                    } else {
                        $data += $this->request->post;
                    }
                    co_log($data, "{$this->request->server['path_info']} client put data:");
                    break;
                case 'DELETE':
                    co_log($this->request->get, "{$this->request->server['path_info']} client delete data:");
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
        $res  = '参数错误';
        $data = $this->getParams();

        //如果是登录接口，则需解密接口数据
        if ($this->request->server['request_uri'] == '/system/user/login') {
            $decrypt = private_decrypt($data['login_data'], \Yaf\Registry::get('config')->sign->prv_key);
            $data = json_decode($decrypt, true);
        }

        if (!empty($data)) {
            try {
                $obj  = '\App\Models\Forms\\' . $forms;
                $form = new $obj($action, $data);
            } catch (\Exception $e) {
                $res = $e->getMessage();
            } finally {
                if (isset($form)) {
                    if (!$form->validate()) {
                        $res = $form->getMessages();
                    } else {
                        $res = (array)$form->getFieldValue();
                    }
                }
            }
        }

        if (is_string($res)) {
            throw new \Exception($res, 400);
        } else {
            return $res;
        }
    }

    /**
     * 根据用户登录的token设置缓存
     *
     * @param string $cache_key 缓存key，手机app默认缓存key为appToken
     * @param string $value     用户登录token
     */
    public function setTokenCache(string $cache_key, string $value): void
    {
        $token_params = get_token_params($value);
        try {
            $this->redis = \Yaf\Registry::get('redis_pool')->get();
        } catch (\Exception $e) {
            co_log($e->getMessage(), 'setTokenCache redis数据连接失败');
        }
        $res = $this->redis->hSet($cache_key, $value, $token_params['id']);
        \Yaf\Registry::get('redis_pool')->put($this->redis);
        if ($res === false) {
            co_log('用户登录token缓存失败', 'setTokenCache 存入缓存失败');
        }
    }

    /**
     * 根据用户登录token删除缓存中的用户登录token信息
     *
     * @param string $cache_key 缓存key，手机app默认缓存key为appToken
     * @param string $value     用户登录的token
     */
    public function delTokenCache(string $cache_key, string $value): void
    {
        try {
            $this->redis = \Yaf\Registry::get('redis_pool')->get();
        } catch (\Exception $e) {
            co_log($e->getMessage(), 'delTokenCache redis数据连接失败');
        }
        $res = $this->redis->hDel($cache_key, $value);
        \Yaf\Registry::get('redis_pool')->put($this->redis);
        if ($res === false) {
            co_log('用户登录token缓存删除失败', 'delTokenCache 删除缓存失败');
        }
    }

    /**
     * 根据用户ID删除缓存中的用户登录token信息
     *
     * @param string $cache_key 缓存key，手机app默认缓存key为appToken
     * @param int    $id        用户表ID
     */
    public function delUserCache(string $cache_key, int $id): void
    {
        try {
            $this->redis = \Yaf\Registry::get('redis_pool')->get();
        } catch (\Exception $e) {
            co_log($e->getMessage(), 'delUserCache redis数据连接失败');
        }
        $res = $this->redis->hGetAll($cache_key);
        foreach ($res as $k => $v) {
            if ($v == $id) {
                $this->redis->hDel($cache_key, $k);
            }
        }
        \Yaf\Registry::get('redis_pool')->put($this->redis);
        if ($res === false) {
            co_log('获取用户登录token缓存失败', 'delUserCache 获取缓存失败');
        }
    }

}