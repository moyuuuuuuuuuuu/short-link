<?php

namespace app\service\captcha;

use support\Cache;
use Webman\Captcha\CaptchaBuilder;

class Image extends Captcha
{
    protected $config = [
        'max_width'  => 150,
        'max_height' => 40,
        'length'     => 4,
    ];

    public function render()
    {
        $builder = new CaptchaBuilder();
        // 生成验证码
        $width  = $this->request->get('s', 150);
        $height = $this->request->get('d', 40);

        $width  = $width > $this->config['max_width'] ? $this->config['max_width'] : $width;
        $height = $height > $this->config['max_height'] ? $this->config['max_height'] : $height;

        $builder->build($width, $height);
        $this->request->session()->set('captcha', strtolower($builder->getPhrase()));
        return $builder->get();
    }

    public function check($captcha)
    {
        return strtolower($captcha) !== $this->request->session()->get('captcha');
    }
}
