<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-12-11
 * Time: 下午10:51
 */

namespace App\Models\Forms;

class SystemBackupForms extends AbstractForms
{
    /**
     * 表单字段
     * 格式为：array(方法名=>array(字段名=>验证规则))
     *
     */
    public function del()
    {
        return [
            'id' => [
                'label' => '数据ID',
                'name' => 'id',
                'require' => true,
                'message' => '数据ID不能为空',
                'validate' => [
                    ['type' => 'int', 'min' => '1', 'msg' => '数据ID输入不正确'],
                ],
            ],
            'filename' => [
                'label' => '文件名',
                'name' => 'filename',
                'require' => true,
                'message' => '文件名不能为空',
                'validate' => [
                    ['type' => 'string', 'min' => '1', 'max' => '50', 'msg' => '文件名输入不正确'],
                ],
            ],
        ];
    }

}