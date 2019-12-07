<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    /**
     * setUp
     */
    public function setUp()
    {
        $this->__setUpYafApplication();
    }

    /**
     * setup yaf
     */
    private function __setUpYafApplication()
    {
        $this->__setUpPHPIniVariables();
        // Import application and bootstrap.

        define('APP_DEBUG', TRUE);
        //使用error_reporting来定义哪些级别错误可以触发 -1
        error_reporting(E_ALL);

        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        ini_set('log_errors', '1');
        define('SWOOLE_LOG_LEVEL', 2);
        //每个PHP进程所吃掉的最大内存
        ini_set('memory_limit', '2048M');

        date_default_timezone_set('Asia/Shanghai');

        define('DS', DIRECTORY_SEPARATOR);
        define('ROOT_PATH', dirname(getcwd() . '../'));
        define('CONF_PATH', ROOT_PATH . DS . 'conf');
        define('APP_PATH', ROOT_PATH . DS . 'application');
        define('LIBRARY_PATH', APP_PATH . DS . 'library');

        \Yaf\Loader::import(ROOT_PATH . '/vendor/autoload.php');
        \Yaf\Loader::import(LIBRARY_PATH . '/common/functions.php');

        $common  = require CONF_PATH . DS . 'common.php';
        $develop = require CONF_PATH . DS . 'develop.php';
        $test    = require CONF_PATH . DS . 'test.php';
        $devtest = require CONF_PATH . DS . 'devtest.php';
        $product = require CONF_PATH . DS . 'product.php';

        $yaf_conf = ini_get('yaf.environ');

        try {
            $obj_yaf = new \Yaf\Application(array_merge($common, $$yaf_conf));
            go(function () use ($obj_yaf) {
                $obj_yaf->bootstrap()->run();
            });
        } catch (\Yaf\Exception $e) {
            var_dump($e->getMessage());
        }

    }

    /**
     * setup php ini variables
     */
    private function __setUpPHPIniVariables()
    {

    }


}
