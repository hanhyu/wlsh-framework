<?php
declare(strict_types=1);

namespace App\Library;

use App\Models\Forms\FormsVali;
use Exception;
use Swoole\Coroutine;
use Swoole\WebSocket\Server;

/**
 * Trait WebsocketTrait
 * @package App\Library
 */
trait WebsocketTrait
{
    protected int $cid;
    /**
     * @var Server
     */
    protected Server $server;
    protected int $fd;
    protected string $uri;
    protected array $data;

    /**
     *
     * @param bool $log 手动设置记录日志，默认true自动记录。
     */
    public function beforeInit(bool $log = true): void
    {
        $this->cid    = Coroutine::getCid();
        $this->server = DI::get('server_obj');
        $this->fd     = DI::get('fd_int' . $this->cid);
        $this->data   = DI::get('ws_data_arr' . $this->cid);

        DI::get('atomic_obj')->add(1);

        if ($log) task_monolog($this->server, $this->data, 'websocket send data:', 'ws');
    }

    /**
     * 接口（表单）参数验证过滤器
     *
     * @param array $validations
     *
     * @return array 返回验证过滤后的数据
     * @throws Exception
     */
    public function validator(array $validations): array
    {
        if (empty($this->data['data'] ?? '')) {
            throw new ProgramException('参数错误', 400);
        }

        //todo 优化到路由参数中sign的值控制是否需要进行解密操作,数据加密与数据验证操作
        if (isset($this->data['sign'])) {
            if (!is_string($this->data['sign'])) {
                throw new ProgramException('参数错误', 400);
            }
            $decrypt    = private_decrypt($this->data['sign'], DI::get('config_arr')['sign']['prv_key']);
            $this->data = json_decode($decrypt, true, 512, JSON_THROW_ON_ERROR);
        }

        //如果参数lang_code设置了，则输出对应的信息模板
        if (isset($this->data['language']) and !empty($this->data['language'])) {
            DI::set('ws_language_str', $this->data['language']);
            FormsVali::setLangCode($this->data['language']);
        }

        try {
            $vali_data = FormsVali::validate($this->data['data'], $validations);
            unset($this->data['data']);
            $vali_data = array_intersect_key($vali_data, $validations);
        } catch (Exception $e) {
            throw new ValidateException($e->getMessage(), 400);
        }
        //url参数在入口判断过
        $this->uri = $this->data['uri'];

        return $vali_data;
    }

}
