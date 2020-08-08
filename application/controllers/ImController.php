<?php
declare(strict_types=1);


namespace App\Controllers;


use App\Library\WebsocketTrait;

class ImController
{
    use WebsocketTrait;

    public function __construct()
    {
        $this->beforeInit();
    }

    /**
     * @router auth=false&method=cli
     */
    public function getInfoAction(): void
    {
        $this->server->push($this->fd, ws_response(200, 'im/get_info', 'success'));
    }

}
