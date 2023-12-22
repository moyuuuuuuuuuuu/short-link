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
        if (Cache::has('captcha:email:' . md5($email))) {
            throw new ParamException('验证码已发送，请稍后再试');
        }
        $code         = rand(1000, 9999);
        $emailService = new \app\service\Email();
        $emailService->address($email);
        $emailService->subject('验证码');
        $emailService->body('验证码：' . $code . ',有效期：' . $this->config['ttl'] . '秒');
        if ($emailService->send()) {
            #发送验证码
            Cache::set('captcha:email:' . md5($email), $code, $this->config['ttl']);
            return true;
        }
        throw new ParamException('验证码发送失败');
    }

    public function check($captcha)
    {
        $email = $this->request->post('email', $this->request->get('email', ''));
        if (!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)) {
            throw new ParamException('邮箱格式不正确');
        }
        if (Cache::get('captcha:email:' . md5($email)) != $captcha) {
            throw new ParamException('验证码错误');
        }
        Cache::delete('captcha:email:' . md5($email));
        return true;
    }
}
