<?php
declare(strict_types=1);

use App\Domain\System\User;
use App\Models\Forms\SystemLogForms;

/**
 * 登录日志
 * User: hanhyu
 * Date: 19-1-15
 * Time: 下午2:49
 */
class LogUserController extends Yaf\Controller_Abstract
{
    use App\Library\ControllersTrait;

    /**
     * @var User
     */
    private $log;

    public function init()
    {
        $this->beforeInit();
        $this->log = new User();
    }

    /**
     * 列表
     * @throws Exception
     */
    public function getUserListAction(): void
    {
        $data = $this->validator(SystemLogForms::$getUserList);
        //$data['uid'] = get_token_params($this->request->header['authorization'])['id'];
        $res = $this->log->getLogList($data);
        if ($res) {
            $this->response->end(http_response(200, '', $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

}