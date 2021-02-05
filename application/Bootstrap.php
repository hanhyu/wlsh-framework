<?php
declare(strict_types=1);

use App\Library\{
    AutoReload,
    DI,
    MongoPool,
    ProgramException,
    RouterInit,
    ValidateException,
};
use Swoole\Atomic;
use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server\Task;
use Swoole\Table;
use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;

/**
 * 注意此类中的每一行代码请勿随意上下移动
 *
 * UserDomain: hanhyu
 * Date: 16-7-25
 * Time: 上午10:19
 */
class Bootstrap
{
    /**
     * @var Server
     */
    private Server $server;
    private Table $table;
    private Atomic $atomic;
    protected static $instance;

    public static function getInstance(): Bootstrap
    {
        if (empty(self::$instance) || !(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public function start(): void
    {
        $this->server = new Swoole\WebSocket\Server(
            '0.0.0.0',
            9770,
            SWOOLE_PROCESS,
            SWOOLE_SOCK_TCP
        //SWOOLE_SOCK_TCP | SWOOLE_SSL
        );

        //todo 这里的所有配置参数，不建议使用外部配置文件引入，直接使用统一的docker镜像，无需额外配置。
        $this->server->set([
            //'reactor_num' => 16,
            'worker_num'                 => 4,
            'task_worker_num'            => 4,
            'task_enable_coroutine'      => true,
            'daemonize'                  => SWOOLE_DAEMONIZE,
            //swoole4.4.2版本开始，已稳定内存，协程模式下无需再开启此参数。
            //'max_request'                => 300000,
            'enable_coroutine'           => true,
            'max_coroutine'              => 100000,
            'dispatch_mode'              => 2,
            'enable_reuse_port'          => false,
            'log_level'                  => SWOOLE_LOG_LEVEL,
            'trace_flags'                => SWOOLE_TRACE_ALL,
            'log_file'                   => ROOT_PATH . '/log/swoole.log',
            'pid_file'                   => ROOT_PATH . '/log/swoolePid.log',
            'package_max_length'         => 200000,
            'reload_async'               => true,
            'max_wait_time'              => 7,
            'heartbeat_idle_time'        => 600,
            'heartbeat_check_interval'   => 60,
            'buffer_output_size'         => 8 * 1024 * 1024,
            //'ssl_cert_file'              => ROOT_PATH . '/tests/opensslRsa/cert.crt',
            //'ssl_key_file'               => ROOT_PATH . '/tests/opensslRsa/rsa_private.key',
            //'ssl_ciphers'                => '',
            //'ssl_verify_peer'            => true,
            //'ssl_allow_self_signed'      => true,
            //'open_http2_protocol'        => true,
            //'open_mqtt_protocol'         => true,
            'open_websocket_close_frame' => true,
            'send_yield'                 => true,
            'hook_flags'                 => SWOOLE_HOOK_ALL,
            'user'                       => 'root',
            'group'                      => 'root',
        ]);

        $this->table = new Table(1024);
        $this->table->column('key', Table::TYPE_STRING, 20);
        $this->table->column('value', Table::TYPE_INT, 128);
        $this->table->create();

        $this->atomic = new Atomic();

        /* $this->server->addListener('0.0.0.0', 9771, SWOOLE_SOCK_TCP)->set([
             'open_length_check'     => true,
             'package_length_type'   => 'N',
             'package_length_offset' => 0,
             'package_body_offset'   => 4,
         ]);*/

        $this->server->on('start', [$this, 'onStart']);
        $this->server->on('managerStart', [$this, 'onManagerStart']);
        $this->server->on('workerStart', [$this, 'onWorkerStart']);
        $this->server->on('workerStop', [$this, 'onWorkerStop']);
        $this->server->on('workerExit', [$this, 'onWorkerExit']);
        $this->server->on('handShake', [$this, 'onHandShake']);
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->on('receive', [$this, 'onReceive']);
        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('finish', [$this, 'onFinish']);
        $this->server->on('close', [$this, 'onClose']);
        $this->server->on('workerError', [$this, 'onWorkerError']);
        $this->server->start();
    }

    /**
     * @param Server $server
     */
    public function onStart(Server $server): void
    {
        echo 'Swoole tcp server is started at tcp://127.0.0.1:9771' . PHP_EOL;
        echo 'Swoole http|ws server is started at http|s://127.0.0.1:9770' . PHP_EOL;
    }

    public function onManagerStart(Server $server): void
    {

    }

    /**
     * @param Server $server
     * @param int    $worker_id
     *
     */
    public function onWorkerStart(Server $server, int $worker_id): void
    {
        /* array(3) {
                 [0]=>
           string(26) "/home/baseFrame/swoole.php"
                 [1]=>
           string(46) "/home/baseFrame/application/Bootstrap.php"
                 [2]=>
           string(50) "/home/baseFrame/application/library/AutoReload.php"
         }

         var_dump(get_included_files());*/

        require_once ROOT_PATH . '/vendor/autoload.php';
        require_once LIBRARY_PATH . '/common/functions.php';

        //用inotify监听mvc目录,一有变动就自动重启脚本
        if (0 === $worker_id) {
            $kit = new AutoReload($server->master_pid);
            $kit->watch(CONF_PATH);
            $kit->watch(APP_PATH);
            $kit->addFileType('.php');
            //$kit->run();
        }

        //重命名进程名字
        if ($server->taskworker) {
            swoole_set_process_name('swooleTaskProcess');
        } else {
            swoole_set_process_name('swooleWorkerProcess');
        }

        DI::set('server_obj', $server);
        DI::set('table_obj', $this->table);
        DI::set('atomic_obj', $this->atomic);

        //todo 这里当进程达到max_request设置数量
        try {
            require_once CONF_PATH . DS . 'environ.php';
            require_once CONF_PATH . DS . 'language.php';

            //把配置保存起来
            DI::set('config_arr', array_merge(
                require CONF_PATH . DS . 'common.php',
                require CONF_PATH . DS . CURRENT_ENV . '.php'
            ));

            require_once CONF_PATH . DS . 'di.php';
        } catch (Throwable $e) {
            print_r($e . PHP_EOL);
            $this->server->shutdown();
        }


        /*
         * 默认第一个工作进程发送websocket控制流0x9 ping帧，
         * js客户端websocket底层会自动回复pong包，这样就不用上游业务层做心跳包检测。
         *
         * 下面设置了每30秒向websocket客户端发送一个ping帧，
         * 配合heartbeat_idle_time=>600与heartbeat_check_interval=>60两个参数。
         * 说明：wlsh默认配置为每60秒检测一遍所有客户端fd（http、websocket等tcp连接标识符），
         * 如发现该fd在600秒之内没有发送一条消息，则关闭该连接; 此处设置表示http长连接最多保活10分钟。
         *
         */
        if (0 === $worker_id) {
            $server->tick(30000, function () use ($server) {
                foreach ($server->connections as $fd) {
                    if ($this->server->isEstablished($fd)) {
                        $this->server->push($fd, true, 9);
                    }
                }
            });
        }
    }

    /**
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return bool
     */
    public function onHandShake(Request $request, Response $response): bool
    {
        //以get参数传递token，如： new WebSocket(`wss://127.0.0.1:9770?token=${token}`)
        $token = $request->get['token'] ?? '0';
        $res   = validate_token($token);
        if (!empty($res)) {
            $response->status(401);
            $response->end($res['msg']);
            return false;
        }

        /*
         * 以子协议传递token，客户端初始化时需要传第二个参数，如： new WebSocket('wss://127.0.0.1:9770', token)
        $token_protocol = $request->header['sec-websocket-protocol'] ?? null;
        if (!is_null($token_protocol)) {
            $res = validate_token(urldecode($token_protocol));
            if (!empty($res)) {
                $response->status(400);
                $response->end();
                return false;
            }
        } else {
            $response->status(400);
            $response->end();
            return false;
        }
        */

        // websocket握手连接算法验证
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten          = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            $response->end();
            return false;
        }
        $key = base64_encode(sha1(
            $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
            true
        ));

        $headers = [
            'Upgrade'               => 'websocket',
            'Connection'            => 'Upgrade',
            'Sec-WebSocket-Accept'  => $key,
            'Sec-WebSocket-Version' => '13',
            //'Sec-WebSocket-Protocol' => $token_protocol,
        ];

        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }

        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }

        $response->status(101);
        $response->end();

        $this->server->defer(function () use ($request) {
            $this->onOpen($this->server, $request);
        });
        return true;
    }

