<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-12-11
 * Time: 下午10:51
 */

namespace App\Models\Forms;

class SystemLogForms
{
    /**
     * 表单字段
     * 格式为：array(方法名=>array(字段名=>验证规则))
     *
     */
    public static function info()
    {
        return [
            'name' => 'Required|StrLenGeLe:1,50',
        ];
    }

    public static function getMongoList()
    {
        return [
            'curr_page' => 'Required|IntGe:1',
            'page_size' => 'Required|IntGe:1',
            'log_time'  => ['Date', 'StrIn:'],
        ];
    }

    public static function getMongoInfo()
    {
        return [
            'id' => 'Required|Str',
        ];
    }

    /**
     * 用户登录日志
     * @return array
     */
    public static function getUserList()
    {
        return [
            'curr_page'  => 'Required|IntGe:1',
            'page_size'  => 'Required|IntGe:1',
            'login_time' => ['Date', 'StrIn:'],
            'uname'      => ['StrLenGeLe:3,50', 'StrIn:'],
        ];
    }

}