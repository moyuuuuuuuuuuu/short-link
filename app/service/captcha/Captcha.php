<?php

namespace app\service\captcha;

use support\Request;

abstract class Captcha
{
    protected $request = null;

    protected $config = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function config(array $config = [])
    {
        $this->config = $config;
    }

    abstract public function render();

    abstract public function check($code);
}
