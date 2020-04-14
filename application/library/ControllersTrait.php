<?php
declare(strict_types=1);

namespace App\Library;

use App\Models\Forms\FormsVali;

use Exception;
use Swoole\Atomic;
use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Server;
use Throwable;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-12-12
 * Time: 下午6:38
 */
trait ControllersTrait
{
    protected Server $server;
    protected Request $request;
    protected Response $response;
    protected Atomic $atomic;
    protected int $cid;

    /**
     * 实现aop编程前置方法，供yaf控制器初始化中使用。
     *
     * @param bool $log 在请求的数据长度太长时可以手动设置不记录日志，默认true自动记录。
     */
    public function beforeInit(bool $log = true): void
    {
        $this->cid      = Coroutine::getCid();
        $this->server   = DI::get('server_obj');
        $this->request  = DI::get('request_obj' . $this->cid);
        $this->response = DI::get('response_obj' . $this->cid);
        $this->atomic   = DI::get('atomic_obj');

        $client_ip = get_ip($this->request->server);
        $server_ip = swoole_get_local_ip()['eth0'];
        $info      = "【req_uri】{$this->request->server['request_uri']}【client_ip】{$client_ip}【server_ip】{$server_ip}";

        if ($log) {
            $req_method  = $this->request->server['request_method'];
            $request_uri = explode('/', $this->request->server['request_uri']);
            $channel     = $request_uri[1] ?? 'system';
            switch ($req_method) {
                case 'GET':
                    $data = $this->request->get;
                    break;
                case 'POST':
                    $content_type = $this->request->header['content-type'] ?? 'x-www-form-urlencoded';
                    $let          = stristr($content_type, 'json');

                    if ($let) {
                        $data = json_decode($this->request->rawContent(), true, 512, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                    } else {
                        $data = $this->request->post;
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
                        $data += json_decode($this->request->rawContent(), true, 512, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                    } else {
                        $data += $this->request->post;
                    }

                    break;
                case 'DELETE':
                    $data = $this->request->get;
                    break;
                default:
                    $data = $req_method;
                    break;
            }
            co_log($data, $info, $channel);
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
            case 'DELETE':
            case 'GET':
                if (!empty($this->request->get)) {
                    $data = $this->request->get;
                }
                break;
            case 'POST':
                $content_type = $this->request->header['content-type'] ?? 'x-www-form-urlencoded';
                $let          = stristr($content_type, 'json');
                if ($let) {
                    if (!empty($this->request->rawContent())) {
                        try {
                            $data = json_decode($this->request->rawContent(), true, 512, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                        } catch (Throwable $e) {
                            $data = [];
                        }
                    }
                } else if (!empty($this->request->post)) {
                    $data = $this->request->post;
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
                            $data += json_decode($this->request->rawContent(), true, 512, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                        } catch (Throwable $e) {
                            $data = [];
                        }
                    }
                } else if (!empty($this->request->post)) {
                    $data += $this->request->post;
                }
                break;
            default:
                break;
        }
        return $data;
    }

    /**
     * 接口（表单）参数验证过滤器
     *
     * @param array $validations
     *
     * @return array 返回验证过滤后的数据
     * @throws ProgramException
     * @throws ValidateException
     */
    public function validator(array $validations): array
    {
        $data = $this->getParams();

        if (empty($data)) {
            throw new ProgramException('参数错误', 400);
        }

        //如果是登录接口，则需解密接口数据
        //todo 优化到路由参数中sign的值控制是否需要进行解密操作
        if ($this->request->server['request_uri'] === '/system/user/login') {
            if (!isset($data['login_data']) or !is_string($data['login_data'])) {
                throw new ProgramException('参数错误', 400);
            }
            $decrypt = private_decrypt($data['login_data'], DI::get('config_arr')['sign']['prv_key']);
            $data    = json_decode($decrypt, true, 512, JSON_THROW_ON_ERROR);
        }

        $lang_code = $this->request->header['language'] ?? 'zh-cn';
        if (empty($lang_code)) {
            FormsVali::setLangCode('zh-cn');
        } else {
            FormsVali::setLangCode($lang_code);
        }

        try {
            $data = FormsVali::validate($data, $validations);
            $data = array_intersect_key($data, $validations);
        } catch (Exception $e) {
            throw new ValidateException($e->getMessage(), 400);
        }
        return $data;
    }

}
