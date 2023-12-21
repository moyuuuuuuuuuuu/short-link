<?php

namespace app\service\captcha;

use support\Request;
use support\Cache;
use app\exception\ParamException;

class Email extends Captcha
{

    public function render()
    {
        $email = $this->request->post('email', $this->request->get('email', ''));
        if (!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)) {
            throw new ParamException('邮箱格式不正确');
        }
        #检查缓存是否有验证码
        if (Cache::has('emailcode_' . $email)) {
            throw new ParamException('验证码已发送，请稍后再试');
        }
        $code = rand(1000, 9999);
        #发送验证码
        Cache::set('emailcode_' . $email, $code, 60);
        #调接口发送验证码
        return true;
    }

    public function check($code)
    {
        $email = $this->request->post('email', $this->request->get('email', ''));
        if (!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)) {
            throw new ParamException('邮箱格式不正确');
        }
        if (Cache::get('emailcode_' . $email) !== $code) {
            throw new ParamException('验证码错误');
        }
        return true;
    }
}
