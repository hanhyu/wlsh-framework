<?php
declare(strict_types=1);

namespace App\Modules\Task\controllers;

use Yaf\Controller_Abstract;

/**
 * 操作日志类
 * User: hanhyu
 * Date: 18-10-22
 * Time: 下午5:00
 */
class Log extends Controller_Abstract
{
    /**
     * @param        $content
     * @param string $info
     * @param string $level
     */
    public function IndexAction($content, string $info, string $level): void
    {
        co_log($content, $info, 'task', $level);

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
