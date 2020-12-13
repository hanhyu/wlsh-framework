<?php
declare(strict_types=1);

namespace App\Modules\Close\Controllers;

use App\Library\CloseTrait;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-10-22
 * Time: 下午2:00
 */
class IndexController
{
    use CloseTrait;

    public function __construct()
    {
        $this->beforeInit();
    }

    /**
     * 这里可以做一些连接关闭或客户端退出的后续操作记录日志
     */
    #[Router(method: 'Cli', auth: false)]
    public function IndexAction(): void
    {
        //判断该fd是websocket客户端
        $client_info = $this->server->getClientInfo($this->fd);
        if ($client_info and is_array($client_info) and $client_info['websocket_status'] === 3) {
            //在ws协议下非正常关闭操作后业务需要退出的逻辑
        } else if (APP_DEBUG) {
            //monolog_by_file($this->fd, 'onClose is fd:');
        }
    }

}
