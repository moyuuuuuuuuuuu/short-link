<?php

namespace app\service\events;

use app\manager\model\Short as Model;
use support\Redis;
use Workerman\Timer;

class ShortCode
{
    public function insert($data)
    {
        $code = $data['code'];
        $link = $data['link'];

        Redis::set('short:' . $code, $link, Model::DEFAULT_EXPIRE_TIME);
        Redis::zIncrBy('short:hotness' . $code, Model::DEFAULT_HOTNESS, $code);

        $timeId = Timer::add(3600, function () use ($code) {
            Redis::zIncrBy('short:hotness', -Model::HOTNESS_DECAY_NUM, $code);
            $ttl = Redis::ttl('short:' . $code);
            $ttl = min($ttl, abs($ttl - Model::HOTNESS_DECAY_TIME));
            Redis::expire('short:' . $code, $ttl - Model::HOTNESS_DECAY_TIME);
        });
        Redis::set('short:code:timer:' . $code, $timeId);
    }

    public function delete($code)
    {
        Redis::del('short:' . $code);
        Redis::zRem('short:hotness', $code);
        $timerId = Redis::get('short:code:timer:' . $code);
        Timer::del($timerId);
    }
}
