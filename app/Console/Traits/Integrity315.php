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

namespace App\Console\Traits;

use App\Http\Controllers\Frontend\Integrity315\LikeController;
use App\Service\EasyWechat\OfficialAccounts;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 诚信315活动
 * Class Integrity315
 */
trait Integrity315
{
    /**
     * 活动期间
     * 属性名必须为 trait 名 . Valid
     * @var array
     */
    protected $Integrity315Valid = ['2019-03-12', '2019-03-20'];

    /**
     * 属性名必须为 trait 名 . Methods
     * @var array   方法列表
     */
    protected $Integrity315Methods = [
        'activityStartIntegrity315'         => ['2019-03-12'],
        'achievementIntegrity315'           => ['2019-03-12', '2019-03-15'],
        'virtualSupportIntegrity315'        => ['2019-03-12', '2019-03-15'],
        'nextVirtualSupportIntegrity315'    => ['2019-03-12', '2019-03-15'],
    ];

    /**
     * 活动开始
     * @param $schedule
     */
    protected function activityStartIntegrity315($schedule)
    {
        $schedule->call(function () {
            try {
                $OfficialAccounts = new OfficialAccounts();
                $templateId = 'UwrXGuizNo32_HPHruGeKJVLxSI4NBMP89uVFtTi3eU';
                $url = domain() . "/Frontend/315/html/fill.html?v=" . time();
                Db::table('wx_user')
                    ->where('is_follow', 1)
                    ->select('user_id', 'openid')
                    ->orderBy('user_id')
                    ->chunk(500, function ($users) use ($templateId, $url, $OfficialAccounts) {
                        foreach ($users as $user) {
                            $msg = [
                                'first' => [
                                    'value' => "诚信315，做诚信保险人！发起诚信宣言，为自己立信，打造客户诚信口碑！\n",
                                    'color' => '#FF2525'
                                ],
                                'keyword1' => '315诚信宣言',
                                'keyword2' => '无',
                                'keyword3' => '已更新',
                                'keyword4' => date('Y-m-d', time()),
                                'remark' => [
                                    'value' => "\n分享越多，支持和认可越多！\n发送至朋友圈、微信群>>",
                                    'color' => '#173177'
                                ]
                            ];
                            $OfficialAccounts->templateMessage($user->openid, $templateId, $url, $msg);
                        }
                    });
            } catch (\Exception $e) {

            }
        });
    }

    /**
     * 每日活动成绩
     * @param $schedule
     */
    protected function achievementIntegrity315($schedule)
    {
        $schedule->call(function () {
            try {
                $OfficialAccounts = new OfficialAccounts();
                $templateId = '2yGz4TvwlSARy3dpJtfC-cWf8FyJcjyGjJW1QhmAyP8';
                DB::table('wx_315_like_publicity as a')
                    ->join('wx_user as b', 'a.for_user_id', '=', 'b.user_id')
                    ->where('b.is_follow', 1)
                    ->whereDate('a.created_at', date('Y-m-d', strtotime(' - 1 day')))
                    ->select(DB::raw('count(*) as yesterday_total, openid, for_user_id, publicity_id'))
                    ->groupBy('a.for_user_id', 'openid', 'publicity_id')
                    ->havingRaw('yesterday_total > 1')
                    ->orderBy('a.id')
                    ->chunk(500, function ($users) use ($templateId, $OfficialAccounts) {
                        foreach ($users as $user) {
                            $total = DB::table('wx_315_like_publicity')->where('for_user_id', $user->for_user_id)->count();
                            $url = domain() . "/Frontend/315/html/index.html?publicity_id={$user->publicity_id}&channel=default&v=" . time();

                            $msg = [
                                'first' => [
                                    'value' => "恭喜！您的诚信宣言昨日获得{$user->yesterday_total}个亲友支持！累计亲友支持{$total}个\n",
                                    'color' => '#173177'
                                ],
                                'keyword1' => '无',
                                'keyword2' => '进行中',
                                'remark' => [
                                    'value' => "\n继续分享，向亲友客户晒出您的诚信风采！>>",
                                    'color' => '#FF2525'
                                ]
                            ];
                            $OfficialAccounts->templateMessage($user->openid, $templateId, $url, $msg);
                        }
                    });
            } catch (\Exception $e) {

            }
        })->dailyAt('08:00');
    }

    /**
     * 虚拟支持
     * @param $schedule
     */
    public function virtualSupportIntegrity315($schedule)
    {
        $schedule->call(function () {
            try {
                //半小时后再次通知的用户
                $nextToLike = [];
                DB::table('wx_315_publicity as a')
                    ->leftJoin('wx_315_like_publicity as b', function ($join) {
                        $join->on('a.user_id', '=', 'b.for_user_id')
                            ->whereBetween('b.created_at', [date('Y-m-d', strtotime('- 1 day')), date('Y-m-d', time())]);
                    })
                    ->leftJoin('wx_user as c', 'a.user_id', '=', 'c.user_id')
                    ->select('openid', 'a.user_id', 'a.publicity_id')
                    ->groupBy('openid', 'a.user_id', 'a.publicity_id')
                    ->whereRaw('dlx_b.id is null')
                    ->orderBy('a.user_id')
                    ->chunk(200, function ($users) use (& $nextToLike){
                        foreach ($users as $user) {
                            array_push($nextToLike, $user);
                            $doLikeUser = DB::table('wx_user')
                                ->orderBy(DB::raw('rand()'))
                                ->select('user_id', 'nickname')
                                ->first();
                            (new LikeController())->index([
                                'publicity_id' => $user->publicity_id,
                                'user_id' => $doLikeUser->user_id,
                                'nickname' => $doLikeUser->nickname,
                                'message' => '诚信销售，投保无忧！'
                            ]);
                        }
                    });
                $key = 'virtualSupport_' . date('ymd', time());
                Cache::add($key, json_encode($nextToLike), 3600);
            } catch (\Exception $e) {

            }
        })->dailyAt('19:00');
    }


    /**
     * 当日第二次虚拟支持
     * @param $schedule
     */
    public function nextVirtualSupportIntegrity315($schedule)
    {
        $schedule->call(function () {
            try {
                $key = 'virtualSupport_' . date('ymd', time());
                $users = Cache::pull($key);
                if (!$users) {
                    return;
                }
                $users = json_decode($users);
                foreach ($users as $user) {
                    $doLikeUser = DB::table('wx_user')
                        ->orderBy(DB::raw('rand()'))
                        ->select('user_id', 'nickname')
                        ->first();
                    (new LikeController())->index([
                        'publicity_id' => $user->publicity_id,
                        'user_id' => $doLikeUser->user_id,
                        'nickname' => $doLikeUser->nickname,
                        'message' => '诚信销售，投保无忧！'
                    ]);
                }
            } catch (\Exception $e) {

            }
        })->dailyAt('19:30');
    }
}