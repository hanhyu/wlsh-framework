<?php declare(strict_types=1);

namespace App\Library;

use Envms\FluentPDO\Query;
use Swoole\Coroutine;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-10-28
 * Time: 下午3:34
 */
abstract class AbstractPdo
{
    private static array $instance = [];

    public static function getInstance(): static
    {
        $_class_name = static::class;
        $_cid        = Coroutine::getCid();
        if (!isset(static::$instance[$_class_name][$_cid])) {
            //new static()与new static::class一样，但为了IDE友好提示类中的方法，需要用new static()
            $_instance = static::$instance[$_class_name][$_cid] = new static();
        } else {
            $_instance = static::$instance[$_class_name][$_cid];
        }

        defer(static function () use ($_class_name, $_cid) {
            unset(static::$instance[$_class_name][$_cid]);
        });

        //为了IDE代码提示功能
        return $_instance;
    }

    private function __construct()
    {
    }

    /**
     * 使用单例可以最大化在协程内利用pdo连接池对象
     *
     * User: hanhyu
     * Date: 2021/1/30
     * Time: 上午10:19
     *
     * @return Query
     * @throws ProgramException
     */
    public static function getDb(): Query
    {
        $_class_name = static::class;
        $_cid        = Coroutine::getCid();
        if (!isset(static::$instance[$_class_name]['pdo'][$_cid])) {
            /** @var $_pool_obj PdoPool */
            $_pool_obj = DI::get(static::getPool());
            if (!$_pool_obj->available) {
                throw new ProgramException('服务正在重启中，请稍候重试', 500);
            }
            $_instance = static::$instance[$_class_name]['pdo'][$_cid] = new Query($_pool_obj->get());
        } else {
            $_instance = static::$instance[$_class_name]['pdo'][$_cid];
        }

        defer(static function () use ($_class_name, $_cid) {
            unset(static::$instance[$_class_name]['pdo'][$_cid]);
        });

        return $_instance;
    }

}
