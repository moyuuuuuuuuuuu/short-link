<?php

namespace app\manager\controller;

use app\service\enums\VisitEnum;
use support\{Request, Response};
use Webman\Event\Event;

class Base extends \app\common\controller\Base
{
    static $noNeedLogin = [];

    protected $user;


    public function afterAction(Request $request, Response $response)
    {
        parent::afterAction($request, $response);
        #TODO:记录访问日志
        Event::emit('visit', new VisitEnum([
            'controller'   => $this->request->controller,
            'action'       => $this->request->action,
            'ip'           => $this->request->getRealIp(),
            'time'         => time(),
            'user_id'      => $this->request->user->id ?? 0,
            'x_request_id' => $this->request->XRequestId ?? '',
            'param'        => json_encode($this->request->all()),
        ]));
    }
}
