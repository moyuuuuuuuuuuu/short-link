<?php

namespace app\manager\controller;

use app\manager\controller\Base;

use Webman\Http\{Response, Request};

class Index extends Base
{
    public function index(Request $request)
    {
        return $this->p('123123', 200, [
            'name' => 'webman',
            'time' => time(),
        ]);
    }
}
