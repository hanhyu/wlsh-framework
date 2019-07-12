<?php
declare(strict_types=1);
$a         = 123;
$scheduler = new Swoole\Coroutine\Scheduler;
$scheduler->add(function ($a) {
    Co::sleep(1);
    echo $a;
}, $a);
$scheduler->start();

/*   Co\run(function () {
        \Co::sleep(1);
        echo "Done.\n";
    });*/
