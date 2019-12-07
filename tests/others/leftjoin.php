<?php
/**
 * 使用php数组实现left join功能
 * 需求：优化两个foreach循环，使用多种算法实现。
 * UserDomain: hanhyu
 * Date: 19-1-15
 * Time: 下午5:20
 */
$arr1 = [
    ["id" => "104", "u_id" => "1"],
    ["id" => "103", "u_id" => "18"],
    ["id" => "102", "u_id" => "1"],
    ["id" => "101", "u_id" => "1"],
];
$arr2 = [
    ["u_id" => "1", "name" => "a"],
    ["u_id" => "18", "name" => "b"],
];

/*
 * 10万次 0.1007微秒
 *
$t1 = microtime(true);
for ($j = 0; $j < 100000; $j++) {
    foreach ($arr1 as $k => $v) {
        foreach ($arr2 as $key => $value) {
            if ($v['u_id'] == $value['u_id']) $arr1[$k]['name'] = $value['name'];
        }
    }
}
$t2 = microtime(true);
echo $t2 - $t1;
*/

/*
 * 10万次 0.0495微秒
 *
$t1 = microtime(true);
$new = array_column($arr2, 'name', 'u_id');
for ($j = 0; $j < 100000; $j++) {
    foreach ($arr1 as $k => $v) {
        $arr1[$k]['name'] = $new[$v['u_id']];
    }
}
$t2 = microtime(true);
echo $t2 - $t1;
*/

/*$chan = new Swoole\Coroutine\Channel(10);

foreach ($arr1 as $k => $v) {
    go(function () use ($arr1, $k, $v, $arr2, $chan) {
        foreach ($arr2 as $key => $value) {
            if ($v['u_id'] == $value['u_id']) $arr1[$k]['name'] = $value['name'];
        }
        $chan->push([$k => $arr1[$k]]);
    });
}

go(function () use ($arr1, $chan) {
    $count = count($arr1);
    $let = [];
    for ($i = 0; $i < $count; $i++) {
        $let += $chan->pop();
    }
    echo(var_export($let));
});*/


//echo(var_export($arr1));
/*输出：
 * [
    ['id' => '104', 'u_id' => '1', 'name' => 'a'],
    ['id' => '103', 'u_id' => '18', 'name' => 'b'],
    ['id' => '102', 'u_id' => '1', 'name' => 'a'],
    ['id' => '101', 'u_id' => '1', 'name' => 'a'],
];*/
