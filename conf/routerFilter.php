<?php
/**
 * 允许外部http访问的方法，格式：key=>value
 * key是请求的uri，value是转发的路由相关参数组
 *
 * value参数说明：
 * auth值是需要使用authorization进行token认证的路由，false不需要，true需要
 * method值是请求的http方法
 * action值为*时代表路由直接转发请求的uri，当设定其他值时代表转发指定的路由，指定格式：/modules/controller/action
 *
 * 注意：uri与action结尾不能有/字符
 */
return [
    /***************************************** 测试服务相关路由 *****************************************/
    '/login/test' => ['auth' => false, 'method' => 'GET', 'action' => '*'],

    '/login/get_redis'          => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/get_user_list'      => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/get_mongo_log_list' => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/get_log_user_list'  => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/get_log_user_view'  => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/get_user_info'  => ['auth' => false, 'method' => 'GET', 'action' => '*'],

    '/login/publisher_redis' => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/consumer_redis'  => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/ack_redis'       => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/del_redis'       => ['auth' => false, 'method' => 'GET', 'action' => '*'],

    '/login/set_es' => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/get_es' => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/set_xs' => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/get_xs' => ['auth' => false, 'method' => 'GET', 'action' => '*'],

    '/login/co_mysql' => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/sw_mysql' => ['auth' => false, 'method' => 'GET', 'action' => '*'],
    '/login/sw_pgsql' => ['auth' => false, 'method' => 'GET', 'action' => '*'],

    /***************************************** 用户设置相关路由 *****************************************/

    '/system/user/login'         => ['auth' => false, 'method' => 'POST', 'action' => '*'],
    '/system/user/logout'        => ['auth' => true, 'method' => 'POST', 'action' => '*'],
    '/system/user/get_user'      => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/user/get_user_list' => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/user/set_user'      => ['auth' => true, 'method' => 'POST', 'action' => '*'],
    '/system/user/del_user'      => ['auth' => true, 'method' => 'DELETE', 'action' => '*'],
    '/system/user/edit_user'     => ['auth' => true, 'method' => 'PUT', 'action' => '*'],
    '/system/user/pull'          => ['auth' => true, 'method' => 'POST', 'action' => '*'],

    /***************************************** 系统菜单相关路由 *****************************************/

    '/system/menu/get_menu_info'    => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/menu/get_menu_list'    => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/v1/menu/get_menu_list' => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/v1/menu/get_menu_info' => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/v1/menu/get_redis'     => ['auth' => false, 'method' => 'GET', 'action' => '*'],

    '/system/menu/set_menu'      => ['auth' => true, 'method' => 'POST', 'action' => '*'],
    '/system/menu/get_menu'      => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/menu/edit_menu'     => ['auth' => true, 'method' => 'PUT', 'action' => '*'],
    '/system/menu/del_menu'      => ['auth' => true, 'method' => 'DELETE', 'action' => '*'],
    '/system/router/get_list'    => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/router/set_router'  => ['auth' => true, 'method' => 'POST', 'action' => '*'],
    '/system/router/del_router'  => ['auth' => true, 'method' => 'DELETE', 'action' => '*'],
    '/system/router/edit_router' => ['auth' => true, 'method' => 'PUT', 'action' => '*'],

    /***************************************** 首页服务状况相关路由 *************************************/

    '/system/server_status/get_status' => ['auth' => true, 'method' => 'GET', 'action' => '*'],

    /***************************************** 数据库备份相关路由 ***************************************/

    '/system/backup/index'    => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/backup/add'      => ['auth' => true, 'method' => 'POST', 'action' => '*'],
    '/system/backup/get_list' => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/backup/down'     => ['auth' => true, 'method' => 'POST', 'action' => '*'],
    '/system/backup/del'      => ['auth' => true, 'method' => 'DELETE', 'action' => '*'],

    /***************************************** 日志中心相关路由 *****************************************/

    '/system/log_mongo/get_mongo_list' => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/log_mongo/get_mongo_info' => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/log_swoole/get_info'      => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/log_swoole/clean_log'     => ['auth' => true, 'method' => 'POST', 'action' => '*'],
    '/system/log_swoole/get_monolog'   => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/log_user/get_user_list'   => ['auth' => true, 'method' => 'GET', 'action' => '*'],

    /******************************** 投递task任务处理相关路由 ****************************/

    '/task/log/index'   => ['auth' => false, 'method' => 'Cli', 'action' => '*'],
    '/finish/log/index' => ['auth' => false, 'method' => 'Cli', 'action' => '/finish/flog/index'],

    /******************************** 连接完成关闭时close回调处理相关路由 ****************************/

    '/close/index/index' => ['auth' => false, 'method' => 'Cli', 'action' => '/close/index/index'],

    /******************************** 业务流程管理相关路由 ****************************/

    '/system/process/get_msg_list' => ['auth' => true, 'method' => 'GET', 'action' => '*'],
    '/system/process/set_msg' => ['auth' => true, 'method' => 'POST', 'action' => '*'],

];
