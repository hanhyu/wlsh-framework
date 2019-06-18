<?php

class TestQps
{
    protected $concurrency; //并发量
    protected $request;     //请求量
    protected $requested = 0;
    protected $start_time;

    function __construct()
    {
        $this->concurrency = 100;
        $this->request     = 10000;
    }

    public function run()
    {
        $this->start_time = microtime(true);
        $this->webSocket();
    }

    protected function webSocket()
    {
        go(function () {
            for ($c = 1; $c <= $this->concurrency; $c++) {
                $cli = new \Swoole\Coroutine\Http\Client('127.0.0.1', 9501);
                $cli->set(['websocket_mask' => false]);
                $ret = $cli->upgrade('/');
                if ($ret) {
                    $i = $this->request / $this->concurrency;
                    while ($i >= 1) {
                        $this->push($cli);
                        $cli->recv();
                        $i--;
                    }
                }
            }
            $this->finish();
        });
    }

    protected function push($cli)
    {
        $ret = $cli->push('Hello World');
        if ($ret === true) {
            $this->requested++;
        }
    }

    protected function finish()
    {
        $cost_time = round(microtime(true) - $this->start_time, 4);
        echo "Concurrency:" . $this->concurrency . PHP_EOL;
        echo "Request num:" . $this->request . PHP_EOL;
        echo "Success num:" . $this->requested . PHP_EOL;
        echo "Total time:" . $cost_time . PHP_EOL;
        echo "Request per second:" . intval($this->request / $cost_time) . PHP_EOL;
    }
}

$test = new TestQps();
$test->run();