<?php
/**
 * 开发程序时入口打开debug
 * User: hanhyu
 * Date: 18-10-11
 * Time: 上午10:15
 */

function stop(): void
{
    if (is_file(dirname(__FILE__) . '/log/swoolePid.log')) {
        $fp        = fopen(dirname(__FILE__) . '/log/swoolePid.log', "r");
        $masterPid = fgets($fp);
        fclose($fp);
        //使用swoole_process::kill代替posix_kill
        if (\Swoole\Process::kill($masterPid, 0)) {
            \Swoole\Process::kill($masterPid);
            $timeout   = 60;
            $startTime = time();
            echo "=================== stop  ===================" . PHP_EOL;
            while (true) {
                // Check the process status
                if (\Swoole\Process::kill($masterPid, 0)) {
                    // 判断是否超时
                    if (time() - $startTime >= $timeout) {
                        echo PHP_EOL . "================= fail  =================" . PHP_EOL;
                        return;
                    }
                    sleep(1);
                    echo '.';
                    continue;
                }
                echo PHP_EOL . "================== success  =================" . PHP_EOL;
                return;
            }
        }
    }
    echo "================= please start the first  =================" . PHP_EOL;
    return;
}

function reload(): void
{
    if (is_file(dirname(__FILE__) . '/log/swoolePid.log')) {
        $fp        = fopen(dirname(__FILE__) . '/log/swoolePid.log', "r");
        $masterPid = fgets($fp);
        fclose($fp);
        if (\Swoole\Process::kill($masterPid, 0)) {
            \Swoole\Process::kill($masterPid, SIGUSR1);
            echo "=================== reload  ===================" . PHP_EOL;
            sleep(1);
            echo PHP_EOL . "================== success  =================" . PHP_EOL;
            return;
        }
    }
    echo "================= please start the first  =================" . PHP_EOL;
    return;
}

function run(string $param = 'produce'): void
{
    if ($param == 'dev') {
        define('APP_DEBUG', TRUE);
    } else {
        define('APP_DEBUG', FALSE);
    }

    //使用error_reporting来定义哪些级别错误可以触发 -1
    error_reporting(E_ALL);

    if (APP_DEBUG) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        ini_set('log_errors', 1);
        //'SWOOLE_LOG_DEBUG | SWOOLE_LOG_TRACE | SWOOLE_LOG_INFO | SWOOLE_LOG_NOTICE | SWOOLE_LOG_WARNING | SWOOLE_LOG_ERROR'
        define('SWOOLE_LOG_LEVEL', 2);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        ini_set('log_errors', 1);
        define('SWOOLE_LOG_LEVEL', 5);
    }

    //每个PHP进程所吃掉的最大内存
    ini_set('memory_limit', '2048M');

    date_default_timezone_set('Asia/Shanghai');

    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT_PATH', dirname(__FILE__));
    define('CONF_PATH', ROOT_PATH . DS . 'conf');
    define('APPLICATION_PATH', ROOT_PATH . DS . 'application');
    define('LIBRARY_PATH', APPLICATION_PATH . DS . 'library');

    require LIBRARY_PATH . DS . 'Server.php';

    $serverObj = Server::getInstance();
    $serverObj->start();
}

function explain(): void
{
    echo <<<EOT
        Usage:
          php swoole.php {command} [arguments ...] [options ...]
        
        Commands:
          start     Start Swoole Server
          stop      Stop Swoole Server
          reload    Reload Swoole Worker Process
        
        Start Options:
          dev   Start Debug
          -d    Start Daemonize HTTP Server
    EOT;
    echo PHP_EOL;
    return;
}

function start(array &$argv): void
{
    $daemonize = false;
    $param     = 'produce';
    if (isset($argv[2])) {
        if ($argv[2] == '-d') {
            $daemonize = true;
        } elseif ($argv[2] == 'dev') {
            $param = 'dev';
        } else {
            explain();
            return;
        }
    }
    if (isset($argv[3]) AND $argv[2] != '-d') {
        if ($argv[3] == '-d') {
            $daemonize = true;
        } else {
            explain();
            return;
        }
    }

    define('SWOOLE_DAEMONIZE', $daemonize);
    run($param);
}

/**
 * 判断输入的参数指令
 */
if (isset($argv[1])) {
    $let = $argv;
    switch ($argv[1]) {
        case 'start':
            start($argv);
            break;
        case 'stop':
            stop();
            break;
        case 'reload':
            reload();
            break;
        default:
            explain();
            break;
    }
    return;
}

explain();


