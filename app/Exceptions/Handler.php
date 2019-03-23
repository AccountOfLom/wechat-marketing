<?php

namespace App\Exceptions;

use App\Exceptions\Traits\LogError;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use LogError;

    protected $error_code = 10050;

    protected $message = '糟糕！网络出问题了~';

    protected $code = 500;

    /**
     * 不被处理的异常错误
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * 认证异常时不被flashed的数据
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * 上报异常至错误driver，如日志文件(storage/logs/laravel.log)，第三方日志存储分析平台
     *
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * 将异常信息响应给客户端
     *
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // DEBUG 模式下，不做处理
        if(config('app.debug')){
            return parent::render($request, $exception);
        }
        return $this->handle($request, $exception);
    }

    public function handle($request, $exception)
    {
        if (!$exception instanceof CustomException) {
            //写入异常日志
            $this->writeLogError($exception);
            $result = [
                'state'         => 0,
                'error_code'    => $this->error_code,
                'data'          => [],
                'message'       => $this->message
            ];
            return response()->json($result, $this->code);
        }
        return parent::render($request, $exception);
    }
}
