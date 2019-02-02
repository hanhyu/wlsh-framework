<?php

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-23
 * Time: 下午5:34
 */
class CsCall
{
    public function __call($name, $arguments)
    {
        $data = call_user_func_array([$this, $name], $arguments);
        return $data;
    }
}

class Test1 extends CsCall
{
    protected function sum1(int $a, int $b): int
    {
        return $a + $b;
    }

    public function sum2(int $a, int $b): int
    {
        return $a + $b;
    }
}

$obj = new Test1();
echo $obj->sum2(2,5) . PHP_EOL;
echo $obj->sum1(2,3);