<?php declare(strict_types=1);


namespace App\Modules\System\Controllers;


use App\Domain\System\LogDomain;
use App\Library\ControllersTrait;
use App\Library\ProgramException;
use App\Library\ValidateException;
use App\Models\Forms\SystemLogForms;

class LogRouterController
{
    use ControllersTrait;

    protected LogDomain $log;

    public function __construct()
    {
        $this->beforeInit(false);
        $this->log = new LogDomain();
    }

    /**
     * 获取路由日志列表
     *
     * User: hanhyu
     * Date: 2021/2/15
     * Time: 下午9:13
     * @return string
     * @throws ProgramException
     * @throws ValidateException|\JsonException
     */
    #[Router(method: 'GET', auth: true)]
    public function getListAction(): string
    {
        $data = $this->validator(SystemLogForms::$getRouterList);
        return http_response(data: $this->log->getRouterList($data));
    }

}
