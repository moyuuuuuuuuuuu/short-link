<?php

namespace app\service\events;

use app\service\enums\VisitEnum;

class Visit
{
    public function visit(VisitEnum $visit)
    {
        $data   = $visit->toArray();
        $apiUri = sprintf('http://ip-api.com/json/%s?fields=city,lat,lon', $data['ip']);
        $result = file_get_contents($apiUri);
        $result = json_decode($result, true);
        if ($result && $result['status'] && $result['status'] == 'success') {
            $data['city'] = $result['city'];
            $data['lat']  = $result['lat'];
            $data['lon']  = $result['lon'];
        }
        #TODO:记录访问日志
        $res = \app\common\model\ManagerVisit::create($data);
        var_dump($res);
    }
}
