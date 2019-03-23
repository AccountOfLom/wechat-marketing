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

namespace App\Http\Controllers\Frontend\Integrity315;


use App\Http\Controllers\Frontend\BaseController;
use App\Service\EasyWechat\OfficialAccounts;
use Illuminate\Support\Facades\DB;


/**
 * 模板消息推送
 * Class MessageController
 * @package App\Http\Controllers\Frontend\Integrity315
 */
class MessageController extends BaseController
{
    /**
     * 发起时推动
     * @param $publicityId
     */
    public function create($publicityId)
    {
        $templateId = '8El9dfGK4yWuHxcCpdE1v-Ehr72tcmbRTHaqbMDSji4';
        $url = domain() . "/Frontend/315/html/index.html?publicity_id={$publicityId}&channel=default&v=" . time();
        $msg = [
            'first'     => [
                'value'     => "恭喜！你已成功发起诚信宣言！\n展现诚信态度，传递保险正能量！\n",
                'color'     => '#FF2525'
            ],
            'keyword1'  => '诚信宣言',
            'keyword2'  => '无',
            'remark'    => [
                'value'     => "\n分享越多，支持和认可越多！！\n发送至朋友圈、微信群>>",
                'color'     => '#173177'
            ]
        ];
        (new OfficialAccounts())->templateMessage(session('userInfo.openid'), $templateId, $url, $msg);
    }

    /**
     * 每天前几个支持的推动消息
     * @param $userId
     * @param $publicityId
     * @param $fromUserName
     */
    public function like($userId, $publicityId, $fromUserName)
    {
        $templateId = '2yGz4TvwlSARy3dpJtfC-cWf8FyJcjyGjJW1QhmAyP8';
        $url = domain() . "/Frontend/315/html/index.html?publicity_id={$publicityId}&channel=default&v=" . time();
        $openid = DB::table('wx_user')->where('user_id', $userId)->value('openid');
        $msg = [
            'first'     => [
                'value'     => "恭喜！您的亲友 ". $fromUserName ." 刚支持了您的诚信宣言，赶紧来看看吧！\n",
                'color'     => '#FF2525'
            ],
            'keyword1'  => '见排行榜',
            'keyword2'  => '进行中',
            'remark'    => [
                'value'     => "\n一个支持，就是一个认同！\n继续喊亲友支持，为您的保险事业立信！>>",
                'color'     => '#173177'
            ]
        ];
        (new OfficialAccounts())->templateMessage($openid, $templateId, $url, $msg);
    }

    /**
     * 中奖通知
     */
    public function lotteryNotification()
    {
        $publicityId = DB::table('wx_315_publicity')->where('user_id', session('userInfo.user_id'))->value('publicity_id');
        $templateId = '4dlJ6vvX2AQ380E3aAq18E3-33wrm7KPApSgX_Xr3gg';
        $url = domain() . "/Frontend/315/html/index.html?publicity_id={$publicityId}&channel=default&v=" . time();
        $msg = [
            'first'     => [
                'value'     => "恭喜你中奖啦！\n",
                'color'     => '#FF2525'
            ],
            'keyword1'  => '315大抽奖',
            'keyword2'  => '现金红包',
            'remark'    => [
                'value'     => "\n叫好友来支持，获更多大奖！>>\n",
                'color'     => '#173177'
            ]
        ];
        (new OfficialAccounts())->templateMessage(session('userInfo.openid'), $templateId, $url, $msg);
    }
}