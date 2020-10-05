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
use JsonException;

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
    protected array $data = [];

    /**
     *
     * @param bool $log 在请求的数据长度太长时可以手动设置不记录日志，默认true自动记录。
     *
     * @throws JsonException
     */
    public function beforeInit(bool $log = true): void
    {
        $this->cid      = Coroutine::getCid();
        $this->server   = DI::get('server_obj');
        $this->request  = DI::get('request_obj' . $this->cid);
        $this->response = DI::get('response_obj' . $this->cid);
        $this->atomic   = DI::get('atomic_obj');

        $this->atomic->add(1);
        $client_ip = get_ip($this->request->server);
        $server_ip = null;
        $info      = "【req_uri】{$this->request->server['request_uri']}【client_ip】{$client_ip}【server_ip】{$server_ip}";

        $req_method  = $this->request->server['request_method'];
        $request_uri = explode('/', $this->request->server['request_uri']);
        $channel     = $request_uri[1] ?? 'system';
        switch ($req_method) {
            case 'GET':
                if (!empty($this->request->get)) {
                    $this->data = $this->request->get;
                }
                break;
            case 'POST':
                $content_type = $this->request->header['content-type'] ?? 'x-www-form-urlencoded';
                $let          = stristr($content_type, 'json');

                if ($let) {
                    if (!empty($this->request->rawContent())) {
                        try {
                            $this->data = json_decode($this->request->rawContent(), true, 512, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                        } catch (JsonException $e) {
                            throw new ProgramException('无法处理请求内容', 422);
                        }
                    }
                } else if (!empty($this->request->post)) {
                    $this->data = $this->request->post;
                }
                break;
            case 'PUT':
                if (!empty($this->request->get)) {
                    $this->data = $this->request->get;
                }

                $content_type = $this->request->header['content-type'] ?? 'x-www-form-urlencoded';
                $let          = stristr($content_type, 'json');

                if ($let) {
                    if (!empty($this->request->rawContent())) {
                        try {
                            $this->data += json_decode($this->request->rawContent(), true, 512, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                        } catch (JsonException $e) {
                            throw new ProgramException('无法处理请求内容', 422);
                        }
                    }
                } else if (!empty($this->request->post)) {
                    $this->data += $this->request->post;
                }
                break;
            case 'DELETE':
                if (!empty($this->request->get)) {
                    $this->data = $this->request->get;
                }
                break;
            default:
        }

        //HOOK开启状态的协程下不能使用c版的mongodb,需使用task进程模拟
        if ($log) task_log($this->server, $this->data, $info, $channel);
    }

    /**
     * 接口（表单）参数验证过滤器
     *
     * @param array $validations
     *
     * @return array 返回验证过滤后的数据
     * @throws ProgramException
     * @throws ValidateException|JsonException
     */
    public function validator(array $validations): array
    {
        if (empty($this->data)) {
            throw new ProgramException('参数错误', 400);
        }

        //如果是登录接口，则需解密接口数据
        //todo 优化到路由参数中sign的值控制是否需要进行解密操作
        if ($this->request->server['request_uri'] === '/system/user/login') {
            if (!isset($this->data['login_data']) or !is_string($this->data['login_data'])) {
                throw new ProgramException('参数错误', 400);
            }
            $decrypt    = private_decrypt($this->data['login_data'], DI::get('config_arr')['sign']['prv_key']);
            $this->data = json_decode($decrypt, true, 512, JSON_THROW_ON_ERROR);
        }

        $lang_code = $this->request->header['language'] ?? 'zh-cn';
        if (empty($lang_code)) {
            FormsVali::setLangCode('zh-cn');
        } else {
            FormsVali::setLangCode($lang_code);
        }

        try {
            $data = FormsVali::validate($this->data, $validations);
            unset($this->data);
            $data = array_intersect_key($data, $validations);
        } catch (Exception $e) {
            throw new ValidateException($e->getMessage(), 400);
        }
        return $data;
    }

}
