<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-11-12
 * Time: 下午9:47
 */
function is_mobile(string $text):bool
{
    $search = '/^0?1[3|4|5|6|7|8|9][0-9]\d{8}$/';
    if (preg_match($search, $text)) {
        return true;
    } else {
        return false;
    }
}

echo is_mobile('18957355303');
