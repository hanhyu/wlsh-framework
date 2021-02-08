<?php declare(strict_types=1);


namespace Tests\phpunit\domain;

use App\Domain\System\LogDomain;
use App\Models\Mysql\RouterLogMysql;
use PHPUnit\Framework\TestCase;

//php phpunit.php phpunit/domain/LogTest.php
final class LogTest extends TestCase
{
    public function testGetMongoById(): void
    {
        $id  = '6017dd259fb5573e224e3152';
        $res = (new LogDomain())->getMongoById($id);
        self::assertEquals($id, $res->_id);
    }

    public function testGetRouterLog(): void
    {
        $res = RouterLogMysql::getInstance()->getList();
        self::assertIsArray($res);
    }

}
