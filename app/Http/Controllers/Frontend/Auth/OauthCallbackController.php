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

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Frontend\Auth\Traits\UserToken;
use App\Http\Controllers\Frontend\BaseController;
use App\Repositories\WxUserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

/**
 * 微信授权通用回调处理类
 * 配置相关：config/wechat oauth.callback
 * Class OauthCallbackController
 * @package App\Http\Controllers\Frontend\Auth
 */
class OauthCallbackController extends BaseController
{
    use UserToken;

    /**
     * 微信用户同意授权后同步跳转到此
     * @param Request $request
     * @param WxUserRepository $wxUserRepository
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index(Request $request, WxUserRepository $wxUserRepository)
    {
        //获取微信用户信息
        $oauth = $this->OfficialAccountsServer->oauth;
        $userInfo = $oauth->user()->toArray();

        //用户是否已存在
        $user = $wxUserRepository->getUserByUnionid($userInfo['original']['unionid']);
        if (!$user) {
            $userId = $wxUserRepository->store($userInfo['original']);
        } else {
            $wxUserRepository->updateNickName($userInfo['original']);
            $userId = $user->user_id;
        }

        //存储用户 accessToken
        Redis::set($userId . '_access_token', $userInfo['token']);

        //生成 token
        $userToken = $this->createUserToken($userId);

        $backUrl = $request->get('back_url');

        if (!$backUrl) {
            return response()->json([
                'state' => 1,
                'message' => 'success',
                'data' => ['userToken' => $userToken]
            ]);
        }

        //重定向到前端地址
        if (strpos($backUrl, '?') === false) {
            return redirect("{$backUrl}?userToken={$userToken}");
        }
        return redirect("{$backUrl}&userToken={$userToken}");
    }
}