<?php

namespace Swoole\WebSocket;

class Server extends \Swoole\Http\Server
{

    private $onHandshake = null;

    /**
     * @return mixed
     */
    public function push($fd, $data, $opcode = null, $finish = null)
    {
    }

    /**
     * @return mixed
     */
    public function disconnect($fd, $code = null, $reason = null)
    {
    }

    /**
     * @return mixed
     */
    public function isEstablished($fd)
    {
    }

    /**
     * @return mixed
     */
    public static function pack($data, $opcode = null, $finish = null, $mask = null)
    {
    }

    /**
     * @return mixed
     */
    public static function unpack($data)
    {
    }


}
