<?php

namespace app\common\model;

use support\Model;

class ManagerVisit extends Model
{
    public    $table      = 'manager_visit';
    public    $timestamps = false;
    protected $fillable   = [
        'user_id',
        'module',
        'controller',
        'action',
        'ip',
        'time',
        'x_request_id',
        'param',
        'city',
        'lat',
        'lon',
    ];
}
