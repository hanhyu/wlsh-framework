<?php
declare(strict_types=1);

namespace App\Modules\System\controllers;

use App\Domain\System\Log;
use App\Models\Forms\SystemLogForms;
use Exception;
use Yaf\Controller_Abstract;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-9-3
 * Time: 下午4:57
 */
class LogMongo extends Controller_Abstract
{
    use \ControllersTrait;

    /**
     * @var Log
     */
    protected $log;

    public function init()
    {
        $this->beforeInit();
        $this->log = new Log();
    }

    /**
     * 列表
     * @throws Exception
     */
    public function getMongoListAction(): void
    {
        $data = $this->validator(SystemLogForms::$getMongoList);
        $res  = $this->log->getMongoList($data);
        $this->response->end(http_response(200, '', $res));
    }

    /**
     * User: hanhyu
     * Date: 19-6-22
     * Time: 下午10:11
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getMongoInfoAction()
    {
        $data = $this->validator(SystemLogForms::$getMongoInfo);
        $res  = $this->log->getMongoById($data['id']);
        $this->response->end(http_response(200, '', $res));
    }

}
