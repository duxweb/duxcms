<?php

namespace Dux\Api;

use Dux\Handlers\ExceptionBusiness;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ApiMiddleware
{

    /**
     * 误差秒
     * @var int
     */
    protected int $time = 60;


    public function __construct(public $callback)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {

        // 请求超时
        if (!$this->allowTimestamp($request)) {
            throw new ExceptionBusiness('Request Timeout', 408);
        }
        // 签名失败
        if (!$this->signVerify($request)) {
            throw new ExceptionBusiness('Sign Failed', 402);
        }
        return $handler->handle($request);
    }

    /**
     * 签名验证
     * @param Request $request
     * @return bool
     */
    protected function signVerify(Request $request): bool
    {
        $time = $request->getHeader('Content-Date')[0];
        $sign = $request->getHeader('Content-MD5')[0];
        $id = $request->getHeader('AccessKey')[0];

        if (empty($id) || empty($sign) || empty($time)) {
            throw new ExceptionBusiness('Parameter signature failed', 402);
        }

        $secretKey = call_user_func($this->callback, $id);
        if (!$secretKey) {
            throw new ExceptionBusiness('Signature authorization failed', 402);
        }
        $signData = [];
        $signData[] = $request->getUri()->getPath();
        $signData[] = urldecode($request->getUri()->getQuery());
        $signData[] = $time;
        $signStr = hash_hmac("SHA256", implode("\n", $signData), $secretKey);
        return $signStr === $sign;
    }

    /**
     * 判断时差
     * @param Request $request
     * @return bool
     */
    protected function allowTimestamp(Request $request): bool
    {
        $queryTime = (int)$request->getHeader('Content-Date')[0];
        if ($queryTime + $this->time < time()) {
            return false;
        }
        return true;
    }
}