<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-12-11
 * Time: 下午10:51
 */

namespace App\Models\Forms;

class SystemLogForms extends AbstractForms
{
    /**
     * 表单字段
     * 格式为：array(方法名=>array(字段名=>验证规则))
     *
     */
    public function info()
    {
        return [
            'name' => [
                'label' => '名称',
                'name' => 'name',
                'require' => true,
                'message' => '名称不能为空',
                'validate' => [
                    ['type' => 'string', 'min' => '1', 'max' => '50', 'msg' => '名称输入不正确'],
                ],
            ],
        ];
    }

    public function getMongoList()
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
            'log_time' => [
                'label' => '记录时间',
                'name' => 'log_time',
                'require' => false,
                'validate' => [
                    ['type' => 'string', 'min' => '10', 'msg' => '记录时间不正确'],
                ],
            ],
        ];
    }

    public function getMongoInfo()
    {
        return [
            'id' => [
                'label' => '日志ID',
                'name' => 'id',
                'require' => true,
                'message' => '日志ID不能为空',
                'validate' => [
                    ['type' => 'string', 'msg' => '日志ID输入不正确'],
                ],
            ],
        ];
    }

    /**
     * 用户登录日志
     * @return array
     */
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
            'login_time' => [
                'label' => '登录时间',
                'name' => 'login_time',
                'require' => false,
                'validate' => [
                    ['type' => 'string', 'min' => '10', 'msg' => '登录时间不正确'],
                ],
            ],
            'uname' => [
                'label' => '用户名',
                'name' => 'uname',
                'require' => false,
                'validate' => [
                    ['type' => 'string', 'min' => '3', 'msg' => '用户名不正确'],
                ],
            ],
        ];
    }

}