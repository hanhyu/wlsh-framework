<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-12-11
 * Time: 下午5:45
 */

namespace App\Models\Forms;

class SystemMenuForms extends AbstractForms
{
    public function setMenu()
    {
        return [
            'id' => [
                'label' => '菜单ID',
                'name' => 'id',
                'require' => false,
                'default' => 0,
                'validate' => [
                    ['type' => 'int', 'min' => '0', 'msg' => '菜单ID输入不正确'],
                ],
            ],
            'name' => [
                'label' => '菜单名',
                'name' => 'name',
                'require' => true,
                'message' => '菜单名不能为空',
                'validate' => [
                    ['type' => 'string', 'min' => '3', 'max' => '50', 'msg' => '菜单名输入不正确'],
                ],
            ],
            'icon' => [
                'label' => '图标',
                'name' => 'icon',
                'require' => true,
                'message' => '图标不能为空',
                'validate' => [
                    ['type' => 'string', 'min' => '1', 'max' => '50', 'msg' => '菜单图标输入不正确'],
                ],
            ],
            'url' => [
                'label' => '菜单链接',
                'name' => 'url',
                'require' => true,
                'message' => '菜单链接不能为空',
                'validate' => [
                    ['type' => 'string', 'min' => '1', 'max' => '50', 'msg' => '菜单链接输入不正确'],
                ],
            ],
            'up_id' => [
                'label' => '菜单上级ID',
                'name' => 'up_id',
                'require' => true,
                'message' => '菜单上级ID不能为空',
                'validate' => [
                    ['type' => 'int', 'min' => '0', 'max' => '99', 'msg' => '菜单上级ID输入不正确'],
                ],
            ],
            'level' => [
                'label' => '菜单等级',
                'name' => 'level',
                'require' => true,
                'message' => '菜单等级不能为空',
                'validate' => [
                    ['type' => 'int', 'min' => '1', 'max' => '5', 'msg' => '菜单等级输入不正确'],
                ],
            ],
        ];
    }

    public function getMenu()
    {
        return [
            'id' => [
                'label' => '菜单ID',
                'name' => 'id',
                'require' => true,
                'message' => '菜单ID不能为空',
                'validate' => [
                    ['type' => 'int', 'min' => '1', 'msg' => '菜单ID输入不正确'],
                ],
            ],
        ];
    }

    public function getMenuList()
    {
        return [
            'curr_page' => [
                'label' => '列表页数',
                'name' => 'curr_page',
                'require' => false,
                'default' => 1,
                'validate' => [
                    ['type' => 'int', 'min' => '1', 'msg' => '页数输入不正确'],
                ],
            ],
            'page_size' => [
                'label' => '每页显示记录数',
                'name' => 'page_size',
                'require' => true,
                'message' => '每页显示记录数不能为空',
                'validate' => [
                    ['type' => 'int', 'min' => '1', 'msg' => '每页显示记录数不能为空'],
                ],
            ],
        ];
    }

}