<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ip = $request->ip() ? ip2long($request->ip()) : '';

        $params = $request->all() ?? [];
        $xmlParams = file_get_contents("php://input");
        if ($xmlParams) {
            $xmlParams = is_xml($xmlParams) ? xml_to_data($xmlParams) : $xmlParams;
            $params = array_merge($params, ['php://input' => $xmlParams]);
        }
        $params = json_encode($params);

        $key = $request->header('userToken') ?? $request->cookie('userToken');
        $requestInfo = [
            'user_id'       => Cache::get($key . $ip) ?? 0,
            'ip'            => $request->ip(),
            'request_time'  => date('Y-m-d H:i:s', time()),
            'route'         => $request->path(),
            'url'           => $request->fullUrl(),
            'method'        => $request->method(),
            'params'        => $params,
            'header'        => json_encode($request->header()),
            'error_message' => ''
        ];
        try {
            $requestId = DB::table('wx_log_api')->insertGetId($requestInfo);
            //本次请求记录的id
            session(['request_id' => $requestId]);
        } catch (\Exception $e) {
//            Log::error('api请求日志写入失败：' . $e->getMessage() .' 参数：' . json_encode($requestInfo));
        }
        return $next($request);
    }
}
