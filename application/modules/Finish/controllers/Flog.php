<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-14
 * Time: 下午8:33
 */

class FlogController extends Yaf\Controller_Abstract
{
    /**
     * @param        $content
     * @param string $info
     * @param string $level
     */
    public function IndexAction($content, string $info, string $level): void
    {
        co_log($content, $info, $level);
    }

}