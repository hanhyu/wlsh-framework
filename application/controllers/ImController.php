<?php
declare(strict_types=1);


namespace App\Controllers;


use App\Library\Router;
use App\Library\WebsocketTrait;

class ImController
{
    use WebsocketTrait;

    public function __construct()
    {
        $this->beforeInit();
    }

    #[Router(method: 'CLI', auth: false, rate_limit: 60000)]
    public function getInfoAction(): string
    {
        return ws_response();
    }

}
