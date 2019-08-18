<?php
declare(strict_types=1);

/*
使用 foreach 直接对数组操作 : 0.00044894218444824
使用 foreach 调用函数对数组操作 : 0.00089502334594727
使用 array_walk 花费 : 0.0010051727294922
*/


//产生一个10000的一个数组。
$max      = 10000;
$test_arr = range(0, $max);
//我们分别用三种方法测试求这些数加上1的值的时间。

$t1 = microtime(true);
foreach ($test_arr as $k => &$v) {
    $v = $v + 1;
}
$t2 = microtime(true);
$t  = $t2 - $t1;
echo "使用 foreach 直接对数组操作 : {$t}\n";

$t1 = microtime(true);
foreach ($test_arr as $k => &$v) {
    addOne($v);
}
$t2 = microtime(true);
$t  = $t2 - $t1;
echo "使用 foreach 调用函数对数组操作 : {$t}\n";

$t1 = microtime(true);
array_walk($test_arr, 'addOne');
$t2 = microtime(true);
$t  = $t2 - $t1;
echo "使用 array_walk 花费 : {$t}\n";

function addOne(&$item)
{
    $item = $item + 1;
}
