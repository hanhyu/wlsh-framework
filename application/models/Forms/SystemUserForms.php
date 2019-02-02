<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-12-11
 * Time: 下午10:51
 */

namespace App\Models\Forms;

class SystemUserForms extends AbstractForms
{
    /**
     * 表单字段
     * 格式为：array(方法名=>array(字段名=>验证规则))
     *
     */
    public function userLogin()
    {
        return [
            'name' => [
                'label' => '用户名',
                'name' => 'name',
                'require' => true,
                'message' => '用户名不能为空',
                'validate' => [
                    ['type' => 'string', 'min' => '1', 'max' => '50', 'msg' => '用户名输入不正确'],
                ],
            ],
            'pwd' => [
                'label' => '密码',
                'name' => 'pwd',
                'require' => true,
                'message' => '密码不能为空',
                'validate' => [
                    ['type' => 'string', 'min' => '3', 'max' => '20', 'msg' => '密码输入不正确'],
                ],
            ],
            'remark' => [
                'label' => '备注',
                'name' => 'remark',
                'require' => false,
                'default' => '0',
                'validate' => [
                    ['type' => 'string', 'min' => '1', 'max' => '50', 'msg' => '备注输入不正确'],
                ],
            ],
        ];
    }

    public function getUser()
    {
        return [
            'id' => [
                'label' => '用户ID',
                'name' => 'id',
                'require' => true,
                'message' => '用户ID不能为空',
                'validate' => [
                    ['type' => 'int', 'min' => '1', 'msg' => '用户ID输入不正确'],
                ],
            ],
        ];
    }

    public function editUser()
    {
        return [
            'id' => [
                'label' => '用户ID',
                'name' => 'id',
                'require' => true,
                'message' => '用户ID不能为空',
                'validate' => [
                    ['type' => 'int', 'min' => '1', 'msg' => '用户ID输入不正确'],
                ],
            ],
            'status' => [
                'label' => '用户状态',
                'name' => 'status',
                'require' => true,
                'message' => '用户状态不能为空',
                'validate' => [
                    ['type' => 'int', 'min' => '10', 'max' => '250', 'msg' => '用户状态输入不正确'],
                ],
            ],
            'remark' => [
                'label' => '备注',
                'name' => 'remark',
                'require' => false,
                'default' => '0',
                'validate' => [
                    ['type' => 'string', 'min' => '1', 'max' => '50', 'msg' => '备注输入不正确'],
                ],
            ],
        ];
    }

    public function pull()
    {
        return [
            'pwd' => [
                'label' => '密码',
                'name' => 'pwd',
                'require' => true,
                'message' => '密码不能为空',
                'validate' => [
                    ['type' => 'string', 'min' => '3', 'max' => '50', 'msg' => '密码输入不正确'],
                ],
            ],
        ];
    }

    public function getUserList()
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