<?php
declare(strict_types=1);

namespace App\Modules\System\Controllers;

use App\Domain\System\UserDomain;
use App\Library\ControllersTrait;
use App\Library\ProgramException;
use App\Models\Forms\SystemUserForms;
use App\Library\ValidateException;
use JsonException;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-9-3
 * Time: 下午4:57
 */
class UserController
{
    use ControllersTrait;

    /**
     * @var UserDomain
     */
    protected UserDomain $user;

    public function __construct()
    {
        $this->beforeInit();
        $this->user = new UserDomain();
    }

    /**
     * 创建用户
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=post
     */
    public function setUserAction(): void
    {
        $data = $this->validator(SystemUserForms::$userLogin);
        $info = $this->user->existName($data['name']);
        if (!empty($info)) {
            $this->response->end(http_response(400, '该用户名已存在'));
            return;
        }

        $res = $this->user->setUser($data);
        if ($res) {
            $this->response->end(http_response(200, $data['name'] . '注册成功'));
        } else {
            $this->response->end(http_response(400, $data['name'] . '注册失败'));
        }
    }

    /**
     * 用户列表
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=get
     */
    public function getUserListAction(): void
    {
        $data = $this->validator(SystemUserForms::$getUserList);
        $res  = $this->user->getInfoList($data);
        $this->response->end(http_response(200, '', $res));
    }

    /**
     * 删除用户
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=delete
     */
    public function delUserAction(): void
    {
        $data = $this->validator(SystemUserForms::$getUser);
        $res  = $this->user->delUser((int)$data['id']);
        if ($res) {
            $this->response->end(http_response(200, '', ['id' => $data['id']]));
        } else {
            $this->response->end(http_response(400, "{$data['id']}删除失败"));
        }
    }

    /**
     * 根据id获取用户信息
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=get
     */
    public function getUserAction(): void
    {
        $data = $this->validator(SystemUserForms::$getUser);
        $res  = $this->user->getUserById((int)$data['id']);
        if (!empty($res)) {
            $this->response->end(http_response(200, '', $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

    /**
     * 修改用户信息
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=put
     */
    public function editUserAction(): void
    {
        $data = $this->validator(SystemUserForms::$editUser);
        $res  = $this->user->editUser($data);
        if ($res) {
            $this->response->end(http_response(200, $data['id'] . '修改成功'));
        } else {
            $this->response->end(http_response(400, "{$data['id']}修改失败"));
        }
    }

    /**
     * 用户登录
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=false&method=post
     */
    public function loginAction(): void
    {
        $data = $this->validator(SystemUserForms::$userLogin);
        $info = $this->user->getInfoByName($data['name']);
        if (!empty($info)) {
            if ($info[0]['status'] === 0) {
                $resp_content = http_response(400, '该用户处于禁用状态');
            } else if (password_verify($data['pwd'], $info[0]['pwd'])) {
                $params['id']   = $info[0]['id'];
                $params['name'] = $info[0]['name'];
                $params['time'] = time();
                $token          = get_token($params);
                //$this->response->cookie('token', $token);
                $resp_content = http_response(200, '', ['token' => $token]);

                $params['ip'] = ip2long($this->request->header['x-real-ip'] ?? get_ip($this->request->server));
                $this->user->setLoginLog($params);
                //模拟日志发送邮件
                //task_log($this->server, $data['name'], '用户登录:', 'alert');
            } else {
                $resp_content = http_response(400, '用户名或密码错误');
            }
        } else {
            $resp_content = http_response(400, '用户名或密码错误');
        }
        sign($this->cid, $resp_content);
        $this->response->end($resp_content);
    }

    /**
     * 用户退出
     * @router auth=true&method=post
     */
    public function logoutAction(): void
    {
        $token = get_token_params((string)$this->request->header['authorization']);
        $this->user->setLogoutLog($token);
        $this->response->end(http_response());
    }

    /**
     * 自动更新服务器代码钩子
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=post
     */
    public function pullAction(): void
    {
        $data = $this->validator(SystemUserForms::$pull);
        //执行钩子方法，让程序自动更新git项目
        if ($data['pwd'] === 'wlsh_base_frame') {
            $shell = 'cd /home/baseFrame/ && git pull';
            exec($shell, $result, $status);
            if ($status) {
                $this->response->end(http_response(400, 'git自动更新数据失败'));
            } else { //成功
                $this->response->end(http_response(200, '', ['content' => $result]));
                //返回pull更新代码后重载服务
                $this->server->defer(function () {
                    $this->server->reload();
                });
            }
        } else {
            $this->response->end(http_response(400, '密码错误'));
        }
    }

    /**
     * UserDomain: hanhyu
     * Date: 2019/11/10
     * Time: 下午10:15
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=post
     */
    public function editPwdAction(): void
    {
        $data = $this->validator(SystemUserForms::$editPwd);

        $headers     = $this->request->header;
        $data['uid'] = get_token_params($headers['authorization'])['id'];

        $res = $this->user->editPwd($data);

        if (-1 === $res) {
            $this->response->end(http_response(400, '旧密码错误'));
        } else if (!empty($res)) {
            $this->response->end(http_response(200, '密码修改成功'));
        } else {
            $this->response->end(http_response(500, '修改密码失败，请重新操作'));
        }
    }


}
