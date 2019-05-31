<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-11-14
 * Time: 上午9:35
 */
$items = array(1,2,3,4,5);

foreach($items as $v) echo $v;

echo PHP_EOL;

$items[] = array_shift($items);
foreach($items as $v) echo $v;


