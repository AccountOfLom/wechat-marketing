<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace App\Http\Controllers\Frontend\Auth\Traits;

use App\Models\WxUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

trait UserToken
{

    /**
     * token   缓存
     * @param string $userId
     * @return string
     */
    public function createUserToken(string $userId): string
    {
        $key = md5(str_shuffle($userId . time()));
        $ip = Request::capture()->ip() ? ip2long(Request::capture()->ip()) : '';
        Cache::add($key . $ip, $userId, config('system.tokenValidTime'));
        return $key;
    }

    /**
     * 根据用户 user_id 生成token
     * 测试用
     * @param string $userId
     * @return JsonResponse
     */
    public function testToken(string $userId)
    {
        $user = (new WxUser())->find($userId);
        if (!$user) {
            die('用户不存在：' . $userId);
        }
        $this->result['data']['userToken'] = $this->createUserToken($userId);
        return response()->json($this->result, 200);
    }
}