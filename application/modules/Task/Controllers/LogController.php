<?php
declare(strict_types=1);

namespace App\Modules\Task\Controllers;

use App\Library\ProgramException;
use App\Library\Router;
use App\Library\TaskTrait;
use App\Models\Clickhouse\RouterLogClickhouse;
use App\Models\Mysql\RouterLogMysql;
use Envms\FluentPDO\Exception;

/**
 * 操作日志类
 * UserDomain: hanhyu
 * Date: 18-10-22
 * Time: 下午5:00
 */
class LogController
{
    use TaskTrait;

    public function __construct()
    {
        $this->beforeInit();
    }

    /**
     * @throws \Exception
     */
    #[Router(method: 'CLI', auth: false)]
    public function IndexAction(): void
    {
        if ($this->data['level'] === 'critica' or $this->data['level'] === 'alert' or $this->data['level'] === 'emergency') {
            send_email($this->data['content'], $this->data['info']);
        }

        if (APP_DEBUG) {
            //$let = monolog_by_mongodb($this->data['content'], $this->data['info'], $this->data['channel'], $this->data['level']);
            //if (!$let) { //如果使用mongodb记录日志失败，则使用文件存储日志。
            monolog_by_file($this->data['content'], $this->data['info'], $this->data['channel'], $this->data['level']);
            //}
        }

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

    /**
     * User: hanhyu
     * Date: 2021/2/4
     * Time: 下午2:59
     * @throws Exception
     * @throws ProgramException
     * @throws \JsonException
     * @todo 流量日志如果需要进行分析，可以使用ClickHouse替换
     */
    #[Router(method: 'CLI', auth: false)]
    public function routerLogAction(): void
    {
        RouterLogMysql::getInstance()->setLog($this->data);
        //RouterLogClickhouse::getInstance()->setLog($this->data);
    }

}
