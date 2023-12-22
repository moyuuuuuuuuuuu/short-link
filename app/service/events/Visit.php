<?php

namespace app\service\events;

use app\common\model\LoginLog;
use app\common\model\ManagerVisit;
use app\service\enums\VisitEnum;
use Webman\Http\Request;

class Visit
{
    /**
     * 访问记录
     * @param Request $request
     * @return void
     */
    public function visit(Request $request)
    {
        $controller         = $request->controller;
        $controllerTmpArray = explode('\\', $controller);
        $data               = [
            'module'       => $request->app,
            'controller'   => strtolower(array_pop($controllerTmpArray)),
            'action'       => $request->action,
            'ip'           => $request->getRealIp(),
            'time'         => time(),
            'user_id'      => $request->user->id ?? 0,
            'x_request_id' => $request->XRequestId ?? '',
            'param'        => json_encode($request->all()),
            'city'         => '',
            'lat'          => 0,
            'lon'          => 0,
        ];

        $apiUri = sprintf('http://ip-api.com/json/%s?fields=city,lat,lon', $data['ip']);
        $result = file_get_contents($apiUri);
        $result = json_decode($result, true);
        if ($result && $result['status'] && $result['status'] == 'success') {
            $data['city'] = $result['city'];
            $data['lat']  = $result['lat'];
            $data['lon']  = $result['lon'];
        }
        ManagerVisit::create($data);
    }

    public function login(Request $request)
    {
        $data   = [
            'ip'      => $request->getRealIp(),
            'time'    => time(),
            'user_id' => $request->user->id ?? 0,
            'city'    => '',
            'lat'     => 0,
            'lon'     => 0,
        ];
        $apiUri = sprintf('http://ip-api.com/json/%s?fields=city,lat,lon', $data['ip']);
        $result = file_get_contents($apiUri);
        $result = json_decode($result, true);
        if ($result && $result['status'] && $result['status'] == 'success') {
            $data['city'] = $result['city'];
            $data['lat']  = $result['lat'];
            $data['lon']  = $result['lon'];
        }
        LoginLog::create($data);
    }
}
