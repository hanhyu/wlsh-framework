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
     * @router auth=true&method=get
     */
    public function getUserListAction(): void
    {
        $data = $this->validator(SystemLogForms::$getUserList);
        //$data['uid'] = get_token_params($this->request->header['authorization'])['id'];
        $res = $this->log->getLogList($data);
        $this->response->end(http_response(200, '', $res));
    }

}
