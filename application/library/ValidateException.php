<?php
namespace App\Library;

use Exception;

/**
 * 参数验证手动触发的信息
 * Class ValidateException
 */
class ValidateException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
