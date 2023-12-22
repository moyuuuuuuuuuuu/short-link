<?php

namespace app\service\captcha;

use app\exception\ParamException;
use support\Cache;
use support\Request;

class Sms extends Captcha
{

    public function render()
    {
        $phone = $this->request->post('phone', $this->request->get('phone', ''));
        if (!preg_match("/^1[3456789]\d{9}$/", $phone)) {
            throw new ParamException('手机号格式不正确');
        }
        #检查缓存是否有验证码
        if (Cache::has('captcha:sms:' . md5($phone))) {
            throw new ParamException('验证码已发送，请稍后再试');
        }
        $code = rand(1000, 9999);
        #发送验证码
        Cache::set('captcha:sms:' . md5($phone), $code, $this->config['ttl']);
        #调接口发送验证码
        return true;
    }

    public function check($captcha)
    {
        $phone = $this->request->post('phone', $this->request->get('phone', ''));
        if (!preg_match("/^1[3456789]\d{9}$/", $phone)) {
            throw new ParamException('手机号格式不正确');
        }
        if (Cache::get('captcha:sms:' . md5($phone)) != $captcha) {
            throw new ParamException('验证码错误');
        }
        Cache::delete('captcha:sms:' . md5($phone));
        return true;
    }
}
