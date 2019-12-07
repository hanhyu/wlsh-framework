<?php
declare(strict_types=1);

namespace App\Modules\System\Controllers;

use App\Library\ControllersTrait;
use App\Library\DI;
use App\Models\MysqlFactory;
use Swoole\Coroutine;

/**
 * 查看服务器各种状态信息
 * UserDomain: hanhyu
 * Date: 18-12-10
 * Time: 上午9:44
 */
class ServerStatus
{
    use ControllersTrait;

    public function __construct()
    {
        $this->beforeInit();
    }

    /**
     * 查看当前server的活动tcp连接信息
     * {"start_time":1544406749,"connection_num":1,"accept_count":2,"close_count":1,
     * "tasking_num":0,"request_count":34,"worker_request_count":3,"coroutine_num":1}
     */
    public function getStatusAction(): void
    {
        $swoole                = $this->server->stats();
        $content['version']    = DI::get('config_arr')['version'];
        $content['before_url'] = DI::get('config_arr')['before_url'];
        $content['uname']      = php_uname();
        $content['swoole_v']   = SWOOLE_VERSION;
        //exec('nginx -v', $content['nginxV']);
        //$content['nginxV'] = fgets(STDIN);
        $content['php_v'] = PHP_VERSION;
        //$content['mysql_v']   = Coroutine::exec('mysql -V')['output'];
        $content['mysql_v']   = MysqlFactory::systemMenu()->getVersion();
        $content['filesize']  = ini_get('upload_max_filesize');
        $content['exec_time'] = ini_get('max_execution_time');
        $content['memory']    = round(memory_get_usage() / 1024 / 1024, 2);
        $content['atomic']    = $this->atomic->get();

        $data['swoole']  = $swoole;
        $data['content'] = $content;
        $this->response->end(http_response(200, '', $data));
        //echo http_response(200, $data);
    }

    //todo 在运维平台中增加监听指定端口，可以手动发送指令启动服务，停止服务功能

}