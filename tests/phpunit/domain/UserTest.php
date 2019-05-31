<?php
declare(strict_types=1);

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
        $user = new User();
        $res  = $user->getInfoList($data);
        $this->assertNotEmpty($res);
        //print_r($res);
    }


}
