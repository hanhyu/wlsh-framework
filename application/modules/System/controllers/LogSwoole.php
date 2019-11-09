<?php
declare(strict_types=1);

namespace App\Modules\System\controllers;

use App\Models\Forms\SystemLogForms;
use Exception;
use Swoole\Coroutine;
use Yaf\Controller_Abstract;

/**
 * 获取日志类
 * User: hanhyu
 * Date: 18-9-29
 * Time: 下午3:02
 */
class LogSwoole extends Controller_Abstract
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
        $fp      = fopen(ROOT_PATH . '/log/' . $data['name'], 'rb');
        $content = Coroutine::fread($fp);
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
        if ($data['name'] === 'swoole.log' || $data['name'] === 'swoolePid.log') {
            $fp = fopen(ROOT_PATH . '/log/' . $data['name'], 'wb+');
        } else { //monolog日志
            $fp = fopen(ROOT_PATH . '/log/monolog/' . $data['name'], 'wb+');
        }
        $content = Coroutine::fwrite($fp, '日志已清空。。。');
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
            $fp      = fopen($file, 'rb');
            $content = Coroutine::fread($fp);
            fclose($fp);
            $this->response->end(http_response(200, '', ['content' => $content]));
        } else {
            $this->response->end(http_response(200, '', ['content' => '查询目录不存在']));
        }
    }

}
