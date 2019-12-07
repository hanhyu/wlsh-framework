<?php

namespace App\Library;

use Exception;

/**
 * wlsh程序手动抛出的异常
 * Class ProgramException
 */
class ProgramException extends Exception
{
    public function __construct(string $message, int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
