<?php

namespace App\Models\Forms;

class SystemRouterForms
{
    public static $setRouter = [
        'name'    => 'Required|StrLenGeLe:2,20|Alias:路由名称',
        'url'     => 'Required|StrLenGeLe:3,200|Alias:请求链接',
        'auth'    => 'Required|IntIn:0,1|Alias:认证',
        'method'  => 'Required|StrIn:GET,POST,PUT,DELETE,CLI|Alias:请求方法',
        'action'  => 'Required|StrLenGeLe:1,200|Alias:实际路由',
        'type'    => 'Required|IntIn:0,1|Alias:路由类型',
        'menu_id' => 'Required|IntGeLe:0,100|Alias:路由所属类别',
        'comment' => 'Required|StrLenGeLe:1,20|Alias:路由说明',
    ];

    public static $editRouter = [
        'id'      => 'Required|IntGe:1',
        'name'    => 'Required|StrLenGeLe:2,20',
        'url'     => 'Required|StrLenGeLe:3,200',
        'auth'    => 'Required|IntIn:0,1',
        'method'  => 'Required|StrIn:GET,POST,PUT,DELETE,CLI',
        'action'  => 'Required|StrLenGeLe:1,200',
        'type'    => 'Required|IntIn:0,1',
        'menu_id' => 'Required|IntGeLe:0,100',
        'comment' => 'Required|StrLenGeLe:1,20',
    ];

    public static $getList = [
        'curr_page' => 'IntGe:1|Alias:列表页数',
        'page_size' => 'Required|IntGe:1|Alias:每页显示记录数',
    ];

    public static $delRouter = [
        'id' => 'Required|IntGe:1|Alias:路由id',
    ];

}