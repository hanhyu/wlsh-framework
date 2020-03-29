<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
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
    public static array $info = [
        'name' => 'Required|StrLenGeLe:1,50|Alias:日志名称',
    ];


    public static array $getMongoList = [
        'curr_page'  => 'Required|IntGe:1|Alias:当前页数',
        'page_size'  => 'Required|IntGe:1|Alias:每页显示多少条',
        'start_time' => ['DateTime|Alias:开始时间', 'StrIn:'],
        'end_time'   => ['DateTime|Alias:结束时间', 'StrIn:'],
        'channel'    => ['StrLenGeLe:2,50|Alias:通道类型', 'StrIn:'],
    ];


    public static array $getMongoInfo = [
        'id' => 'Required|Str',
    ];


    /**
     * 用户登录日志
     * @return array
     */
    public static array $getUserList = [
        'curr_page'  => 'Required|IntGe:1|Alias:当前页数',
        'page_size'  => 'Required|IntGe:1|Alias:每页显示多少条',
        'login_time' => ['Date|Alias:登录时间', 'StrIn:'],
        'uname'      => ['StrLenGeLe:3,50|Alias:用户名', 'StrIn:'],
    ];

}
