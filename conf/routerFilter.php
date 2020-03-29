<?php
/**
 * 允许外部http访问的方法，格式：key=>value
 * key是请求的uri，value是转发的路由相关参数组
 *
 * value参数说明：
 * auth值是需要使用authorization进行token认证的路由，false不需要，true需要
 * method值是请求的http方法
 * rate-limit值代表该接口服务限流参数
 * circuit-breaker值代表该接口服务超时熔断参数
 *
 * 注意：uri结尾不能有/字符
 *
 * 可以按不同项目、团队、需求、个人喜好等对uri增加加密key
 */
return [
    /***************************************** 测试服务相关路由 *****************************************/
    '/login/test'            => ['auth' => false, 'method' => 'GET',],
    '/login/setRedis'        => ['auth' => false, 'method' => 'GET',],
    '/login/getRedis'        => ['auth' => false, 'method' => 'GET',],
    '/login/getUserList'     => ['auth' => false, 'method' => 'GET',],
    '/login/getMongoLogList' => ['auth' => false, 'method' => 'GET',],
    '/login/getLogUserList'  => ['auth' => false, 'method' => 'GET',],
    '/login/getLogUserView'  => ['auth' => false, 'method' => 'GET',],
    '/login/getUserInfo'     => ['auth' => false, 'method' => 'GET',],
    '/login/getCoRedis'      => ['auth' => false, 'method' => 'GET',],
    '/login/testCo'          => ['auth' => false, 'method' => 'GET',],

    '/login/publisherRedis' => ['auth' => false, 'method' => 'GET',],
    '/login/consumerRedis'  => ['auth' => false, 'method' => 'GET',],
    '/login/ackRedis'       => ['auth' => false, 'method' => 'GET',],
    '/login/delRedis'       => ['auth' => false, 'method' => 'GET',],

    '/login/setEs' => ['auth' => false, 'method' => 'GET',],
    '/login/getEs' => ['auth' => false, 'method' => 'GET',],
    '/login/setXs' => ['auth' => false, 'method' => 'GET',],
    '/login/getXs' => ['auth' => false, 'method' => 'GET',],

    '/login/coMysql' => ['auth' => false, 'method' => 'GET',],
    '/login/swMysql' => ['auth' => false, 'method' => 'GET',],
    '/login/swPgsql' => ['auth' => false, 'method' => 'GET',],

    '/im/getInfo' => ['auth' => false, 'method' => 'Cli',],

    /***************************************** 用户设置相关路由 *****************************************/

    '/system/user/login'       => ['auth' => false, 'method' => 'POST',],
    '/system/user/logout'      => ['auth' => true, 'method' => 'POST',],
    '/system/user/getUser'     => ['auth' => true, 'method' => 'GET',],
    '/system/user/getUserList' => ['auth' => true, 'method' => 'GET',],
    '/system/user/setUser'     => ['auth' => true, 'method' => 'POST',],
    '/system/user/delUser'     => ['auth' => true, 'method' => 'DELETE',],
    '/system/user/editUser'    => ['auth' => true, 'method' => 'PUT',],
    '/system/user/pull'        => ['auth' => true, 'method' => 'POST',],
    '/system/user/editPwd'     => ['auth' => true, 'method' => 'POST',],

    /***************************************** 系统菜单相关路由 *****************************************/

    '/system/menu/getMenuInfo'    => ['auth' => true, 'method' => 'GET',],
    '/system/menu/getMenuList'    => ['auth' => true, 'method' => 'GET',],
    '/system/v1/menu/getMenuList' => ['auth' => true, 'method' => 'GET',],
    '/system/v1/menu/getMenuInfo' => ['auth' => true, 'method' => 'GET',],
    '/system/v1/menu/getRedis'    => ['auth' => false, 'method' => 'GET',],

    '/system/menu/setMenu'      => ['auth' => true, 'method' => 'POST',],
    '/system/menu/getMenu'      => ['auth' => true, 'method' => 'GET',],
    '/system/menu/editMenu'     => ['auth' => true, 'method' => 'PUT',],
    '/system/menu/delMenu'      => ['auth' => true, 'method' => 'DELETE',],
    '/system/router/getList'    => ['auth' => true, 'method' => 'GET',],
    '/system/router/setRouter'  => ['auth' => true, 'method' => 'POST',],
    '/system/router/delRouter'  => ['auth' => true, 'method' => 'DELETE',],
    '/system/router/editRouter' => ['auth' => true, 'method' => 'PUT',],

    /***************************************** 首页服务状况相关路由 *************************************/

    '/system/serverStatus/getStatus' => ['auth' => true, 'method' => 'GET',],

    /***************************************** 数据库备份相关路由 ***************************************/

    '/system/backup/index'   => ['auth' => true, 'method' => 'GET',],
    '/system/backup/add'     => ['auth' => true, 'method' => 'POST',],
    '/system/backup/getList' => ['auth' => true, 'method' => 'GET',],
    '/system/backup/down'    => ['auth' => true, 'method' => 'POST',],
    '/system/backup/del'     => ['auth' => true, 'method' => 'DELETE',],

    /***************************************** 日志中心相关路由 *****************************************/

    '/system/logMongo/getMongoList' => ['auth' => true, 'method' => 'GET',],
    '/system/logMongo/getMongoInfo' => ['auth' => true, 'method' => 'GET',],
    '/system/logSwoole/getInfo'     => ['auth' => true, 'method' => 'GET',],
    '/system/logSwoole/cleanLog'    => ['auth' => true, 'method' => 'POST',],
    '/system/logSwoole/getMonolog'  => ['auth' => true, 'method' => 'GET',],
    '/system/logUser/getUserList'   => ['auth' => true, 'method' => 'GET',],

    /******************************** 业务流程管理相关路由 ****************************/

    '/system/process/getMsgList' => ['auth' => true, 'method' => 'GET',],
    '/system/process/setMsg'     => ['auth' => true, 'method' => 'POST',],

];
