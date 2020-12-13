<?php declare(strict_types=1);

use longlang\phpkafka\Consumer\Consumer;
use longlang\phpkafka\Consumer\ConsumerConfig;

require dirname(__DIR__) . '/../vendor/autoload.php';

$config = new ConsumerConfig();
$config->setBroker('172.17.0.1:9092');
$config->setTopic('test');
$consumer = new Consumer($config);
while (true) {
    $message = $consumer->consume();
    if ($message) {
        var_dump($message->getKey() . ':' . $message->getValue());
        $consumer->ack($message->getPartition()); // ack
    }
    sleep(1);
}

return;
