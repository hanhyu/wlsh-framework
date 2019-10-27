<?php
declare(strict_types=1);

use Yaf\Registry;

class MongoPool
{
    /**
     * @var \MongoDB\Client
     */
    protected $ch;

    public function __construct()
    {
        $this->ch = new \MongoDB\Client(
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
