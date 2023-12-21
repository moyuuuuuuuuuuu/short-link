<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class XRequestId implements MiddlewareInterface
{

    /**
     * @inheritDoc
     */
    public function process(Request $request, callable $handler): Response
    {
        // TODO: Implement process() method.
        $request->XRequestId = $xRequestId ?? uniqid();
        return $handler($request);
    }
}
