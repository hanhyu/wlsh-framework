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

    /**
     * @router auth=false&method=cli
     */
    public function IndexAction(): void
    {
        /*$content = "onFinish content:{$this->data['content']},info:{$this->data['info']},;level:{ $this->data['level']}";
        $fp      = fopen(ROOT_PATH . '/log/swoole.log', 'ab+');
        fwrite($fp, $content);
        fclose($fp);*/
    }

}
