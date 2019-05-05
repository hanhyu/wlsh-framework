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
    public static function userLogin()
    {
        return [
            'name'   => 'Required|StrLenGeLe:1,50',
            'pwd'    => 'Required|StrLenGeLe:3,20',
            'remark' => 'StrLenGeLe:1,50',
        ];
    }

    public static function getUser()
    {
        return [
            'id' => 'Required|IntGe:1',
        ];
    }

    public static function editUser()
    {
        return [
            'id'     => 'Required|IntGe:1',
            'status' => 'Required|IntGeLe:10,250',
            'remark' => 'StrLenGeLe:1,50',
        ];
    }

    public static function pull()
    {
        return [
            'pwd' => 'Required|StrLenGeLe:3,50',
        ];
    }

    public static function getUserList()
    {
        return [
            'curr_page' => 'IntGe:1',
            'page_size' => 'Required|IntGe:1',
        ];
    }

}