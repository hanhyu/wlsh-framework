<?php
declare(strict_types=1);

namespace App\Modules\System\controllers;

use App\Domain\System\Process as ProcessDomain;
use App\Models\Forms\SystemProcessForms;
use Exception;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-2-1
 * Time: 下午5:38
 */
class Process extends \Yaf\Controller_Abstract
{
    use \ControllersTrait;

    /**
     * @var ProcessDomain
     */
    protected $msg;

    public function init()
    {
        $this->beforeInit();
        $this->msg = new ProcessDomain();
    }

    /**
     * 列表
     * @throws Exception
     */
    public function getMsgListAction(): void
    {
        $data = $this->validator(SystemProcessForms::$getMsgList);
        $res  = $this->msg->getMsgList($data);
        if ($res) {
            $this->response->end(http_response(200, '', $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

    /**
     * User: hanhyu
     * Date: 19-6-5
     * Time: 下午8:42
     * @throws Exception
     */
    public function setMsgAction(): void
    {
        $data = $this->validator(SystemProcessForms::$setMsg);

        $data['crt_dt'] = date('Y-m-d H:i:s');
        $data['id']     = get_token_params($this->request->header['authorization'])['id'];

        $res = $this->msg->setMsg($data);
        if ($res) {
            $this->response->end(http_response(200, $data['name'] . '添加成功'));
        } else {
            $this->response->end(http_response(400, $data['name'] . '添加失败'));
        }
    }

}