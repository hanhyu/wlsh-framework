<?php declare(strict_types=1);


namespace App\Library;

use longlang\phpkafka\Consumer\Consumer;
use longlang\phpkafka\Consumer\ConsumerConfig;
use longlang\phpkafka\Producer\Producer;
use longlang\phpkafka\Producer\ProducerConfig;
use Swoole\Coroutine;

abstract class AbstractKafka
{
    /**
     * 此处使用静态延迟绑定，实现选择不同的消费主题
     * @var int
     */
    protected static string $topic = 'test';
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

    public static function getProducer(string $db_config = 'kafka'): Producer
    {
        $kafka_arr = DI::get('config_arr')[$db_config];

        $config = new ProducerConfig();
        $config->setBootstrapServer($kafka_arr['host']);
        $config->setUpdateBrokers($kafka_arr['update_brokers']);
        $config->setAcks(-1);
        return new Producer($config);
    }

    public static function getCustomer(string $db_config = 'kafka'): Consumer
    {
        $kafka_arr = DI::get('config_arr')[$db_config];

        $config = new ConsumerConfig();
        $config->setBroker($kafka_arr['host']);
        $config->setTopic(static::$topic);
        $config->setGroupId($kafka_arr['group_id']);
        $config->setClientId($kafka_arr['client_id']); // 客户端ID，不同的消费者进程请使用不同的设置
        $config->setGroupInstanceId($kafka_arr['group_instance_id']); // 分组实例ID，不同的消费者进程请使用不同的设置
        return new Consumer($config);
    }

}
