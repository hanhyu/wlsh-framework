<?php
declare(strict_types=1);

namespace App\Library;

use MongoDB\Client;

class MongoPool
{
    /**
     * @var Client
     */
    protected $ch;

    public function __construct()
    {
        $this->ch = new Client(
            DI::get('config_arr')['log']['mongo'],
            [
                'username'   => DI::get('config_arr')['log']['username'],
                'password'   => DI::get('config_arr')['log']['pwd'],
                'authSource' => DI::get('config_arr')['log']['database'],
            ]);
        $this->ch->listDatabases();
        unset($this->ch);
    }

}
