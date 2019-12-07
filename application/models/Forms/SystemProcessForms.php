<?php
/**
 * 非正常提示码
 * UserDomain: hanhyu
 * Date: 18-12-11
 * Time: 下午10:50
 */

namespace App\Models\Forms;

class SystemProcessForms
{
    /**
     * 添加内容
     * 格式为：array(方法名=>array(字段名=>验证规则))
     *
     */
    public static array $setMsg = [
        'content' => 'Required|StrLenGeLe:1,1000',
    ];

    /**
     * 获取内容列表
     * @return array
     */
    public static array $getMsgList = [
        'curr_page' => 'Required|IntGe:1',
        'page_size' => 'Required|IntGe:1',
        'id'        => ['IntGe:1', 'StrIn:'],
    ];

}
