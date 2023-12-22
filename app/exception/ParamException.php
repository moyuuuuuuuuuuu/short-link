<?php

namespace app\exception;

class ParamException extends SystemException
{
    public function __construct(string $message = "参数错误", int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
