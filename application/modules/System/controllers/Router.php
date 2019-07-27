<?php
declare(strict_types=1);

namespace App\Modules\System\controllers;

use App\Domain\System\Router as RouterDomain;
use App\Models\Forms\SystemRouterForms;
use Exception;

class Router extends \Yaf\Controller_Abstract
{
    use \ControllersTrait;
    /**
     * @var RouterDomain
     */
    protected $router;

    public function init()
    {
        $this->beforeInit();
        $this->router = new RouterDomain();
    }

    /**
     * 获取路由列表数据
     * @throws Exception
     */
    public function getListAction(): void
    {
        $data = $this->validator(SystemRouterForms::$getList);
        $res  = $this->router->getList($data);
        $this->response->end(http_response(200, '', $res));
    }


    /**
     * 添加路由信息
     * @throws Exception
     */
    public function setRouterAction(): void
    {
        $data = $this->validator(SystemRouterForms::$setRouter);
        $res  = $this->router->setRouter($data);
        if ($res) {
            $this->response->end(http_response(200, '路由添加成功'));
        } else {
            $this->response->end(http_response(500, '路由添加失败'));
        }
    }


    /**
     * 修改路由
     * @throws Exception
     */
    public function editRouterAction(): void
    {
        $data = $this->validator(SystemRouterForms::$editRouter);
        $res  = $this->router->editRouter($data);
        if ($res) {
            $this->response->end(http_response(200, "{$data['name']}修改成功"));
        } else {
            $this->response->end(http_response(500, "{$data['name']}修改失败"));
        }
    }

    /**
     * 删除路由
     * @throws Exception
     */
    public function delRouterAction(): void
    {
        $data = $this->validator(SystemRouterForms::$delRouter);
        $res  = $this->router->delRouter((int)$data['id']);
        if ($res) {
            $this->response->end(http_response(200, '', ['id' => $data['id']]));
        } else {
            $this->response->end(http_response(500, "{$data['id']}删除失败"));
        }
    }

    public function getInfoAction(): void
    {
        $res = $this->router->getInfo();
        if (!empty($res)) {
            $this->response->end(http_response(200, '', ['list' => $res]));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

}