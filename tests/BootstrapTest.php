<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Library\DI;

class BootstrapTest extends TestCase
{
    public function setUp(): void
    {
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

        require ROOT_PATH . '/vendor/autoload.php';
        require LIBRARY_PATH . '/common/functions.php';

        DI::set('config_arr', array_merge(
            require CONF_PATH . DS . 'common.php',
            require CONF_PATH . DS . 'local.php'
        ));

        require CONF_PATH . DS . 'di.php';
    }

}
