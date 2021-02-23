<?php
declare(strict_types=1);

//注意这里的controllers必需要小写开头，yaf规范
namespace App\Modules\System\Controllers\V1;

use App\Library\ProgramException;
use App\Library\Router;
use App\Library\ValidateException;
use App\Modules\System\Controllers\MenuController as BaseMenu;
use App\Models\Forms\SystemMenuForms;

class MenuControllerController extends BaseMenu
{

    /**
     * v1版本重写方法
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午3:20
     * @throws ProgramException
     * @throws ValidateException|\JsonException
     */
    #[Router(method: 'GET', auth: true)]
    public function getMenuListAction(): string
    {
        $data = $this->validator(SystemMenuForms::$getMenuList);
        $res  = $this->menu->getList($data);
        if ($res) {
            return http_response(data: $res);
        }

        return http_response(500, '查询失败');
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午4:27
     * @throws \JsonException
     */
    #[Router(method: 'GET', auth: true)]
    public function getMenuInfoAction(): string
    {
        $data['menu'] = $this->menu->getInfo();
        return http_response(data: $data);
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午4:30
     * @throws \JsonException
     */
    #[Router(method: 'GET', auth: true)]
    public function getRedisAction(): string
    {
        $data = $this->menu->getRedis('key');
        return http_response(data: ['content' => $data]);
    }

}
