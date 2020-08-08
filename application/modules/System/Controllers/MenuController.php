<?php
declare(strict_types=1);

namespace App\Modules\System\Controllers;

use App\Domain\System\MenuDomain;
use App\Library\ControllersTrait;
use App\Library\DI;
use App\Library\ProgramException;
use App\Library\ValidateException;
use App\Models\Forms\SystemMenuForms;
use JsonException;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-8-31
 * Time: 下午4:09
 */
class MenuController
{
    use ControllersTrait;

    /**
     * @var MenuDomain
     */
    protected MenuDomain $menu;

    public function __construct()
    {
        $this->beforeInit();
        $this->menu = new MenuDomain();
    }

    /**
     *
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=get
     */
    public function getMenuListAction(): void
    {
        $data = $this->validator(SystemMenuForms::$getMenuList);
        $res  = $this->menu->getList($data);
        $this->response->end(http_response(200, '', $res));
    }

    /**
     * @throws JsonException
     * @router auth=true&method=get
     */
    public function getMenuInfoAction(): void
    {
        $data['menu']  = $this->menu->getInfo();
        $data['title'] = DI::get('config_arr')['page']['title'];
        $this->response->end(http_response(200, '', $data));
    }

    /**
     *
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=post
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
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=get
     */
    public function getMenuAction(): void
    {
        $data = $this->validator(SystemMenuForms::$getMenu);
        $res  = $this->menu->getMenuById((int)$data['id']);
        if (!empty($res)) {
            $this->response->end(http_response(200, '', $res));
        } else {
            $this->response->end(http_response(500, '获取菜单失败'));
        }
    }

    /**
     *
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=put
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
     * @throws ProgramException
     * @throws ValidateException|JsonException
     * @router auth=true&method=delete
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
