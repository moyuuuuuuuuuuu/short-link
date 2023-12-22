<?php

namespace app\service\captcha;

use Webman\Http\Request;

abstract class Captcha
{
    protected $request = null;

    protected $config = [
        'ttl' => 180
    ];

    public function __construct(Request $request, array $config = [])
    {
        $this->request = $request;
        $this->config  = array_merge($this->config, $config);
    }


    public function config(array $config = [])
    {
        $this->config = $config;
    }

    abstract public function render();

    abstract public function check($captcha);
}
