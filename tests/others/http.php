<?php declare(strict_types=1);
//高性能HTTP服务器
$http = new Swoole\Http\Server('127.0.0.1', 9501, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

$http->set([
    'worker_num'               => 4,
    'dispatch_mode'            => 2,
    'enable_reuse_port'        => false,
    'package_max_length'       => 200000,
    'reload_async'             => true,
    'max_wait_time'            => 7,
    'heartbeat_idle_time'      => 600,
    'heartbeat_check_interval' => 60,
    'buffer_output_size'       => 8 * 1024 * 1024,
    'send_yield'               => true,
    'hook_flags'               => SWOOLE_HOOK_ALL | SWOOLE_HOOK_CURL,
]);

$http->on('start', static function () {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

$http->on('request', function ($request, $response) {
    $response->header('Content-Type', 'text/plain');
    $response->end("Hello World\n");
});

$http->start();
