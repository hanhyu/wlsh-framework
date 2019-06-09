<?php
declare(strict_types=1);

namespace App\Modules\System\Controllers;

use App\Models\Forms\SystemLogForms;
use Exception;

/**
 * 获取日志类
 * User: hanhyu
 * Date: 18-9-29
 * Time: 下午3:02
 */
class LogSwoole extends \Yaf\Controller_Abstract
{
    use \ControllersTrait;

    public function init()
    {
        $this->beforeInit();
    }

    /**
     * 查看swoole日志信息
     * @throws Exception
     */
    public function getInfoAction(): void
    {
        $data    = $this->validator(SystemLogForms::$info);
        $fp      = fopen(ROOT_PATH . '/log/' . $data['name'], "r");
        $content = \Swoole\Coroutine::fread($fp);
        fclose($fp);
        $this->response->end(http_response(200, '', ['content' => $content]));
    }

    /**
     * 清空swoole日志
     * @throws Exception
     */
    public function cleanLogAction(): void
    {
        $data = $this->validator(SystemLogForms::$info);
        if ($data['name'] == 'swoole.log' || $data['name'] == 'swoolePid.log') {
            $fp = fopen(ROOT_PATH . '/log/' . $data['name'], "w+");
        } else { //monolog日志
            $fp = fopen(ROOT_PATH . '/log/monolog/' . $data['name'], "w+");
        }
        $content = \Swoole\Coroutine::fwrite($fp, '日志已清空。。。');
        fclose($fp);
        $this->response->end(http_response(200, '', ['content' => $content]));
    }

    /**
     * 查询monolog日志
     * @throws Exception
     */
    public function getMonologAction(): void
    {
        $data = $this->validator(SystemLogForms::$info);
        $file = ROOT_PATH . '/log/monolog/' . $data['name'];
        if (is_file($file)) {
            $fp      = fopen($file, "r");
            $content = \Swoole\Coroutine::fread($fp);
            fclose($fp);
            $this->response->end(http_response(200, '', ['content' => $content]));
        } else {
            $this->response->end(http_response(200, '', ['content' => '查询目录不存在']));
        }
    }

}