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

class Login extends Base
{
    static $noNeedLogin = ['login', 'smscode', 'emailcode', 'captcha'];

    public function login()
    {
        $phone    = $this->request->post('phone', '');
        $email    = $this->request->post('email', '');
        $username = $this->request->post('username', '');
        $password = $this->request->post('password', '');
        $captcha  = $this->request->post('captcha', '');
        if (!empty($phone)) {
            #TODO:手机验证码登录
            $captchaInstance = new Sms($this->request);
            $where           = ['phone' => $phone];
        } else if (!empty($email)) {
            #TODO:邮箱验证码登录
            $captchaInstance = new Email($this->request);
            $where           = ['email' => $email];
        } else {
            #TODO:用户名密码登录
            $captchaInstance = new Image($this->request);
            $where           = ['username' => $username];
        }
        if ($captchaInstance->check($captcha)) {
            return $this->p('验证码错误', 400);
        }
        $user = User::where($where)->first();
        if (empty($user)) {
            return $this->p('用户不存在', 400);
        }
        if (!empty($password) && !empty($username) && !password_verify($password, $user->password)) {
            return $this->p('密码错误', 400);
        }
        if ($user->status !== User::NORMAL) {
            return $this->p('用户已被禁用', 400);
        }
        if (!Cache::has('token_' . $user->id)) {
            $now        = new \DateTimeImmutable();
            $algorithm  = new Sha256();
            $signingKey = InMemory::plainText(random_bytes(32));
            $builder    = new Builder((new JoseEncoder()), ChainedFormatter::default());
            $token      = $builder->issuedBy(getenv('JWT_ISSUER',))
                ->permittedFor(getenv('JWT_AUDIENCE'))
                ->identifiedBy(md5($user->id))
                ->issuedAt($now)
                ->expiresAt($now->modify('+' . intval(getenv('JWT_TTL', 1)) . ' hour'))
                ->withClaim('id', $user->id)
                ->withClaim('username', $user->username)
                ->withClaim('phone', $user->phone)
                ->withClaim('email', $user->email)
                ->withClaim('avatar', $user->avatar)
                ->withClaim('status', $user->status)
                ->getToken($algorithm, $signingKey);
            $token      = $token->toString();
            Cache::set('token_' . $user->id, $token, intval(getenv('JWT_TTL', 1)) * 3600);
        } else {
            $token = Cache::get('token_' . $user->id);
        }
        return $this->p('登录成功', 200, [
            'token' => $token
        ]);
    }

    public function logout()
    {

    }

    public function smscode()
    {

        if (!(new Sms($this->request))->render()) {
            return $this->p('验证码发送失败', 400);
        }

        return $this->p('验证码发送成功', 200);
    }

    public function emailcode()
    {
        if (!(new Email($this->request))->render()) {
            return $this->p('验证码发送失败', 400);
        }

        return $this->p('验证码发送成功', 200);
    }

    public function captcha()
    {
        return (new Image($this->request))->render();
    }
}
