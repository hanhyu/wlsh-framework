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
     * @param bool $log_flag 默认true自动记录，false不记录日志
     *
     * @throws ProgramException
     */
    public function beforeInit(bool $log_flag = true): void
    {
        $this->cid      = Coroutine::getCid();
        $this->server   = DI::get('server_obj');
        $this->request  = DI::get('request_obj' . $this->cid);
        $this->response = DI::get('response_obj' . $this->cid);
        $this->atomic   = DI::get('atomic_obj');

        $this->atomic->add(1);

        DI::set('log_flag' . $this->cid, $log_flag);

        //$req_method = $this->request->server['request_method'];
        switch ($this->request->getMethod()) {
            case 'GET':
                if (!empty($this->request->get)) {
                    $this->data = $this->request->get;
                }
                break;
            case 'POST':
                $content_type = $this->request->header['content-type'] ?? 'x-www-form-urlencoded';
                $let          = stristr($content_type, 'json');

                if ($let) {
                    if (!empty($this->request->getContent())) {
                        try {
                            $this->data = json_decode($this->request->getContent(), true, 512, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                        } catch (JsonException) {
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
                    if (!empty($this->request->getContent())) {
                        try {
                            $this->data += json_decode($this->request->getContent(), true, 512, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                        } catch (JsonException) {
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

        DI::set('req_data' . $this->cid, $this->data);
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
        if (!empty($this->data)) {
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
