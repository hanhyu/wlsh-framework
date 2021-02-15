<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-12-11
 * Time: 下午5:45
 */

namespace App\Models\Forms;

class SystemMenuForms
{
    /**
     *
     * UserDomain: hanhyu
     * Date: 19-5-4
     * Time: 下午10:35
     * @return array
     */
    public static array $setMenu = [
        'id'    => 'IntGe:0|Alias:菜单ID',
        'name'  => 'Required|StrLenGeLe:3,50|Alias:菜单名',
        'icon'  => 'Required|StrLenGeLe:1,50|Alias:图标',
        'url'   => 'Required|StrLenGeLe:1,50|Alias:菜单链接',
        'up_id' => 'Required|IntGeLe:0,99|Alias:菜单上级ID',
        'level' => 'Required|IntGeLe:1,5|Alias:菜单等级',
    ];

    public static array $getMenu = [
        'id' => 'Required|IntGe:1',
    ];

    public static array $getMenuList = [
        'curr_page' => 'Required|IntGe:1|Alias:当前页',
        'page_size' => 'Required|IntGe:1|Alias:每页显示多少条',
    ];

}
