<?php

namespace app\middleware;

use app\service\enums\UserEnum;
use app\exception\NoLoginException;
use app\exception\ValidTokenException;
use app\manager\controller\Base;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
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
        if (empty($noNeedLogin) || in_array($action, $noNeedLogin) || in_array('*', $noNeedLogin)) {
            return $next($request);
        }

        if (!$authorization) {
            throw new NoLoginException('请先登录', 401);
        }
        list($bearer, $authorization) = explode(' ', $authorization);

        if ($bearer != 'Bearer') {
            throw new ValidTokenException('请先登录', 401);
        }
        #验证token
        $parse    = new  Parser(new JoseEncoder());
        $token    = $parse->parse($authorization);
        $validate = new Validator();
        try {
            $validate->assert($token,);
        } catch (RequiredConstraintsViolated $e) {
            throw new ValidTokenException($e->violations(), 401);
        }

        $userInfo      = $token->claims()->all();
        $request->user = new UserEnum($userInfo);
        return $next($request);
    }
}
