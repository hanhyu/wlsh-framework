<?php
declare(strict_types=1);

namespace App\Modules\Task\Controllers;

use App\Library\TaskTrait;

/**
 * 操作日志类
 * UserDomain: hanhyu
 * Date: 18-10-22
 * Time: 下午5:00
 */
class Log
{
    use TaskTrait;

    public function __construct()
    {
        $this->beforeInit();
    }

    public function IndexAction(): void
    {
        co_log($this->data['content'], $this->data['info'], 'task', $this->data['level']);

        /*
         * 测试投递finish路由
         *
        $tasks['uri'] = '/finish/log/index';
        $tasks['content'] = 'task send email finish';
        $tasks['info'] = 'test';
        $tasks['level'] = 'info';
        echo serialize($tasks);
        */
    }

}
