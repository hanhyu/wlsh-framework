<?php
/**
 * 注意此类中的每一行代码请勿随意上下移动
 *
 * User: hanhyu
 * Date: 16-7-25
 * Time: 上午10:19
 */

require 'AutoReload.php';

use Yaf\{
    Registry,
    Loader,
    Application,
    Exception
};

class Server
{
    /**
     * @var \Swoole\WebSocket\Server
     */
    private $server;
    private $table;
    private $atomic;
    private $config_file;
    /**
     * @var Yaf\Application
     */
    private $yaf_obj;
    protected static $instance = null;

    public static function getInstance()
    {
        if (empty(self::$instance) || !(self::$instance instanceof Server)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public function setConfigIni($config_ini): void
    {
        /*if (!is_file($config_ini)) {
            trigger_error('Server Config File Not Exist!', E_USER_ERROR);
        }*/
        $this->config_file = $config_ini;
    }

    public function start(): void
    {
        $this->server = new Swoole\WebSocket\Server(
            "0.0.0.0",
            9770,
            SWOOLE_PROCESS,
            SWOOLE_SOCK_TCP | SWOOLE_SSL
        );

        //todo 这里的所有配置参数，可以使用外部配置文件引入。
        $this->server->set([
            //'reactor_num' => 16,
            'worker_num'                 => 8,
            'task_worker_num'            => 8,
            'task_enable_coroutine'      => true,
            'daemonize'                  => SWOOLE_DAEMONIZE,
            'max_request'                => 300000,
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
            'ssl_cert_file'              => ROOT_PATH . '/tests/opensslRsa/cert.crt',
            'ssl_key_file'               => ROOT_PATH . '/tests/opensslRsa/rsa_private.key',
            //'open_http2_protocol'      => true,
            //'open_mqtt_protocol' => true,
            'open_websocket_close_frame' => true,
            'send_yield'                 => true,
        ]);

        $this->table = new Swoole\Table(1024);
        $this->table->column('key', Swoole\Table::TYPE_STRING, 20);
        $this->table->column('value', Swoole\Table::TYPE_INT, 128);
        $this->table->create();

        $this->atomic = new Swoole\Atomic();

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
     * @param Swoole\WebSocket\Server $server
     */
    public function onStart(Swoole\WebSocket\Server $server): void
    {
        echo "Swoole tcp server is started at tcp://127.0.0.1:9771" . PHP_EOL;
        echo "Swoole http|ws server is started at http://127.0.0.1:9770" . PHP_EOL;
    }

    public function onManagerStart(Swoole\WebSocket\Server $server): void
    {

    }

    /**
     * @param Swoole\WebSocket\Server $server
     * @param int                     $worker_id
     *
     * @throws NotFound
     */
    public function onWorkerStart(Swoole\WebSocket\Server $server, int $worker_id): void
    {
        /* array(3) {
                 [0]=>
           string(26) "/home/baseFrame/swoole.php"
                 [1]=>
           string(46) "/home/baseFrame/application/library/Server.php"
                 [2]=>
           string(50) "/home/baseFrame/application/library/AutoReload.php"
         }

         var_dump(get_included_files());*/

        //用inotify监听mvc目录,一有变动就自动重启脚本
        if (0 == $worker_id) {
            $kit = new AutoReload($server->master_pid);
            $kit->watch(CONF_PATH);
            $kit->watch(APPLICATION_PATH);
            $kit->addFileType('.php');
            $kit->run();
        }

        //重命名进程名字
        if ($server->taskworker) {
            swoole_set_process_name('swooleTaskProcess');
        } else {
            swoole_set_process_name('swooleWorkerProcess');
        }

        Registry::set('server', $server);
        Registry::set('table', $this->table);
        Registry::set('atomic', $this->atomic);

        Loader::import(ROOT_PATH . '/vendor/autoload.php');
        Loader::import(LIBRARY_PATH . '/common/functions.php');

        //实例化yaf
        try {
            //$this->yaf_obj = new Yaf\Application($this->config_file, ini_get('yaf.environ'));
            $this->yaf_obj = new Application($this->config_file);
            $this->yaf_obj->bootstrap()->run();
        } catch (Exception $e) {
            var_dump($e->getMessage());
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
        if ($worker_id == 0) {
            $server->tick(30000, function () use ($server) {
                foreach ($server->connections as $fd) {
                    if ($this->server->isEstablished($fd))
                        $this->server->push($fd, true, 9);
                }
            });
        }

    }

    /**
     *
     * @param Swoole\Http\Request  $request
     * @param Swoole\Http\Response $response
     *
     * @return bool
     */
    public function onHandShake(Swoole\Http\Request $request, Swoole\Http\Response $response): bool
    {
        //以get参数传递token，如： new WebSocket(`wss://127.0.0.1:9770?token=${token}`)
        $token = $request->get['token'] ?? '0';
        $res   = validate_token($token);
        if (!empty($res)) {
            $response->status(400);
            $response->end($res);
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
     * @param Swoole\WebSocket\Server $server
     * @param Swoole\Http\Request     $request
     */
    public function onOpen(Swoole\WebSocket\Server $server, Swoole\Http\Request $request): void
    {
        /*
        Yaf\Registry::get('table')->set($request->fd, ['uid' => intval($request->get['uid'])]);
         if ($server->isEstablished($request->fd))
             $server->push($request->fd, ws_response(200, "wsConnect", '连接成功'));
        */
        echo '===============' . date("Y-m-d H:i:s", time()) . '欢迎' . $request->fd . '进入==============' . PHP_EOL;
    }

    /**
     * websocket协议路由转接
     *
     * @param Swoole\WebSocket\Server $server
     * @param Swoole\WebSocket\Frame  $frame
     */
    public function onMessage(Swoole\WebSocket\Server $server, Swoole\WebSocket\Frame $frame): void
    {
        if ($frame->opcode == 0x08) {
            //echo "Close frame received: Code {$frame->code} Reason {$frame->reason}\n";
        } else {
            $res = json_decode($frame->data, true);
            if (!isset($res['uri']) and empty($res['uri'])) {
                if ($server->isEstablished($frame->fd))
                    $server->push($frame->fd, ws_response(400, null, '非法访问'));
                $server->close($frame->fd, true);
                return;
            }

            $req_obj = new Yaf\Request\Http($res['uri'], '/');
            $req_obj->setParam((array)$frame);


            try {
                $this->yaf_obj->getDispatcher()->dispatch($req_obj);
            } catch (ValidateException $e) { //参数验证手动触发的信息
                if ($server->isEstablished($frame->fd))
                    $server->push($frame->fd, ws_response($e->getCode(), '', $e->getMessage(), [], true));
            } catch (ProgramException $e) { //程序手动抛出的异常
                if ($server->isEstablished($frame->fd))
                    $server->push($frame->fd, ws_response($e->getCode(), '', $e->getMessage()));
            } catch (Throwable $e) {
                $msg = APP_DEBUG ? $e->getMessage() : '服务异常';
                if ($server->isEstablished($frame->fd))
                    $server->push($frame->fd, ws_response(500, '', $msg));

                co_log(
                    ['message' => $e->getMessage(), 'trace' => $e->getTrace()],
                    "onRequest Throwable message:",
                    'websocket'
                );
            }
        }
    }

    /**
     * http协议路由转接
     *
     * @param Swoole\Http\Request  $request
     * @param Swoole\Http\Response $response
     *
     */
    public function onRequest(Swoole\Http\Request $request, Swoole\Http\Response $response): void
    {
        //TODO 绑定固定域名才能访问
        //请求过滤,会请求2次
        if (in_array('/favicon.ico', [$request->server['request_uri']])) {
            $response->end();
            return;
        }

        $response->header('Content-Type', 'application/json;charset=utf-8');

        $request_uri = explode('/', $request->server['request_uri']);
        $yaf_config  = Yaf\Registry::get('config');
        if (isset($request_uri[1]) AND !empty($request_uri[1])) {
            $response->header('Access-Control-Allow-Methods', 'POST,DELETE,PUT,GET,OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type,Authorization');
            $response->header('Access-Control-Expose-Headers', 'Timestamp,Sign,Language');
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Max-Age', '8388608');
            $response->header('Access-Control-Allow-Origin', $yaf_config['origin']['domain']);

            //过滤掉固定的几个模块不能在外部http直接访问，ws、task、tcp、close、finish模块
            $router = explode(',', $yaf_config['router']['notHttp']);
            if (in_array($request_uri[1], $router)) {
                $response->status(404);
                $response->end();
                return;
            }

            //预检
            if ($request->server['request_method'] == "OPTIONS") {
                $response->end();
                return;
            }
        }

        $req_obj         = new Yaf\Request\Http($request->server['request_uri'], '/');
        $req_obj->method = $request->server['request_method'];

        //注册全局信息
        /*多个协程是并发执行的，因此不能使用类静态变量/全局变量保存协程上下文内容。
        使用局部变量是安全的，因为局部变量的值会自动保存在协程栈中，
        其他协程访问不到协程的局部变量。*/
        Yaf\Registry::set('request', $request);
        Yaf\Registry::set('response', $response);

        try {
            $this->yaf_obj->getDispatcher()->dispatch($req_obj);
        } catch (ValidateException $e) { //参数验证手动触发的信息
            $response->end(http_response($e->getCode(), $e->getMessage(), [], true));
        } catch (ProgramException $e) { //程序手动抛出的异常
            $response->end(http_response($e->getCode(), $e->getMessage()));
        } catch (Throwable $e) {
            $msg = APP_DEBUG ? $e->getMessage() : '服务异常';
            $response->end(http_response(500, $msg));

            co_log(
                ['message' => $e->getMessage(), 'trace' => $e->getTrace()],
                "onRequest Throwable message:",
                'http'
            );
        }
    }

    /**
     * tcp协议路由转接
     *
     * @param Swoole\WebSocket\Server  $server
     * @param int                      $fd
     * @param int                      $reactor_id
     * @param                          $data
     */
    //todo 暂未实现路由Tcp模块
    public function onReceive(Swoole\WebSocket\Server $server, int $fd, int $reactor_id, $data): void
    {
        $data      = substr($data, 4);
        $res       = json_decode($data, true);
        $res['fd'] = $fd;
        $req_obj   = new Yaf\Request\Http($res['uri'], '/');
        $req_obj->setParam($res);

        try {
            $this->yaf_obj->getDispatcher()->dispatch($req_obj);
        } catch (Throwable $e) {
            co_log(
                ['message' => $e->getMessage(), 'trace' => $e->getTrace()],
                "onReceive Throwable message:",
                'receive'
            );
        }
    }

    /**
     * http协议中使用task方法,只限用于在worker操作方法中调用task时不依赖task方法返回的结果,如:redis,mysql等插入操作且不需返回插入后的状态.
     * websocket协议中用task方法,可直接在task方法中调用push方法返回数据给客户端,这样swoole服务模式就变为worker中方法是异步
     * 到task方法中同步+协程执行模式,worker中可更多地处理请求以提高websocket服务器性能.
     * task路由转接
     *
     * @param Swoole\WebSocket\Server $server
     * @param Swoole\Server\Task      $task
     *
     */
    public function onTask(Swoole\WebSocket\Server $server, Swoole\Server\Task $task): void
    {
        $res     = unserialize($task->data);
        $req_obj = new Yaf\Request\Http($res['uri'], '/');
        unset($res['uri']);
        $req_obj->setParam($res);

        ob_start();
        try {
            $this->yaf_obj->getDispatcher()->dispatch($req_obj);
        } catch (Throwable $e) {
            co_log(
                ['message' => $e->getMessage(), 'trace' => $e->getTrace()],
                "onTask Throwable message:",
                'task'
            );
        } finally {
            $result = ob_get_contents();
        }
        ob_end_clean();

        $task->finish($result);
    }

    /**
     * task任务完成返回数据到worker时路由转接
     *
     * @param Swoole\WebSocket\Server $server
     * @param int                     $task_id
     * @param string                  $data
     */
    public function onFinish(Swoole\WebSocket\Server $server, int $task_id, string $data): void
    {
        if (!empty($data)) {
            $res = unserialize($data);
            if (isset($res['uri'])) {
                $req_obj = new Yaf\Request\Http($res['uri'], '/');
                unset($res['uri']);
                $req_obj->setParam((array)$res);

                try {
                    $this->yaf_obj->getDispatcher()->dispatch($req_obj);
                } catch (Throwable $e) {
                    if (APP_DEBUG) {
                        co_log(
                            ['message' => $e->getMessage(), 'trace' => $e->getTrace()],
                            "onFinish Throwable message:",
                            'finish'
                        );
                    }
                }
            }
        }
    }

    /**
     * 连接关闭路由转接
     *
     * @param Swoole\WebSocket\Server $server
     * @param int                     $fd
     * @param int                     $reactorId
     */
    public function onClose(Swoole\WebSocket\Server $server, int $fd, int $reactorId): void
    {
        //echo "client-{$fd} is closed" . PHP_EOL;
        //echo '==============='. date("Y-m-d H:i:s", time()). '欢送' . $fd . '离开==============' . PHP_EOL;
        $res['uri'] = '/close/index/index';
        $res['fd']  = $fd;

        $req_obj = new Yaf\Request\Http($res['uri'], '/');
        $req_obj->setParam((array)$res);

        try {
            $this->yaf_obj->getDispatcher()->dispatch($req_obj);
        } catch (Throwable $e) {
            if (APP_DEBUG) {
                co_log(
                    ['message' => $e->getMessage(), 'trace' => $e->getTrace()],
                    "onClose Throwable message:",
                    'close'
                );
            }
        }
    }

    /**
     * 此事件在worker进程终止时发生。在此函数中可以回收worker进程申请的各类资源
     *
     * @param Swoole\WebSocket\Server $server
     * @param int                     $worker_id
     */
    public function onWorkerStop(Swoole\WebSocket\Server $server, int $worker_id): void
    {
        //请勿开启opcache，如开启了需要在这里使用opcache_reset();
    }

    /**
     * 在onWorkerExit中尽可能地移除/关闭异步的Socket连接，最终底层检测到Reactor中事件监听的句柄数量为0时退出进程。
     *
     * @param Swoole\WebSocket\Server $server
     * @param int                     $worker_id
     */
    public function onWorkerExit(Swoole\WebSocket\Server $server, int $worker_id): void
    {

    }

    /**
     * 此函数主要用于报警和监控，一旦发现Worker进程异常退出，那么很有可能是遇到了致命错误或者进程CoreDump。
     * 通过记录日志或者发送报警的信息来提示开发者进行相应的处理
     *
     * @param Swoole\WebSocket\Server $server
     * @param int                     $worker_id
     * @param int                     $worker_pid
     * @param int                     $exit_code
     * @param int                     $signal
     */
    public function onWorkerError(Swoole\WebSocket\Server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal): void
    {
        $content = "onWorkerError: pid:{$worker_pid},code:{$exit_code},signal:{$signal}";
        $fp      = fopen(ROOT_PATH . '/log/swoole.log', "a+");
        fwrite($fp, $content);
        fclose($fp);
    }

}