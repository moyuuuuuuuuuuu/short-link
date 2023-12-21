<?php

namespace app\exception;

class NoLoginException extends \RuntimeException
{
    public function __construct($message = "", $code = 4001, \Throwable $previous = null)
    {
        $message = $message ?? '请先登录';
        parent::__construct($message, $code, $previous);
    }
}
