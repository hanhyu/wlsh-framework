<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-11-13
 * Time: 下午6:16
 */
$arr = [
    ['count'=>12],
    ['list'=>['a'=>1]]
];

list($res['count'], $res['list']) = array_map('current', $arr);

echo json_encode($arr) . PHP_EOL;
echo json_encode($res) . PHP_EOL;

/*[{"count":12},{"list":{"a":1}}]
{"count":12,"list":{"a":1}}*/
