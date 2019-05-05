<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-12-11
 * Time: 下午5:45
 */

namespace App\Models\Forms;

class SystemMenuForms
{
    /**
     * id 菜单ID
     * name 菜单名
     * icon 图标
     * url 菜单链接
     * up_id 菜单上级ID
     * level 菜单等级
     *
     * User: hanhyu
     * Date: 19-5-4
     * Time: 下午10:35
     * @return array
     */
    public static function setMenu()
    {
        return [
            'id'    => 'IntGe:0',
            'name'  => 'Required|StrLenGeLe:3,50',
            'icon'  => 'Required|StrLenGeLe:1,50',
            'url'   => 'Required|StrLenGeLe:1,50',
            'up_id' => 'Required|IntGeLe:0,99',
            'level' => 'Required|IntGeLe:1,5',
        ];
    }

    public static function getMenu()
    {
        return ['id' => 'Required|IntGe:1'];
    }

    public static function getMenuList(): array
    {
        return [
            "curr_page" => "Required|IntGe:1",
            "page_size" => "Required|IntGe:1",
        ];
    }

}