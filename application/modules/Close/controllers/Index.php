<?php
declare(strict_types=1);

use Yaf\Registry;
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-10-22
 * Time: 下午2:00
 */
class IndexController extends Yaf\Controller_Abstract
{
    /**
     * 这里可以做一些连接关闭或客户端退出的后续操作记录日志
     *
     * @param int $fd
     */
    public function IndexAction(int $fd): void
    {
        $server = Registry::get('server');
        //判断该fd是websocket客户端
        if ($server->getClientInfo($fd)['websocket_status'] == 3) {
            //在ws协议下非正常关闭操作后业务需要退出的逻辑
        };
        co_log($fd, "onClose is fd:");
    }

}