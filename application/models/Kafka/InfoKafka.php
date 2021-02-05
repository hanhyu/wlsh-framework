<?php declare(strict_types=1);


namespace App\Models\Kafka;


use App\Library\AbstractKafka;

class InfoKafka extends AbstractKafka
{
    protected static string $topic = 'info';

    public function addInfo($data): void
    {
        self::getProducer()->send(self::$topic, $data);
    }

    public function getInfo(): ?string
    {
        $msg = self::getCustomer()->consume();
        if ($msg) {
            self::getCustomer()->ack($msg);
            return $msg->getValue();
        }
        return null;
    }

}
