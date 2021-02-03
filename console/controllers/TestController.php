<?php declare(strict_types=1);

namespace Console\Controllers;

use App\Domain\System\UserDomain;
use App\Models\Mongo\MonologMongo;
use SebastianBergmann\CodeCoverage\Report\PHP;

class TestController
{
    public function addAction($a, $b): int
    {
        return $a + $b;
    }

    public function getInfoAction(): array
    {
        return (new UserDomain)->getInfoByName('ceshi123');
    }

    public function getAction($c): void
    {
    }

    public function checkMemoryLeakAction(): void
    {
        while (1) {
            MonologMongo::getInstance()->getMongoList(['where' => [], 'curr_data' => 1, 'page_size' => 10]);
            echo time();
            usleep(300);
        }
    }
}
