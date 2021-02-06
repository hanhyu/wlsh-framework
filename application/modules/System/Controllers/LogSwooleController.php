<?php
declare(strict_types=1);

namespace App\Modules\System\Controllers;

use App\Library\ControllersTrait;
use App\Library\ProgramException;
use App\Library\ValidateException;
use App\Models\Forms\SystemLogForms;
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
     */
    #[Router(method: 'GET', auth: true)]
    public function getInfoAction(): string
    {
        $data = $this->validator(SystemLogForms::$info);
        return http_response(data: [
            'content' => file_get_contents(ROOT_PATH . '/log/' . $data['name']),
        ]);
    }

    /**
     * 清空swoole日志
     * @throws ProgramException
     * @throws ValidateException|JsonException
     */
    #[Router(method: 'POST', auth: true)]
    public function cleanLogAction(): string
    {
        $data = $this->validator(SystemLogForms::$info);
        if ($data['name'] === 'swoole.log' || $data['name'] === 'swoolePid.log') {
            $fp = fopen(ROOT_PATH . '/log/' . $data['name'], 'wb+');
        } else { //monolog日志
            $fp = fopen(ROOT_PATH . '/log/monolog/' . $data['name'], 'wb+');
        }
        $content = fwrite($fp, '日志已清空。。。');
        fclose($fp);
        return http_response(data: ['content' => $content]);
    }

    /**
     * 查询monolog日志
     * @throws ProgramException
     * @throws ValidateException|JsonException
     */
    #[Router(method: 'GET', auth: true)]
    public function getMonologAction(): string
    {
        $data = $this->validator(SystemLogForms::$info);
        $file = ROOT_PATH . '/log/monolog/' . $data['name'];
        if (is_file($file)) {
            $filesize = number_format(filesize($file) / 1024, 2);
            if (30720 < $filesize) {
                return http_response(400, "文件太大：{$filesize} kb，请直接查看。");
            }

            return http_response(data: [
                'content' => file_get_contents($file),
            ]);
        }

        return http_response(data: ['content' => '查询目录不存在']);
    }

}
