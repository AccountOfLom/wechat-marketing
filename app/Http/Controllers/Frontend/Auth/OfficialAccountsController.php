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
use Illuminate\Http\Request;

/**
 * 公众号授权
 * Class OfficialAccountsController
 * @package App\Http\Controllers\Api\Auth
 */
class OfficialAccountsController extends BaseController
{
    use UserToken;

    /**
     * 发起授权
     * @return mixed
     */
    public function oauth()
    {
        return $this->OfficialAccountsServer->oauth->redirect();
    }

    /**
     * 获取JsSDK配置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function JsSdkConfig(Request $request)
    {
        $jsApiList = [
            'updateAppMessageShareData',
            'updateTimelineShareData',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareQZone',
            'startRecord',
            'stopRecord',
            'onVoiceRecordEnd',
            'playVoice',
            'pauseVoice',
            'stopVoice',
            'onVoicePlayEnd',
            'uploadVoice',
            'downloadVoice'
        ];
        if ($request->has('url')) {
            $this->OfficialAccountsServer->jssdk->seturl(urldecode($request->get('url')));
        }
        $jsConfig = $this->OfficialAccountsServer->jssdk->buildConfig($jsApiList, false);
        $this->result['data']['jsConfig'] = json_decode($jsConfig);
        return response()->json($this->result, 200);
    }


}