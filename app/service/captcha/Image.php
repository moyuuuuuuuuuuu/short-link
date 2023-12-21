<?php

namespace app\service\captcha;

use support\Cache;
use Webman\Captcha\CaptchaBuilder;

class Image extends Captcha
{

    public function render()
    {
        $builder = new CaptchaBuilder();
        // 生成验证码
        $builder->build();
        $this->request->session()->set('captcha', strtolower($builder->getPhrase()));
        return $builder->get();
    }

    public function check( $captcha)
    {
        return strtolower($captcha) !== $this->request->session()->get('captcha');
    }
}
