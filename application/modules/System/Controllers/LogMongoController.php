<?php
declare(strict_types=1);

namespace App\Modules\System\Controllers;

use App\Domain\System\LogDomain;
use App\Library\ControllersTrait;
use App\Library\ProgramException;
use App\Library\ValidateException;
use App\Models\Forms\SystemLogForms;
use MongoDB\Driver\Exception\Exception;
use JsonException;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-9-3
 * Time: 下午4:57
 */
class LogMongoController
{
    use ControllersTrait;

    /**
     * @var LogDomain
     */
    protected LogDomain $log;

    public function __construct()
    {
        $this->beforeInit(false);
        $this->log = new LogDomain();
    }

    /**
     * 列表
     * @throws ProgramException
     * @throws ValidateException
     * @throws Exception|JsonException
     * @router auth=true&method=get
     */
    public function getMongoListAction(): void
    {
        $data = $this->validator(SystemLogForms::$getMongoList);
        $res  = $this->log->getMongoList($data);
        $this->response->end(http_response(200, '', $res));
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-22
     * Time: 下午10:11
     * @throws ProgramException
     * @throws ValidateException
     * @throws Exception|JsonException
     * @router auth=true&method=get
     */
    public function getMongoInfoAction(): void
    {
        $data = $this->validator(SystemLogForms::$getMongoInfo);
        $res  = $this->log->getMongoById($data['id']);
        $this->response->end(http_response(200, '', $res));
    }

}
