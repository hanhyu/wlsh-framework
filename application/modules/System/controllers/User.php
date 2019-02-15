<?php
declare(strict_types=1);

use \App\Services\System\UserServices;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-9-3
 * Time: 下午4:57
 */
class UserController extends Yaf\Controller_Abstract
{
    use \App\Library\ControllersTrait;

    /**
     * @var UserServices
     */
    private $user;

    public function init()
    {
        $this->beforeInit();
        $this->user = new UserServices();
    }

    /**
     * 创建用户
     * @throws Exception
     */
    public function setUserAction(): void
    {
        $data = $this->validator('SystemUserForms', 'userLogin');
        $info = $this->user->getInfoByName($data['name']);
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
     * @throws Exception
     */
    public function getUserListAction(): void
    {
        $data = $this->validator('SystemUserForms', 'getUserList');
        $res  = $this->user->getInfoList($data);
        if ($res) {
            $this->response->end(http_response(200, $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

    /**
     * 删除用户
     * @throws Exception
     */
    public function delUserAction(): void
    {
        $data = $this->validator('SystemUserForms', 'getUser');
        $res  = $this->user->delUser((int)$data['id']);
        if ($res) {
            $this->response->end(http_response(200, ['id' => $data['id']]));
        } else {
            $this->response->end(http_response(400, "{$data['id']}删除失败"));
        }
    }

    /**
     * 根据id获取用户信息
     * @throws Exception
     */
    public function getUserAction(): void
    {
        $data = $this->validator('SystemUserForms', 'getUser');
        $res  = $this->user->getUserById((int)$data['id']);
        if ($res) {
            $this->response->end(http_response(200, $res));
        }
    }

    /**
     * 修改用户信息
     * @throws Exception
     */
    public function editUserAction(): void
    {
        $data = $this->validator('SystemUserForms', 'editUser');
        $res  = $this->user->editUser($data);
        if ($res) {
            $this->response->end(http_response(200, $data['id'] . '修改成功'));
        } else {
            $this->response->end(http_response(400, "{$data['id']}修改失败"));
        }
    }

    /**
     * 用户登录
     * @throws Exception
     */
    public function loginAction(): void
    {
        $data = $this->validator('SystemUserForms', 'userLogin');
        $info = $this->user->getInfoByName($data['name']);
        if (!empty($info)) {
            if ($info[0]['status'] == 0) {
                $this->response->end(http_response(400, '该用户处于禁用状态'));
                return;
            } else if (password_verify($data['pwd'], $info[0]['pwd'])) {
                $params['id']   = $info[0]['id'];
                $params['name'] = $info[0]['name'];
                $params['time'] = time();
                $token          = get_token($params);
                //$this->response->cookie('token', $token);
                $this->response->end(http_response(200, ['token' => $token]));

                $params['ip'] = ip2long(get_ip($this->request->server));
                $this->user->setLoginLog($params);
                //模拟日志发送邮件
                //task_log($this->server, $data['name'], '用户登录:', 'alert');
                return;
            }
        }
        $this->response->end(http_response(400, '用户名或密码错误'));
    }

    /**
     * 用户退出
     */
    public function logoutAction(): void
    {
        $token = get_token_params(strval($this->request->header['authorization']));
        $this->user->setLogoutLog($token);
        $this->response->end(http_response());
    }

    /**
     * 自动更新服务器代码钩子
     * @throws Exception
     */
    public function pullAction(): void
    {
        $data = $this->validator('SystemUserForms', 'pull');
        //执行钩子方法，让程序自动更新git项目
        if ($data['pwd'] == 'wlsh_base_frame') {
            $shell = "cd /home/baseFrame/ && git pull";
            exec($shell, $result, $status);
            if ($status) {
                $this->response->end(http_response(400, 'git自动更新数据失败'));
            } else { //成功
                $this->response->end(http_response(200, ['content' => $result]));
                //返回pull更新代码后重载服务
                $this->server->defer(function () {
                    $this->server->reload();
                });
            }
        } else {
            $this->response->end(http_response(400, '密码错误'));
        }
    }

}