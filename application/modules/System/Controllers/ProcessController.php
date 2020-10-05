<?php
declare(strict_types=1);

namespace App\Modules\System\Controllers;

use App\Domain\System\ProcessDomain;
use App\Library\ControllersTrait;
use App\Library\ProgramException;
use App\Library\ValidateException;
use App\Models\Forms\SystemProcessForms;
use JsonException;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-2-1
 * Time: 下午5:38
 */
class ProcessController
{
    use ControllersTrait;

    /**
     * @var ProcessDomain
     */
    protected ProcessDomain $msg;

    public function __construct()
    {
        $this->beforeInit();
        $this->msg = new ProcessDomain();
    }

    /**
     * 列表
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=get
     */
    public function getMsgListAction(): string
    {
        $data = $this->validator(SystemProcessForms::$getMsgList);
        $res  = $this->msg->getMsgList($data);
        return http_response(200, '', $res);
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-5
     * Time: 下午8:42
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=post
     */
    public function setMsgAction(): string
    {
        $data = $this->validator(SystemProcessForms::$setMsg);

        $data['crt_dt'] = date('Y-m-d H:i:s');
        $data['id']     = get_token_params($this->request->header['authorization'])['id'];

        $res = $this->msg->setMsg($data);
        if ($res) {
            return http_response(200, $data['name'] . '添加成功');
        }

        return http_response(400, $data['name'] . '添加失败');
    }

}
