<?php
declare(strict_types=1);


class Server12
{
    private $http;

    public function __construct()
    {
        $this->http = new Swoole\Http\Server('0.0.0.0', 9770);
        $this->http->set([
            //'worker_num' => 16,
            'daemonize'                => false,
            'max_request'              => 100000,
            'dispatch_mode'            => 2,
            //'task_worker_num' => 16,
            'heartbeat_check_interval' => 660,
            'heartbeat_idle_time'      => 1200,
        ]);
        $this->http->on('start', [$this, 'onStart']);
        $this->http->on('managerStart', [$this, 'onManagerStart']);
        $this->http->on('workerStart', [$this, 'onWorkerStart']);
        $this->http->on('request', [$this, 'onRequest']);
        $this->http->on('close', [$this, 'onClose']);
        $this->http->on('finish', [$this, 'onFinish']);
        $this->http->start();
    }

    public function onStart($http)
    {
        echo "Swoole http server is started at http://127.0.0.1:9501\n";
    }

    public function onManagerStart($http)
    {
    }

    public function onWorkerStart($http, $worker_id)
    {
    }

    public function onRequest($request, $response)
    {
        var_dump($request->server['request_uri']);
    }

    public function onClose($http, $fd)
    {
        //echo test($this->test);
    }

    public function onFinish($http, $task_id, $data)
    {
    }


}


new Server12();
