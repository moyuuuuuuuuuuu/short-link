<?php

namespace app\common\controller;

use support\{Request, Response};

class Base
{


    /**
     * @var Request
     */
    protected $request;

    public function beforeAction(Request $request)
    {
        $this->request = $request;
        $this->initialize();
    }

    public function initialize()
    {

    }

    public function afterAction(Request $request, Response $response)
    {

    }

    public function p($msg = 'success', $code = 200, $data = [])
    {
        return \response(json_encode([
            'code'    => $code,
            'message' => $msg,
            'data'    => $data,
        ]));
    }
}
