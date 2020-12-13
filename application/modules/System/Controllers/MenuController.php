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
     */
    #[Router(method: 'GET', auth: true)]
    public function getMenuListAction(): string
    {
        $data = $this->validator(SystemMenuForms::$getMenuList);
        $res  = $this->menu->getList($data);
        return http_response(data: $res);
    }

    /**
     * @throws JsonException
     */
    #[Router(method: 'GET', auth: true)]
    public function getMenuInfoAction(): string
    {
        $data['menu']  = $this->menu->getInfo();
        $data['title'] = DI::get('config_arr')['page']['title'];
        return http_response(data: $data);
    }

    /**
     *
     * @throws ProgramException
     * @throws ValidateException|JsonException
     */
    #[Router(method: 'POST', auth: true)]
    public function setMenuAction(): string
    {
        $data = $this->validator(SystemMenuForms::$setMenu);
        $res  = $this->menu->setMenu($data);
        if ($res) {
            return http_response(200, $data['name'] . '菜单添加成功');
        }

        return http_response(500, '菜单添加失败');
    }

    /**
     *
     * @throws ProgramException
     * @throws ValidateException|JsonException
     */
    #[Router(method: 'GET', auth: true)]
    public function getMenuAction(): string
    {
        $data = $this->validator(SystemMenuForms::$getMenu);
        $res  = $this->menu->getMenuById((int)$data['id']);
        if (!empty($res)) {
            return http_response(data: $res);
        }

        return http_response(500, '获取菜单失败');
    }

    /**
     *
     * @throws ProgramException
     * @throws ValidateException|JsonException
     */
    #[Router(method: 'PUT', auth: true)]
    public function editMenuAction(): string
    {
        $data = $this->validator(SystemMenuForms::$setMenu);
        $res  = $this->menu->editMenu($data);
        if ($res) {
            return http_response(200, $data['name'] . '修改成功');
        }

        return http_response(500, "{$data['name']}修改失败");
    }

    /**
     *
     * @throws ProgramException
     * @throws ValidateException|JsonException
     */
    #[Router(method: 'DELETE', auth: true)]
    public function delMenuAction(): string
    {
        $data = $this->validator(SystemMenuForms::$getMenu);
        $res  = $this->menu->delMenu((int)$data['id']);
        if ($res) {
            return http_response(data: ['id' => $data['id']]);
        }

        return http_response(500, "{$data['id']}删除失败");
    }

}
