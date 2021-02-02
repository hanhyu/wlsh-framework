<?php declare(strict_types=1);

namespace Console\Controllers;

use App\Domain\System\UserDomain;

class TestController
{
    public function getInfoAction($a, $b)
    {
        return $a + $b;
    }

    public function getListAction()
    {
        return (new UserDomain)->getInfoByName('ceshi123');
    }

    public function getAction($c)
    {
    }
}
