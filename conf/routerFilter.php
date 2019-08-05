<?php
/**
 * 允许外部http访问的方法，格式：key=>value
 * key是请求的uri，value是转发的路由相关参数组
 *
 * value参数说明：
 * auth值是需要使用authorization进行token认证的路由，false不需要，true需要
 * method值是请求的http方法
 * action值代表转发指定的路由，指定格式：/modules/controller/action
 *
 * 注意：uri与action结尾不能有/字符
 */
return [
    /***************************************** 测试服务相关路由 *****************************************/
    '/login/test'               => ['auth' => false, 'method' => 'GET', 'action' => '/Login/test'],
    '/login/set_redis'          => ['auth' => false, 'method' => 'GET', 'action' => '/Login/setRedis'],
    '/login/get_redis'          => ['auth' => false, 'method' => 'GET', 'action' => '/Login/getRedis'],
    '/login/get_user_list'      => ['auth' => false, 'method' => 'GET', 'action' => '/Login/getUserList'],
    '/login/get_mongo_log_list' => ['auth' => false, 'method' => 'GET', 'action' => '/Login/getMongoLogList'],
    '/login/get_log_user_list'  => ['auth' => false, 'method' => 'GET', 'action' => '/Login/getLogUserList'],
    '/login/get_log_user_view'  => ['auth' => false, 'method' => 'GET', 'action' => '/Login/getLogUserView'],
    '/login/get_user_info'      => ['auth' => false, 'method' => 'GET', 'action' => '/Login/getUserInfo'],
    '/login/get_co_redis'       => ['auth' => false, 'method' => 'GET', 'action' => '/Login/getCoRedis'],
    '/login/test_co'            => ['auth' => false, 'method' => 'GET', 'action' => '/Login/testCo'],

    '/login/publisher_redis' => ['auth' => false, 'method' => 'GET', 'action' => '/Login/publisherRedis'],
    '/login/consumer_redis'  => ['auth' => false, 'method' => 'GET', 'action' => '/Login/consumerRedis'],
    '/login/ack_redis'       => ['auth' => false, 'method' => 'GET', 'action' => '/Login/ackRedis'],
    '/login/del_redis'       => ['auth' => false, 'method' => 'GET', 'action' => '/Login/delRedis'],

    '/login/set_es' => ['auth' => false, 'method' => 'GET', 'action' => '/Login/setEs'],
    '/login/get_es' => ['auth' => false, 'method' => 'GET', 'action' => '/Login/getEs'],
    '/login/set_xs' => ['auth' => false, 'method' => 'GET', 'action' => '/Login/setXs'],
    '/login/get_xs' => ['auth' => false, 'method' => 'GET', 'action' => '/Login/getXs'],

    '/login/co_mysql' => ['auth' => false, 'method' => 'GET', 'action' => '/Login/coMysql'],
    '/login/sw_mysql' => ['auth' => false, 'method' => 'GET', 'action' => '/Login/swMysql'],
    '/login/sw_pgsql' => ['auth' => false, 'method' => 'GET', 'action' => '/Login/swPgsql'],

    /***************************************** 用户设置相关路由 *****************************************/

    '/system/user/login'         => ['auth' => false, 'method' => 'POST', 'action' => '/system/User/login'],
    '/system/user/logout'        => ['auth' => true, 'method' => 'POST', 'action' => '/system/User/logout'],
    '/system/user/get_user'      => ['auth' => true, 'method' => 'GET', 'action' => '/system/User/getUser'],
    '/system/user/get_user_list' => ['auth' => true, 'method' => 'GET', 'action' => '/system/User/getUserList'],
    '/system/user/set_user'      => ['auth' => true, 'method' => 'POST', 'action' => '/system/User/setUser'],
    '/system/user/del_user'      => ['auth' => true, 'method' => 'DELETE', 'action' => '/system/User/delUser'],
    '/system/user/edit_user'     => ['auth' => true, 'method' => 'PUT', 'action' => '/system/User/editUser'],
    '/system/user/pull'          => ['auth' => true, 'method' => 'POST', 'action' => '/system/User/pull'],

    /***************************************** 系统菜单相关路由 *****************************************/

    '/system/menu/get_menu_info'    => ['auth' => true, 'method' => 'GET', 'action' => '/system/Menu/getMenuInfo'],
    '/system/menu/get_menu_list'    => ['auth' => true, 'method' => 'GET', 'action' => '/system/Menu/getMenuList'],
    '/system/v1/menu/get_menu_list' => ['auth' => true, 'method' => 'GET', 'action' => '/system/v1/Menu/getMenuList'],
    '/system/v1/menu/get_menu_info' => ['auth' => true, 'method' => 'GET', 'action' => '/system/v1/Menu/getMenuInfo'],
    '/system/v1/menu/get_redis'     => ['auth' => false, 'method' => 'GET', 'action' => '/system/v1/Menu/getRedis'],

    '/system/menu/set_menu'      => ['auth' => true, 'method' => 'POST', 'action' => '/system/Menu/setMenu'],
    '/system/menu/get_menu'      => ['auth' => true, 'method' => 'GET', 'action' => '/system/Menu/getMenu'],
    '/system/menu/edit_menu'     => ['auth' => true, 'method' => 'PUT', 'action' => '/system/Menu/editMenu'],
    '/system/menu/del_menu'      => ['auth' => true, 'method' => 'DELETE', 'action' => '/system/Menu/delMenu'],
    '/system/router/get_list'    => ['auth' => true, 'method' => 'GET', 'action' => '/system/Router/getList'],
    '/system/router/set_router'  => ['auth' => true, 'method' => 'POST', 'action' => '/system/Router/setRouter'],
    '/system/router/del_router'  => ['auth' => true, 'method' => 'DELETE', 'action' => '/system/Router/delRouter'],
    '/system/router/edit_router' => ['auth' => true, 'method' => 'PUT', 'action' => '/system/Router/editRouter'],

    /***************************************** 首页服务状况相关路由 *************************************/

    '/system/server_status/get_status' => ['auth' => true, 'method' => 'GET', 'action' => '/system/ServerStatus/getStatus'],

    /***************************************** 数据库备份相关路由 ***************************************/

    '/system/backup/index'    => ['auth' => true, 'method' => 'GET', 'action' => '/system/Backup/index'],
    '/system/backup/add'      => ['auth' => true, 'method' => 'POST', 'action' => '/system/Backup/add'],
    '/system/backup/get_list' => ['auth' => true, 'method' => 'GET', 'action' => '/system/Backup/getList'],
    '/system/backup/down'     => ['auth' => true, 'method' => 'POST', 'action' => '/system/Backup/down'],
    '/system/backup/del'      => ['auth' => true, 'method' => 'DELETE', 'action' => '/system/Backup/del'],

    /***************************************** 日志中心相关路由 *****************************************/

    '/system/log_mongo/get_mongo_list' => ['auth' => true, 'method' => 'GET', 'action' => '/system/LogMongo/getMongoList'],
    '/system/log_mongo/get_mongo_info' => ['auth' => true, 'method' => 'GET', 'action' => '/system/LogMongo/getMongoInfo'],
    '/system/log_swoole/get_info'      => ['auth' => true, 'method' => 'GET', 'action' => '/system/LogSwoole/getInfo'],
    '/system/log_swoole/clean_log'     => ['auth' => true, 'method' => 'POST', 'action' => '/system/LogSwoole/cleanLog'],
    '/system/log_swoole/get_monolog'   => ['auth' => true, 'method' => 'GET', 'action' => '/system/LogSwoole/getMonolog'],
    '/system/log_user/get_user_list'   => ['auth' => true, 'method' => 'GET', 'action' => '/system/LogUser/getUserList'],

    /******************************** 投递task任务处理相关路由 ****************************/

    '/task/log/index'   => ['auth' => false, 'method' => 'Cli', 'action' => '/task/Log/index'],
    '/finish/log/index' => ['auth' => false, 'method' => 'Cli', 'action' => '/finish/Flog/index'],

    /******************************** 连接完成关闭时close回调处理相关路由 ****************************/

    '/close/index/index' => ['auth' => false, 'method' => 'Cli', 'action' => '/close/Index/index'],

    /******************************** 业务流程管理相关路由 ****************************/

    '/system/process/get_msg_list' => ['auth' => true, 'method' => 'GET', 'action' => '/system/Process/getMsgList'],
    '/system/process/set_msg'      => ['auth' => true, 'method' => 'POST', 'action' => '/system/Process/setMsg'],

];
