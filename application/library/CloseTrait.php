<?php
declare(strict_types=1);


namespace App\Library;


use Swoole\Coroutine;
use Swoole\WebSocket\Server;

trait CloseTrait
{
    protected Server $server;
    protected int $fd;
    protected int $cid;

    public function beforeInit(): void
    {
        $this->cid    = Coroutine::getCid();
        $this->fd     = DI::get('fd_int' . $this->cid);
        $this->server = DI::get('server_obj');
    }

}
