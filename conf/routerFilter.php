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
    '/login/test'               => ['auth' => false, 'method' => 'GET', 'action' => '/login/test'],
    '/login/set_redis'          => ['auth' => false, 'method' => 'GET', 'action' => '/login/setRedis'],
    '/login/get_redis'          => ['auth' => false, 'method' => 'GET', 'action' => '/login/getRedis'],
    '/login/get_user_list'      => ['auth' => false, 'method' => 'GET', 'action' => '/login/getUserList'],
    '/login/get_mongo_log_list' => ['auth' => false, 'method' => 'GET', 'action' => '/login/getMongoLogList'],
    '/login/get_log_user_list'  => ['auth' => false, 'method' => 'GET', 'action' => '/login/getLogUserList'],
    '/login/get_log_user_view'  => ['auth' => false, 'method' => 'GET', 'action' => '/login/getLogUserView'],
    '/login/get_user_info'      => ['auth' => false, 'method' => 'GET', 'action' => '/login/getUserInfo'],
    '/login/get_co_redis'       => ['auth' => false, 'method' => 'GET', 'action' => '/login/getCoRedis'],
    '/login/test_co'            => ['auth' => false, 'method' => 'GET', 'action' => '/login/testCo'],

    '/login/publisher_redis' => ['auth' => false, 'method' => 'GET', 'action' => '/login/publisherRedis'],
    '/login/consumer_redis'  => ['auth' => false, 'method' => 'GET', 'action' => '/login/consumerRedis'],
    '/login/ack_redis'       => ['auth' => false, 'method' => 'GET', 'action' => '/login/ackRedis'],
    '/login/del_redis'       => ['auth' => false, 'method' => 'GET', 'action' => '/loign/delRedis'],

    '/login/set_es' => ['auth' => false, 'method' => 'GET', 'action' => '/login/setEs'],
    '/login/get_es' => ['auth' => false, 'method' => 'GET', 'action' => '/login/getEs'],
    '/login/set_xs' => ['auth' => false, 'method' => 'GET', 'action' => '/login/setXs'],
    '/login/get_xs' => ['auth' => false, 'method' => 'GET', 'action' => '/login/getXs'],

    '/login/co_mysql' => ['auth' => false, 'method' => 'GET', 'action' => '/login/coMysql'],
    '/login/sw_mysql' => ['auth' => false, 'method' => 'GET', 'action' => '/login/swMysql'],
    '/login/sw_pgsql' => ['auth' => false, 'method' => 'GET', 'action' => '/login/swPgsql'],

    /***************************************** 用户设置相关路由 *****************************************/

    '/system/user/login'         => ['auth' => false, 'method' => 'POST', 'action' => '/system/user/login'],
    '/system/user/logout'        => ['auth' => true, 'method' => 'POST', 'action' => '/system/user/logout'],
    '/system/user/get_user'      => ['auth' => true, 'method' => 'GET', 'action' => '/system/user/getUser'],
    '/system/user/get_user_list' => ['auth' => true, 'method' => 'GET', 'action' => '/system/user/getUserList'],
    '/system/user/set_user'      => ['auth' => true, 'method' => 'POST', 'action' => '/system/user/setUser'],
    '/system/user/del_user'      => ['auth' => true, 'method' => 'DELETE', 'action' => '/system/user/delUser'],
    '/system/user/edit_user'     => ['auth' => true, 'method' => 'PUT', 'action' => '/system/user/editUser'],
    '/system/user/pull'          => ['auth' => true, 'method' => 'POST', 'action' => '/system/user/pull'],

    /***************************************** 系统菜单相关路由 *****************************************/

    '/system/menu/get_menu_info'    => ['auth' => true, 'method' => 'GET', 'action' => '/system/menu/getMenuInfo'],
    '/system/menu/get_menu_list'    => ['auth' => true, 'method' => 'GET', 'action' => '/system/menu/getMenuList'],
    '/system/v1/menu/get_menu_list' => ['auth' => true, 'method' => 'GET', 'action' => '/system/v1/menu/getMenuList'],
    '/system/v1/menu/get_menu_info' => ['auth' => true, 'method' => 'GET', 'action' => '/system/v1/menu/getMenuInfo'],
    '/system/v1/menu/get_redis'     => ['auth' => false, 'method' => 'GET', 'action' => '/system/v1/menu/getRedis'],

    '/system/menu/set_menu'      => ['auth' => true, 'method' => 'POST', 'action' => '/system/menu/setMenu'],
    '/system/menu/get_menu'      => ['auth' => true, 'method' => 'GET', 'action' => '/system/menu/getMenu'],
    '/system/menu/edit_menu'     => ['auth' => true, 'method' => 'PUT', 'action' => '/system/menu/editMenu'],
    '/system/menu/del_menu'      => ['auth' => true, 'method' => 'DELETE', 'action' => '/system/menu/delMenu'],
    '/system/router/get_list'    => ['auth' => true, 'method' => 'GET', 'action' => '/system/menu/getList'],
    '/system/router/set_router'  => ['auth' => true, 'method' => 'POST', 'action' => '/system/menu/setRouter'],
    '/system/router/del_router'  => ['auth' => true, 'method' => 'DELETE', 'action' => '/system/menu/delRouter'],
    '/system/router/edit_router' => ['auth' => true, 'method' => 'PUT', 'action' => '/system/menu/editRouter'],

    /***************************************** 首页服务状况相关路由 *************************************/

    '/system/server_status/get_status' => ['auth' => true, 'method' => 'GET', 'action' => '/system/serverStatus/getStatus'],

    /***************************************** 数据库备份相关路由 ***************************************/

    '/system/backup/index'    => ['auth' => true, 'method' => 'GET', 'action' => '/system/backup/index'],
    '/system/backup/add'      => ['auth' => true, 'method' => 'POST', 'action' => '/system/backup/add'],
    '/system/backup/get_list' => ['auth' => true, 'method' => 'GET', 'action' => '/system/backup/getList'],
    '/system/backup/down'     => ['auth' => true, 'method' => 'POST', 'action' => '/system/backup/down'],
    '/system/backup/del'      => ['auth' => true, 'method' => 'DELETE', 'action' => '/system/backup/del'],

    /***************************************** 日志中心相关路由 *****************************************/

    '/system/log_mongo/get_mongo_list' => ['auth' => true, 'method' => 'GET', 'action' => '/system/logMongo/getMongoList'],
    '/system/log_mongo/get_mongo_info' => ['auth' => true, 'method' => 'GET', 'action' => '/system/logMongo/getMongoInfo'],
    '/system/log_swoole/get_info'      => ['auth' => true, 'method' => 'GET', 'action' => '/system/logSwoole/getInfo'],
    '/system/log_swoole/clean_log'     => ['auth' => true, 'method' => 'POST', 'action' => '/system/logSwoole/cleanLog'],
    '/system/log_swoole/get_monolog'   => ['auth' => true, 'method' => 'GET', 'action' => '/system/logSwoole/getMonolog'],
    '/system/log_user/get_user_list'   => ['auth' => true, 'method' => 'GET', 'action' => '/system/logUser/getUserList'],

    /******************************** 投递task任务处理相关路由 ****************************/

    '/task/log/index'   => ['auth' => false, 'method' => 'Cli', 'action' => '/task/log/index'],
    '/finish/log/index' => ['auth' => false, 'method' => 'Cli', 'action' => '/finish/flog/index'],

    /******************************** 连接完成关闭时close回调处理相关路由 ****************************/

    '/close/index/index' => ['auth' => false, 'method' => 'Cli', 'action' => '/close/index/index'],

    /******************************** 业务流程管理相关路由 ****************************/

    '/system/process/get_msg_list' => ['auth' => true, 'method' => 'GET', 'action' => '/system/process/getMsgList'],
    '/system/process/set_msg'      => ['auth' => true, 'method' => 'POST', 'action' => '/system/process/setMsg'],

];
