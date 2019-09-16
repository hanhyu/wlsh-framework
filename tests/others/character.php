<?php
declare(strict_types=1);

function reverse(string $str): string
{
    $r = strlen($str) - 1;
    $l = 0;
    while ($l < $r) {
        if (!ctype_alpha($str[$l])) {
            $l++;
        } else if (!ctype_alpha($str[$r])) {
            $r--;
        } else {
            $tmp     = $str[$l];
            $str[$l] = $str[$r];
            $str[$r] = $tmp;
            $l++;
            $r--;
        }
    }
    return $str;
}

$str  = 'a,b$c';
$str1 = 'Ab,c,de!$';

echo reverse($str) . PHP_EOL;
echo reverse($str1);