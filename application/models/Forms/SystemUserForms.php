<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
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
    public static array $userLogin = [
        'name'   => 'Required|StrLenGeLe:1,50',
        'pwd'    => 'Required|StrLenGeLe:3,20',
        'remark' => 'StrLenGeLe:1,50',
    ];

    public static array $getUser = [
        'id' => 'Required|IntGe:1',
    ];

    public static array $editUser = [
        'id'     => 'Required|IntGe:1',
        'status' => 'Required|IntGeLe:10,250',
        'remark' => 'StrLenGeLe:1,50',
    ];

    public static array $pull = [
        'pwd' => 'Required|StrLenGeLe:3,50',
    ];

    public static array $getUserList = [
        'curr_page' => 'IntGe:1',
        'page_size' => 'Required|IntGe:1',
    ];

    public static array $editPwd = [
        'old_pwd' => 'Required|StrLenGeLe:3,50',
        'new_pwd' => 'Required|StrLenGeLe:3,50',
    ];

    public static array $getList = [
        'curr_page' => 'IntGe:1',
        'page_size' => 'Required|IntGe:1',
    ];

    public static array $existToken = [
        'uid'   => 'Required|StrLenGe:1',
        'token' => 'Required|StrLen:32',
    ];

}
