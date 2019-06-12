<?php

/**
 * 程序手动抛出的异常
 * Class ProgramException
 */
class ProgramException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}