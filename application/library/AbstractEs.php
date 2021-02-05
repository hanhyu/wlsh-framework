<?php declare(strict_types=1);


namespace App\Library;


use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Swoole\Coroutine;

abstract class AbstractEs
{
    private static array $instance = [];

    private function __construct()
    {
    }

    public static function getInstance(): static
    {
        $class_name = static::class;
        $cid        = Coroutine::getCid();
        if (!isset(static::$instance[$class_name][$cid])) {
            //new static()与new static::class一样，但为了IDE友好提示类中的方法，需要用new static()
            $_instance = static::$instance[$class_name][$cid] = new static();
        } else {
            $_instance = static::$instance[$class_name][$cid];
        }

        defer(static function () use ($class_name, $cid) {
            unset(static::$instance[$class_name][$cid]);
        });

        //为了IDE代码提示功能
        return $_instance;
    }

    public static function getDb(string $db_schema = 'es'): Client
    {
        $es_arr = DI::get('config_arr')[$db_schema];
        try {
            return ClientBuilder::create()
                ->setHosts($es_arr['host'])
                ->setBasicAuthentication($es_arr['username'], $es_arr['password'])
                ->build();
        } catch (Exception $e) {
            task_monolog(DI::get('server_obj'), $e->getMessage(), '连接es服务端失败。', level: 'alert');
            throw new RuntimeException('es连接失败', 500);
        }
    }

}
