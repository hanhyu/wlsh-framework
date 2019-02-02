<?php
declare(strict_types=1);

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
        co_log($fd, "onClose is fd:");
    }

}