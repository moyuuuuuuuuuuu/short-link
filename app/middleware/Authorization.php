<?php

namespace app\middleware;

use app\service\enums\UserEnum;
use app\exception\NoLoginException;
use app\exception\ValidTokenException;
use app\manager\controller\Base;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Validator;
use Webman\MiddlewareInterface;
use Webman\Http\Request;
use Webman\Http\Response;

class Authorization implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        /**
         * @var Base $controllerClass
         */
        $authorization   = $request->header('authorization');
        $controllerClass = $request->controller;
        $action          = $request->action;
        $noNeedLogin     = $controllerClass::$noNeedLogin;

        if (!in_array($action, $noNeedLogin) && !in_array('*', $noNeedLogin) && empty($authorization)) {
            throw new NoLoginException('请先登录', 401);
        }
        if (!empty($authorization)) {
            list($bearer, $authorization) = explode(' ', $authorization);

            if ($bearer != 'Bearer') {
                throw new ValidTokenException('请先登录', 401);
            }
            #验证token
            $parse = new  Parser(new JoseEncoder());
            $token = $parse->parse($authorization);
            if ($token->isExpired(new \DateTimeImmutable())) {
                throw new ValidTokenException('token已过期', 401);
            }
            $validate = new Validator();

            try {
                $validate->assert($token, new IssuedBy(getenv('JWT_ISSUER')));
                $validate->assert($token, new PermittedFor(getenv('JWT_AUDIENCE')));
            } catch (RequiredConstraintsViolated $e) {
                throw new ValidTokenException($e->violations(), 401);
            }

            $userInfo       = $token->claims()->all();
            $key            = $userInfo['v'] ?? '';
            $iv             = $userInfo['iv'] ?? '';
            $hashData       = $userInfo['hashData'] ?? '';
            $originalString = openssl_decrypt($hashData, 'aes-256-cbc', $key, 0, $iv);
            $data           = json_encode($originalString, true);
        } else {
            $data = [];
        }
        $request->user = new UserEnum((array)$data);
        return $next($request);
    }
}
