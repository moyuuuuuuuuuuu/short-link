<?php

namespace app\exception;

class ValidTokenException extends \RuntimeException
{
    public function __construct($message = "", $code = 4002, \Throwable $previous = null)
    {
        $message = $message ?? '令牌无效';
        parent::__construct($message, $code, $previous);
    }
}
