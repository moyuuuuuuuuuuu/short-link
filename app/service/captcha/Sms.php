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
        if (Cache::has('smscode_' . $phone)) {
            throw new ParamException('验证码已发送，请稍后再试');
        }
        $code = rand(1000, 9999);
        #发送验证码
        Cache::set('smscode_' . $phone, $code, 60);
        #调接口发送验证码
        return true;
    }

    public function check($code)
    {
        $phone = $this->request->post('phone', $this->request->get('phone', ''));
        if (!preg_match("/^1[3456789]\d{9}$/", $phone)) {
            throw new ParamException('手机号格式不正确');
        }
        if (Cache::get('smscode_' . $phone) !== $code) {
            throw new ParamException('验证码错误');
        }
        return true;
    }
}
