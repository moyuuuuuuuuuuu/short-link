<?php

namespace app\manager\controller;


use app\common\enums\UserEnum;
use app\manager\model\User;
use app\service\captcha\Email;
use app\service\captcha\Image;
use app\service\captcha\Sms;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use support\Cache;
use support\Request;
use Webman\Event\Event;

class Login extends Base
{
    static $noNeedLogin = ['login', 'phoneLogin', 'emailLogin', 'sms', 'email', 'captcha'];

    public function login()
    {
        $username = $this->request->post('username', '');
        $password = $this->request->post('password', '');
        $captcha  = $this->request->post('captcha', '');
        #TODO:用户名密码登录
        if ((new Image($this->request))->check($captcha)) {
            return $this->p('验证码错误', 400);
        }
        $where = ['username' => $username];
        $user  = User::where($where)->first();
        if (empty($user)) {
            return $this->p('用户不存在', 402);
        }

        if (!password_verify($password, $user->password)) {
            return $this->p('密码错误', 400);
        }
        if ($user->status !== User::NORMAL) {
            return $this->p('用户已被禁用', 400);
        }
        $token               = $user->generateToken();
        $this->request->user = $user;
        Event::emit('login', $this->request);
        return $this->p('登录成功', 200, [
            'token' => $token
        ]);
    }

    public function phoneLogin()
    {
        $phone = $this->request->post('phone', '');
        $code  = $this->request->post('code', '');

        $captcha = new Sms($this->request);
        if (!$captcha->check($code)) {
            return $this->p('验证码错误', 400);
        }
        if ($this->request->user) {
            $user = User::where('id', $this->request->user->id)->first();
        } else {
            $where = ['phone' => $phone];
            $user  = User::where($where)->first();
        }


        if (!$user) {
            $username = User::noncestr(10);
            $user     = User::create([
                'username'   => $username,
                'nickname'   => User::createNickName(),
                'phone'      => $phone,
                'status'     => User::NORMAL,
                'appid'      => User::appid(),
                'password'   => password_hash($phone, PASSWORD_DEFAULT),
                'avatar'     => User::createAvatar($username),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } else if (!$user->phone) {
            $user->phone = $phone;
            $user->save();
        }

        if ($user->status !== User::NORMAL) {
            return $this->p('用户已被禁用', 400);
        }
        $this->request->user = $user;
        $token               = $user->generateToken();
        Event::emit('login', $this->request);
        return $this->p('登录成功', 200, [
            'token' => $token
        ]);
    }

    public function emailLogin()
    {
        $email   = $this->request->post('email', '');
        $code    = $this->request->post('code', '');
        $captcha = new Email($this->request);
        if (!$captcha->check($code)) {
            return $this->p('验证码错误', 400);
        }
        if ($this->request->user) {
            $user = User::where('id', $this->request->user->id)->first();
        } else {
            $where = ['email' => $email];
            $user  = User::where($where)->first();
        }
        if (!$user) {
            list($emailInfo,) = explode('@', $email);
            $username = User::noncestr(10);
            $user     = User::create([
                'username'   => $username,
                'nickname'   => User::createNickName(),
                'email'      => $email,
                'status'     => User::NORMAL,
                'appid'      => User::appid(),
                'password'   => password_hash($emailInfo, PASSWORD_DEFAULT),
                'avatar'     => User::createAvatar($username),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } elseif (!$user->email) {
            $user->email = $email;
            $user->save();
        }
        if ($user->status !== User::NORMAL) {
            return $this->p('用户已被禁用', 400);
        }

        $this->request->user = $user;
        $token               = $user->generateToken();

        Event::emit('login', $this->request);
        return $this->p('登录成功', 200, [
            'token' => $token
        ]);
    }

    public function logout()
    {

    }

    public function sms(Request $request)
    {

        if (!(new Sms($request))->render()) {
            return $this->p('验证码发送失败', 400);
        }

        return $this->p('验证码发送成功', 200);
    }

    public function email(Request $request)
    {
        if (!(new Email($request))->render()) {
            return $this->p('验证码发送失败', 400);
        }

        return $this->p('验证码发送成功', 200);
    }

    public function captcha(Request $request)
    {
        return response((new Image($request))->render(), 200, ['Content-Type' => 'image/png']);
    }
}