    /**
     * 用户创建socket连接，记录fd值
     *
     * @param Server  $server
     * @param Request $request
     */
    public function onOpen(Server $server, Request $request): void
    {
        /*
        Yaf\Registry::get('table')->set($request->fd, ['uid' => intval($request->get['uid'])]);
         if ($server->isEstablished($request->fd))
             $server->push($request->fd, ws_response(200, "wsConnect", '连接成功'));
        */
        echo '===============' . date('Y-m-d H:i:s') . '欢迎' . $request->fd . '进入==============' . PHP_EOL;
    }

    /**
     * websocket协议路由转接
     *
     * @param Server $server
     * @param Frame  $frame
     *
     * @throws JsonException
     */
    public function onMessage(Server $server, Frame $frame): void
    {
        if ($frame->opcode === 0x08) {
            //echo "Close frame received: Code {$frame->code} Reason {$frame->reason}\n";
        } else {
            try {
                $res = json_decode($frame->data, true, 512, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            } catch (JsonException $j) {
                if ($server->isEstablished($frame->fd)) {
                    $server->push($frame->fd, ws_response(422, '', '无法处理请求内容'));
                }
            }

            if (!isset($res['uri']) and empty($res['uri'])) {
                if ($server->isEstablished($frame->fd)) {
                    $server->push($frame->fd, ws_response(400, '', '非法访问'));
                }
                $server->close($frame->fd, true);
                return;
            }

            DI::set('fd_int' . Coroutine::getCid(), $frame->fd);
            DI::set('ws_data_arr' . Coroutine::getCid(), $res);

            try {
                $uri_arr = explode('/', $res['uri']);
                (new RouterInit())->routerStartup($uri_arr, 'Cli');
            } catch (ValidateException $e) { //参数验证手动触发的信息
                if ($server->isEstablished($frame->fd)) {
                    $server->push($frame->fd, ws_response($e->getCode(), '', $e->getMessage(), [], true));
                }
            } catch (ProgramException $e) { //程序手动抛出的异常
                if ($server->isEstablished($frame->fd)) {
                    $server->push($frame->fd, ws_response($e->getCode(), '', $e->getMessage()));
                }
            } catch (Throwable $e) {
                if ($server->isEstablished($frame->fd)) {
                    if (APP_DEBUG) {
                        $server->push($frame->fd, ws_response(500, '', $e->getMessage(), $e->getTrace()));
                    } else {
                        $server->push($frame->fd, ws_response(500, '', '服务异常'));
                    }
                }

                task_monolog(
                    $server,
                    ['message' => $e->getMessage(), 'trace' => $e->getTrace()],
                    'onRequest Throwable message:',
                    'websocket',
                    'error'
                );
            } finally {
                DI::del('fd_int' . Coroutine::getCid());
                DI::del('ws_data_arr' . Coroutine::getCid());
            }
        }
    }

    /**
     * http协议路由转接
     *
     * @param Request  $request
     * @param Response $response
     *
     * @throws JsonException
     */
    public function onRequest(Request $request, Response $response): void
    {
        $request_uri_str = $request->server['request_uri'] ?? '/';
        if (empty($request_uri_str)) {
            $response->status(404);
            $response->end();
            return;
        }
        //请求过滤,会请求2次
        if ('/favicon.ico' === $request->server['path_info'] or '/favicon.ico' === $request_uri_str) {
            $response->status(200);
            $response->end();
            return;
        }

        $response->header('Content-Type', 'application/json;charset=utf-8');

        $request_uri_arr = explode('/', $request_uri_str);
        if (isset($request_uri_arr[1]) and !empty($request_uri_arr[1])) {
            $response->header('Access-Control-Allow-Methods', 'POST,DELETE,PUT,GET,OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type,Authorization');
            $response->header('Access-Control-Expose-Headers', 'Timestamp,Sign,Language');
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Max-Age', '8388608');
            $response->header('Access-Control-Allow-Origin', DI::get('config_arr')['origin']['domain']);

            //过滤掉固定的几个模块不能在外部http直接访问，ws、task、tcp、close、finish模块
            if (in_array($request_uri_arr[1], DI::get('config_arr')['deny_http_module'], true)) {
                $response->status(404);
                $response->end();
                return;
            }

            //预检
            if ($request->server['request_method'] === 'OPTIONS') {
                $response->status(200);
                $response->end();
                return;
            }
        }

        //注册全局信息
        /*多个协程是并发执行的，因此不能使用类静态变量/全局变量保存协程上下文内容。
        使用局部变量是安全的，因为局部变量的值会自动保存在协程栈中，
        其他协程访问不到协程的局部变量。*/

        $cid = Coroutine::getCid();
        DI::set('request_obj' . $cid, $request);
        DI::set('response_obj' . $cid, $response);

        $response->status(200);

        try {
            $res = (new RouterInit())->routerStartup($request_uri_arr, $request->server['request_method']);
            $response->end($res);
            $this->routerLog($request, 'info', $res);
        } catch (ValidateException $e) { //参数验证手动触发的信息
            $res = http_response($e->getCode(), $e->getMessage(), vail: true);
            $response->end($res);
            $this->routerLog($request, 'notice', $res);
        } catch (ProgramException $e) { //程序手动抛出的异常
            $res = http_response($e->getCode(), $e->getMessage());
            $response->end($res);
            $this->routerLog($request, 'warning', $res);
        } catch (Throwable $e) {
            if (APP_DEBUG) {
                $response->end(http_response(500, $e->getMessage(), $e->getTrace()));
            } else {
                $response->end(http_response(500, '服务异常'));
            }

            $this->routerLog(
                $request,
                'error',
                json_encode(['message' => $e->getMessage(), 'trace' => $e->getTrace()], JSON_THROW_ON_ERROR | 320)
            );
        } finally {
            DI::del('request_obj' . $cid);
            DI::del('response_obj' . $cid);
        }

        error_clear_last();
        clearstatcache();
    }

    /**
     * tcp协议路由转接
     *
     * @param Server $server
     * @param int    $fd
     * @param int    $reactor_id
     * @param string $data
     *
     * @throws JsonException
     */
    //todo 暂未实现路由Tcp模块
    public function onReceive(Server $server, int $fd, int $reactor_id, string $data): void
    {
        $data = substr($data, 4);
        $res  = json_decode($data, true, 512, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        DI::set('fd_int' . Coroutine::getCid(), $fd);
        DI::set('receive_data_arr' . Coroutine::getCid(), $res);

        try {
            $uri_arr = explode('/', $res['uri']);
            (new RouterInit())->routerStartup($uri_arr, 'Cli');
        } catch (Throwable $e) {
            task_monolog(
                $server,
                ['message' => $e->getMessage(), 'trace' => $e->getTrace()],
                'onReceive Throwable message:',
                'receive',
                'error'
            );
        } finally {
            DI::del('fd_int' . Coroutine::getCid());
            DI::del('receive_data_arr' . Coroutine::getCid());
        }
    }

    /**
     * http协议中使用task方法,只限用于在worker操作方法中调用task时不依赖task方法返回的结果,如:redis,mysql等插入操作且不需返回插入后的状态.
     * websocket协议中用task方法,可直接在task方法中调用push方法返回数据给客户端,这样swoole服务模式就变为worker中方法是异步
     * 到task方法中同步+协程执行模式,worker中可更多地处理请求以提高websocket服务器性能.
     * task路由转接
     *
     * @param Server $server
     * @param Task   $task
     *
     * @throws Exception
     */
    public function onTask(Server $server, Task $task): void
    {
        $res = unserialize((string)$task->data);
        DI::set('task_data_arr' . Coroutine::getCid(), $res);
        ob_start();
        try {
            $uri_arr = explode('/', $res['uri']);
            (new RouterInit())->routerStartup($uri_arr, 'Cli');
        } catch (Throwable $e) {
            $fp = fopen(ROOT_PATH . '/log/task.log', 'ab+');
            fwrite($fp, json_encode(['message' => $e->getMessage(), 'trace' => $e->getTrace()], JSON_THROW_ON_ERROR | 320));
            fclose($fp);
        } finally {
            $result = ob_get_contents();
            DI::del('task_data_arr' . Coroutine::getCid());
        }
        ob_end_clean();

        //为了让在task进程业务代码中可以打印变量
        if (!empty($result)) {
            var_dump($result);
            $result = '';
        }

        $task->finish($result);
    }

    /**
     * task任务完成返回数据到worker时路由转接
     *
     * @param Server $server
     * @param int    $task_id
     * @param string $data
     */
    public function onFinish(Server $server, int $task_id, string $data): void
    {
        /*if (!empty($data)) {
            $res        = unserialize($data);
            DI::set('finish_data_arr' . Coroutine::getCid(), $res);
            try {
                $uri_arr = explode('/', '/finish/flog/index');
                (new RouterInit())->routerStartup($uri_arr, 'Cli');
            } catch (Throwable $e) {
                if (APP_DEBUG) {
                    $fp = fopen(ROOT_PATH . '/log/finish.log', 'ab+');
                    fwrite($fp, json_encode(['message' => $e->getMessage(), 'trace' => $e->getTrace()], JSON_THROW_ON_ERROR | 320));
                    fclose($fp);
                }
            } finally {
                DI::del('finish_data_arr' . Coroutine::getCid());
            }

        }*/
    }

    /**
     * 连接关闭路由转接
     *
     * @param Server $server
     * @param int    $fd
     * @param int    $reactorId
     */
    public function onClose(Server $server, int $fd, int $reactorId): void
    {
        //echo "client-{$fd} is closed" . PHP_EOL;
        //echo '==============='. date("Y-m-d H:i:s", time()). '欢送' . $fd . '离开==============' . PHP_EOL;
        /* DI::set('fd_int' . Coroutine::getCid(), $fd);
         try {
             $uri_arr = explode('/', '/close/index/index');
             (new RouterInit())->routerStartup($uri_arr, 'Cli');
         } catch (Throwable $e) {
             if (APP_DEBUG) {
                $fp = fopen(ROOT_PATH . '/log/close.log', 'ab+');
                fwrite($fp, json_encode(['message' => $e->getMessage(), 'trace' => $e->getTrace()], JSON_THROW_ON_ERROR | 320));
                fclose($fp);
             }
         } finally {
             DI::del('fd_int' . Coroutine::getCid());
         }*/
    }

    /**
     * 此事件在worker进程终止时发生。在此函数中可以回收worker进程申请的各类资源
     *
     * @param Server $server
     * @param int    $worker_id
     */
    public function onWorkerStop(Server $server, int $worker_id): void
    {
        //请勿开启opcache，如开启了需要在这里使用opcache_reset();
    }

    /**
     * 在onWorkerExit中尽可能地移除/关闭异步的Socket连接，最终底层检测到Reactor中事件监听的句柄数量为0时退出进程。
     *
     * @param Server $server
     * @param int    $worker_id
     */
    public function onWorkerExit(Server $server, int $worker_id): void
    {

    }

    /**
     * 此函数主要用于报警和监控，一旦发现Worker进程异常退出，那么很有可能是遇到了致命错误或者进程CoreDump。
     * 通过记录日志或者发送报警的信息来提示开发者进行相应的处理
     *
     * @param Server $server
     * @param int    $worker_id
     * @param int    $worker_pid
     * @param int    $exit_code
     * @param int    $signal
     */
    public function onWorkerError(Server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal): void
    {
        $fp = fopen(ROOT_PATH . '/log/workerError.log', 'ab+');
        fwrite($fp, "onWorkerError: pid:{$worker_pid},code:{$exit_code},signal:{$signal}");
        fclose($fp);
    }

    /**
     * 以mysql记录请求日志
     *
     * User: hanhyu
     * Date: 2021/2/6
     * Time: 下午5:33
     *
     * @param Request $request
     * @param string  $level
     * @param string  $resp_data
     */
    private function routerLog(Request $request, string $level = info, string $resp_data): void
    {
        $cid = Coroutine::getCid();
        if (DI::get('log_flag' . $cid)) {
            $request_data['req_method'] = $request->server['request_method'];
            $request_data['req_uri']    = $request->server['request_uri'];
            $request_data['req_data']   = DI::get('req_data' . $cid);
            $request_data['req_ip']     = get_ip($request->server);
            $request_data['resp_data']  = $resp_data;
            $request_data['level']      = $level;
            $request_data['uri']        = '/task/log/routerLog';
            $this->server->task(serialize($request_data));
        }
        DI::del('req_data' . $cid);
        DI::del('log_flag' . $cid);
    }

}
