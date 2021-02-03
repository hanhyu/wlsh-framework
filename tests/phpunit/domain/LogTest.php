<?php declare(strict_types=1);


namespace Tests\phpunit\domain;


use App\Domain\System\LogDomain;
use Tests\BootstrapTest;

//php phpunit.php phpunit/domain/LogTest.php
final class LogTest extends BootstrapTest
{
    public function testGetMongoById(): void
    {
        $id  = '6017dd259fb5573e224e3152';
        $res = (new LogDomain())->getMongoById($id);
        self::assertEquals($id, $res->_id);
    }

}