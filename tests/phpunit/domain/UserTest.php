<?php
declare(strict_types=1);

namespace Test\phpunit\domain;

use Tests\BootstrapTest;
use App\Domain\System\User;

/**
 * @covers User
 */
final class UserTest extends BootstrapTest
{

    public function testGetInfoList(): void
    {
        $data = [
            'curr_page' => 1,
            'page_size' => 1,
        ];

        go(function () use ($data) {
            $user = new User();
            $res  = $user->getInfoList($data);

            print_r($res);

            $this->assertNotEmpty($res);
        });
    }


}
