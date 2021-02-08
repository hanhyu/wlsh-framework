<?php
declare(strict_types=1);

namespace Test\phpunit\domain;

use App\Models\Mysql\SystemUserMysql;
use PHPUnit\Framework\TestCase;
use App\Domain\System\UserDomain;
use function MongoDB\select_server;

/**
 * @covers UserDomain
 */
final class UserTest extends TestCase
{

    public function testGetInfoList(): void
    {
        $data = [
            'curr_page' => 1,
            'page_size' => 1,
        ];

        $user = new UserDomain();
        $res  = $user->getInfoList($data);
        //print_r($res);
        self::assertNotEmpty($res);
    }

    public function testGetInfo(): void
    {
        $name = 'ceshi001';
        $res  = SystemUserMysql::getInstance()->getInfo($name);

        print_r($res);
        self::assertIsArray($res);
    }

    //php phpunit.php phpunit/domain/UserTest.php --filter GetNameById
    public function testGetNameById(): void
    {
        $uid = [1];
        $res = SystemUserMysql::getInstance()->getNameById($uid);
        self::assertEquals(1, $res[0]['id']);
    }

    //php phpunit.php phpunit/domain/UserTest.php --filter NameById
    public function testNameById(): void
    {
        $uid = 101;
        $res = SystemUserMysql::getInstance()->testNameById($uid);
        $res = $res !== false ? $res['name'] : '101';

        self::assertEquals('ceshi001', $res);
    }


}
