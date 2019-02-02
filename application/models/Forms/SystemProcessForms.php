<?php
/**
 * 非正常提示码
 * User: hanhyu
 * Date: 18-12-11
 * Time: 下午10:50
 */

namespace App\Models\Forms;

class SystemProcessForms extends AbstractForms
{
    /**
     * 添加内容
     * 格式为：array(方法名=>array(字段名=>验证规则))
     *
     */
    public function setMsg()
    {
        return [
            'content' => [
                'label'    => '内容',
                'name'     => 'content',
                'require'  => true,
                'message'  => '名称不能为空',
                'validate' => [
                    ['type' => 'string', 'min' => '1', 'max' => '1000', 'msg' => '内容输入不正确'],
                ],
            ],
        ];
    }

    /**
     * 获取内容列表
     * @return array
     */
    public function getMsgList()
    {
        return [
            'curr_page' => [
                'label'    => '列表页数',
                'name'     => 'curr_page',
                'require'  => false,
                'default'  => 1,
                'validate' => [
                    ['type' => 'int', 'min' => '1', 'msg' => '页数输入不正确'],
                ],
            ],
            'page_size' => [
                'label'    => '每页显示记录数',
                'name'     => 'page_size',
                'require'  => true,
                'message'  => '每页显示记录数不能为空',
                'validate' => [
                    ['type' => 'int', 'min' => '1', 'msg' => '每页显示记录数不能为空'],
                ],
            ],
            'id'        => [
                'label'    => '列表ID',
                'name'     => 'id',
                'require'  => false,
                'validate' => [
                    ['type' => 'int', 'min' => '1', 'msg' => '列表ID不正确'],
                ],
            ],
        ];
    }

}