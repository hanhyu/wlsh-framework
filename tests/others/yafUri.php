<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-11-1
 * Time: 下午6:23
 */

$uri = '/api/login/getInfo';

$arr = explode('/', strtolower($uri));

var_dump($arr);

echo $arr[2];

