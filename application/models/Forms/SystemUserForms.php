<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-12-11
 * Time: 下午10:51
 */

namespace App\Models\Forms;

class SystemUserForms
{
    /**
     * 表单字段
     * 格式为：array(方法名=>array(字段名=>验证规则))
     *
     */
    public static $userLogin = [
        'name'   => 'Required|StrLenGeLe:1,50',
        'pwd'    => 'Required|StrLenGeLe:3,20',
        'remark' => 'StrLenGeLe:1,50',
    ];

    public static $getUser = [
        'id' => 'Required|IntGe:1',
    ];

    public static $editUser = [
        'id'     => 'Required|IntGe:1',
        'status' => 'Required|IntGeLe:10,250',
        'remark' => 'StrLenGeLe:1,50',
    ];

    public static $pull = [
        'pwd' => 'Required|StrLenGeLe:3,50',
    ];

    public static $getUserList = [
        'curr_page' => 'IntGe:1',
        'page_size' => 'Required|IntGe:1',
    ];

    public static $editPwd = [
        'old_pwd' => 'Required|StrLenGeLe:3,50',
        'new_pwd' => 'Required|StrLenGeLe:3,50',
    ];

}