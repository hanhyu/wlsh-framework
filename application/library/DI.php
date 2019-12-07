<?php
declare(strict_types=1);

namespace App\Library;

class DI
{
    static protected array $arr;

    /**
     * User: hanhyu
     * Date: 2019/12/4
     * Time: 下午10:07
     *
     * @param string $name
     * @param mixed  $obj
     */
    public static function set(string $name, $obj): void
    {
        //todo 对象序列化存储
        self::$arr[$name] = $obj;
    }

    /**
     * User: hanhyu
     * Date: 2019/12/4
     * Time: 下午10:09
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function get(string $name)
    {
        return self::$arr[$name] ?? '';
    }

    /**
     * User: hanhyu
     * Date: 2019/12/4
     * Time: 下午10:09
     *
     * @param string $name
     */
    public static function del(string $name): void
    {
        unset(self::$arr[$name]);
    }

}
