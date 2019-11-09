<?php
declare(strict_types=1);

use MongoDB\Client;
use Yaf\Registry;

class MongoPool
{
    /**
     * @var Client
     */
    protected $ch;

    public function __construct()
    {
        $this->ch = new Client(
            Registry::get('config')->log->mongo,
            [
                'username'   => Registry::get('config')->log->username,
                'password'   => Registry::get('config')->log->pwd,
                'authSource' => Registry::get('config')->log->database,
            ]);
        $this->ch->listDatabases();
        unset($this->ch);
    }

}
