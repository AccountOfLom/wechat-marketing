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

//315活动摇摇啦红包领取回调
Route::any('yaoyaolaCburl315', '\App\Http\Controllers\Frontend\Integrity315\AwardController@notifyCallback');

//接收微信事件推送
Route::any('WechatMessage', '\App\Service\EasyWechat\OfficialAccountsMessage@entrance');

//微信支付结果通知
Route::post('payResult', '\App\Service\EasyWechat\Pay\Result@index');
