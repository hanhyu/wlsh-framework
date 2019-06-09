<?php

use Yaf\Dispatcher;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-7-25
 * Time: 上午10:17
 */
class Bootstrap extends Yaf\Bootstrap_Abstract
{
    private $config;

    public function _initLoader()
    {
        // Yaf\Loader::import(ROOT_PATH . '/vendor/autoload.php');
        Yaf\Loader::import(LIBRARY_PATH . '/PdoPool');
        Yaf\Loader::import(LIBRARY_PATH . '/RedisPool.php');
        Yaf\Loader::import(LIBRARY_PATH . '/CoMysqlPool.php');
        Yaf\Loader::import(LIBRARY_PATH . '/Language.php');
    }

    public function _initConfig(Dispatcher $dispatcher)
    {
        //关闭视图渲染
        $dispatcher->disableView();

        //把配置保存起来
        $this->config = Yaf\Application::app()->getConfig();
        Yaf\Registry::set('config', $this->config);

        //new Yaf\Config\Ini();
        //添加路由过滤配置
        $router_filter = include CONF_PATH . '/routerFilter.php';
        Yaf\Registry::set('router_filter_config', new Yaf\Config\Simple($router_filter));

        //发送邮件配置
        $email = include CONF_PATH . '/sendEmail.php';
        Yaf\Registry::set('email_config', new Yaf\Config\Simple($email));

        //添加redis连接池
        $redis_pool = new RedisPool();
        Yaf\Registry::set('redis_pool', $redis_pool);

        //添加mysql数据库连接池
        $mysql_pool = new PdoPool('mysql');
        Yaf\Registry::set('mysql_pool', $mysql_pool);


        //如需主从、读写库请在这里自行配置添加
        //$mysql_master = new PdoPool('mysql_master');
        //$mysql_slave = new PdoPool('mysql_slave');

        //添加pgsql数据库连接池
        $pgsql_pool = new PdoPool('pgsql');
        Yaf\Registry::set('pgsql_pool', $pgsql_pool);

        //添加协程mysql数据库连接池
        $co_mysql_pool = new CoMysqlPool();
        Yaf\Registry::set('co_mysql_pool', $co_mysql_pool);
    }

    public function _initPlugin(Dispatcher $dispatcher)
    {
        //注册一个自定义路由插件
        $user_init_plugin = new \App\Plugins\UserInitPlugin();
        $dispatcher->registerPlugin($user_init_plugin);
    }

    public function _initRoute(Dispatcher $dispatcher)
    {
        //在这里注册自己的路由协议,默认使用简单路由
        $router = $dispatcher::getInstance()->getRouter();
        $route  = new Yaf\Route\Supervar("r");
        $router->addRoute("name", $route);
    }

    /**
     * LocalName
     */
    public function _initLocalName()
    {
        //申明, 凡是以Foo和Local开头的类, 都是本地类
        //$loader = Yaf_Loader::getIgnstance();
        //$loader->registerLocalNamespace(array("Foo", "Local"));
    }

    /**
     * Twig View
     *
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initTwig(Dispatcher $dispatcher)
    {
        //$twig = new Twig\Adapter(ROOT_PATH . "/application/views/", $this->config->get("twig")->toArray());
        //$dispatcher::getInstance()->setView($twig);
    }

    public function _initCache(Dispatcher $dispatcher)
    {

    }

}
