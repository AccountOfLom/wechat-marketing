<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\WxUser;
use Closure;
use Illuminate\Support\Facades\Cache;

class CheckUserToken
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
        try {
            //检验 token 是否有效
            $errorCode = 10000;
            $token = $request->hasHeader('userToken') ? $request->header('userToken') : $request->cookie('userToken');
            if (!$token) {
                throw new \Exception('userToken不能为空');
            }
            $ip = $request->ip() ? ip2long($request->ip()) : '';
            $token .= $ip;
            if (!Cache::has($token)) {
                throw new \Exception('无效的userToken');
            }
            //检验 user
            $errorCode = 10001;
            $userId = Cache::get($token);
            $user = (new WxUser())->find($userId);
            if (!$user) {
                throw new \Exception('用户不存在');
            }
            if ($user->state != 1) {
                throw new \Exception('用户已被禁用');
            }
            session(['userInfo' => $user->toArray()]);
            //更新token 缓存时间
            Cache::put($token, $userId, config('system.tokenValidTime'));
            return $next($request);
        } catch (\Exception $e) {
            $baseController = new BaseController();
            $baseController->result = [
                'state'         => 0,
                'error_code'    => $errorCode,
                'message'       => $e->getMessage()
            ];
            return $baseController->result;
        }
    }
}
