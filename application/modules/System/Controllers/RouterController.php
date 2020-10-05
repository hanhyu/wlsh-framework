<?php
declare(strict_types=1);

namespace App\Modules\System\Controllers;

use App\Domain\System\RouterDomain;
use App\Library\ControllersTrait;
use App\Library\ProgramException;
use App\Library\ValidateException;
use App\Models\Forms\SystemRouterForms;
use JsonException;

class RouterController
{
    use ControllersTrait;

    /**
     * @var RouterDomain
     */
    protected RouterDomain $router;

    public function __construct()
    {
        $this->beforeInit();
        $this->router = new RouterDomain();
    }

    /**
     * 获取路由列表数据
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=get
     */
    public function getListAction(): string
    {
        $data = $this->validator(SystemRouterForms::$getList);
        $res  = $this->router->getList($data);
        return http_response(200, '', $res);
    }


    /**
     * 添加路由信息
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=post
     */
    public function setRouterAction(): string
    {
        $data = $this->validator(SystemRouterForms::$setRouter);
        $res  = $this->router->setRouter($data);
        if ($res) {
            return http_response(200, '路由添加成功');
        }

        return http_response(500, '路由添加失败');
    }


    /**
     * 修改路由
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=put
     */
    public function editRouterAction(): string
    {
        $data = $this->validator(SystemRouterForms::$editRouter);
        $res  = $this->router->editRouter($data);
        if ($res) {
            return http_response(200, "{$data['name']}修改成功");
        }

        return http_response(500, "{$data['name']}修改失败");
    }

    /**
     * 删除路由
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=delete
     */
    public function delRouterAction(): string
    {
        $data = $this->validator(SystemRouterForms::$delRouter);
        $res  = $this->router->delRouter((int)$data['id']);
        if ($res) {
            return http_response(200, '', ['id' => $data['id']]);
        }

        return http_response(500, "{$data['id']}删除失败");
    }

    /**
     * @throws JsonException
     * @router auth=true&method=get
     */
    public function getInfoAction(): string
    {
        $res = $this->router->getInfo();
        if (!empty($res)) {
            return http_response(200, '', ['list' => $res]);
        }

        return http_response(500, '查询失败');
    }

}
