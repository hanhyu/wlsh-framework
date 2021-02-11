<?php
declare(strict_types=1);

namespace App\Modules\System\Controllers;

use App\Domain\System\UserDomain;
use App\Library\ControllersTrait;
use App\Library\ProgramException;
use App\Library\ValidateException;
use App\Models\Forms\SystemLogForms;
use JsonException;

/**
 * 登录日志
 * UserDomain: hanhyu
 * Date: 19-1-15
 * Time: 下午2:49
 */
class LogUserController
{
    use ControllersTrait;

    /**
     * @var UserDomain
     */
    protected UserDomain $log;

    public function __construct()
    {
        $this->beforeInit();
        $this->log = new UserDomain();
    }

    /**
     * 列表
     * @throws ProgramException
     * @throws ValidateException|JsonException
     */
    #[Router(method: 'GET', auth: true)]
    public function getUserListAction(): string
    {
        $data = $this->validator(SystemLogForms::$getUserList);
        $res = $this->log->getLogList($data);
        return http_response(data: $res);
    }

}
