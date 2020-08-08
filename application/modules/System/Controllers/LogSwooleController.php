<?php
declare(strict_types=1);

namespace App\Modules\System\Controllers;

use App\Library\ControllersTrait;
use App\Library\ProgramException;
use App\Library\ValidateException;
use App\Models\Forms\SystemLogForms;
use Swoole\Coroutine;
use JsonException;

/**
 * 获取日志类
 * UserDomain: hanhyu
 * Date: 18-9-29
 * Time: 下午3:02
 */
class LogSwooleController
{
    use ControllersTrait;

    public function __construct()
    {
        $this->beforeInit();
    }

    /**
     * 查看swoole日志信息
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=get
     */
    public function getInfoAction(): void
    {
        $data      = $this->validator(SystemLogForms::$info);
        $file_path = ROOT_PATH . '/log/' . $data['name'];
        $fp        = fopen($file_path, 'rb');
        $content   = fread($fp, filesize($file_path));
        fclose($fp);
        $this->response->end(http_response(200, '', ['content' => $content]));
    }

    /**
     * 清空swoole日志
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=post
     */
    public function cleanLogAction(): void
    {
        $data = $this->validator(SystemLogForms::$info);
        if ($data['name'] === 'swoole.log' || $data['name'] === 'swoolePid.log') {
            $fp = fopen(ROOT_PATH . '/log/' . $data['name'], 'wb+');
        } else { //monolog日志
            $fp = fopen(ROOT_PATH . '/log/monolog/' . $data['name'], 'wb+');
        }
        $content = fwrite($fp, '日志已清空。。。');
        fclose($fp);
        $this->response->end(http_response(200, '', ['content' => $content]));
    }

    /**
     * 查询monolog日志
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=get
     */
    public function getMonologAction(): void
    {
        $data = $this->validator(SystemLogForms::$info);
        $file = ROOT_PATH . '/log/monolog/' . $data['name'];
        if (is_file($file)) {
            $fp      = fopen($file, 'rb');
            $content = fread($fp, filesize($file));
            fclose($fp);
            $this->response->end(http_response(200, '', ['content' => $content]));
        } else {
            $this->response->end(http_response(200, '', ['content' => '查询目录不存在']));
        }
    }

}
