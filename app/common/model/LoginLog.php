<?php

namespace app\common\model;

use support\Model;

class LoginLog extends Model
{
    protected $table    = 'login_log';
    protected $fillable = ['user_id', 'ip', 'time', 'city', 'lat', 'lon'];
}
