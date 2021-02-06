<?php
declare(strict_types=1);

namespace App\Modules\Finish\Controllers;


use App\Library\FinishTrait;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-1-14
 * Time: 下午8:33
 */
class FlogController
{
    use FinishTrait;

    public function __construct()
    {
        $this->beforeInit();
    }

    #[Router(method: 'Cli', auth: false)]
    public function IndexAction(): void
    {
    }

}
