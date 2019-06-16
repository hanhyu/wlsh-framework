<?php
declare(strict_types=1);

namespace App\Modules\System\controllers;

use App\Domain\System\Menu as MenuDomain;
use App\Models\Forms\SystemMenuForms;

use Yaf\{
    Controller_Abstract,
    Registry
};

use Exception;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-8-31
 * Time: 下午4:09
 */
class Menu extends Controller_Abstract
{
    use \ControllersTrait;
    /**
     * @var MenuDomain
     */
    protected $menu;

    public function init()
    {
        $this->beforeInit();
        $this->menu = new MenuDomain();
    }

    /**
     *
     * @throws Exception
     */
    public function getMenuListAction(): void
    {
        $data = $this->validator(SystemMenuForms::$getMenuList);
        $res  = $this->menu->getList($data);
        if ($res) {
            $this->response->end(http_response(200, '', $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

    public function getMenuInfoAction():void {
        $data['menu']  = $this->menu->getInfo();
        $data['title'] = Registry::get('config')->page->title;
        $this->response->end(http_response(200, '', $data));
    }

    /**
     *
     * @throws Exception
     */
    public function setMenuAction(): void
    {
        $data = $this->validator(SystemMenuForms::$setMenu);
        $res  = $this->menu->setMenu($data);
        if ($res) {
            $this->response->end(http_response(200, $data['name'] . '菜单添加成功'));
        } else {
            $this->response->end(http_response(500, '菜单添加失败'));
        }
    }

    /**
     *
     * @throws Exception
     */
    public function getMenuAction(): void
    {
        $data = $this->validator(SystemMenuForms::$getMenu);
        $res  = $this->menu->getMenuById((int)$data['id']);
        if ($res) {
            $this->response->end(http_response(200, '', $res));
            return;
        } else {
            $this->response->end(http_response(500, '获取菜单失败'));
        }
    }

    /**
     *
     * @throws Exception
     */
    public function editMenuAction(): void
    {
        $data = $this->validator(SystemMenuForms::$setMenu);
        $res  = $this->menu->editMenu($data);
        if ($res) {
            $this->response->end(http_response(200, $data['name'] . '修改成功'));
        } else {
            $this->response->end(http_response(500, "{$data['name']}修改失败"));
        }
    }

    /**
     *
     * @throws Exception
     */
    public function delMenuAction(): void
    {
        $data = $this->validator(SystemMenuForms::$getMenu);
        $res  = $this->menu->delMenu((int)$data['id']);
        if ($res) {
            $this->response->end(http_response(200, '', ['id' => $data['id']]));
        } else {
            $this->response->end(http_response(500, "{$data['id']}删除失败"));
        }
    }

}