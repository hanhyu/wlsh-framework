<?php
declare(strict_types=1);

use App\Services\System\LogServices;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-9-3
 * Time: 下午4:57
 */
class LogMongoController extends Yaf\Controller_Abstract
{
    use \App\Library\ControllersTrait;

    /**
     * @var LogServices
     */
    private $log;

    public function init()
    {
        $this->beforeInit();
        $this->log = new LogServices();
    }

    /**
     * 列表
     * @throws Exception
     */
    public function getMongoListAction(): void
    {
        $data = $this->validator('SystemLogForms', 'getMongoList');
        $res = $this->log->getMongoList($data);
        if ($res) {
            $this->response->end(http_response(200, $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

    /**
     *
     * @throws Exception
     */
    public function getMongoInfoAction()
    {
        $data = $this->validator('SystemLogForms', 'getMongoInfo');
        $res = $this->log->getMongoById($data['id']);
        if ($res) {
            $this->response->end(http_response(200, $res));
        }
    }

}