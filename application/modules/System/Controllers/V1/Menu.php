<?php
declare(strict_types=1);

//注意这里的controllers必需要小写开头，yaf规范
namespace App\Modules\System\Controllers\V1;

use App\Library\ProgramException;
use App\Library\ValidateException;
use App\Modules\System\Controllers\Menu as BaseMenu;
use App\Models\Forms\SystemMenuForms;

class Menu extends BaseMenu
{

    /**
     * v1版本重写方法
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午3:20
     * @throws ProgramException
     * @throws ValidateException
     */
    public function getMenuListAction(): void
    {
        $data = $this->validator(SystemMenuForms::$getMenuList);
        $res  = $this->menu->getList($data);
        if ($res) {
            $this->response->end(http_response(200, 'success', $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

    public function getMenuInfoAction(): void
    {
        $data['menu'] = $this->menu->getInfo();
        $this->response->end(http_response(200, '', $data));
    }

    public function getRedisAction(): void
    {
        $data = $this->menu->getRedis('key');
        $this->response->end(http_response(200, '', ['content' => $data]));
    }

}
