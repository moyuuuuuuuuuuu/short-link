<?php

namespace app\manager\controller;

use app\service\enums\VisitEnum;
use  Webman\Http\{Request, Response};
use Webman\Event\Event;

class Base extends \app\common\controller\Base
{
    static $noNeedLogin = [];

    protected $user;


    public function afterAction(Request $request, Response $response)
    {
        Event::emit('visit', $this->request);
        parent::afterAction($request, $response);
    }
}
