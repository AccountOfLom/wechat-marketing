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

use \Illuminate\Support\Facades\Cache;
use \App\Http\Controllers\Frontend\Integrity315\PublicityController;
use \Illuminate\Http\Request;

// 315 活动

Route::middleware('userToken')->group(function () {
    // 宣传卡信息的创建和修改
    Route::match(['post', 'put'], 'publicity', 'PublicityController@save');

    // 当前发起人数
    Route::get('total', 'PublicityController@total');

    // 获取公司列表
    Route::get('companyList', 'PublicityController@companyList');

    // 获取自己发起的宣传卡id
    Route::get('selfPublicity', 'PublicityController@selfPublicity');

    // 支持宣传卡
    Route::post('like', 'LikeController@index');

    // 抽奖页数据
    Route::get('drawLottery', 'AwardController@drawLottery');

    // 已抽奖通知
    Route::put('lotteryNotification', 'AwardController@lotteryNotification');

    // 排行榜
    Route::get('dailyRanking', 'RankingController@daily');

});

// 数据展示
Route::get('showData', 'ShowDataController@index');

// 渠道信息
Route::get('channelList', 'PublicityController@channelList');

// 红包跳转
Route::any('redPocket', 'App\Http\Controllers\Frontend\Integrity315\AwardController@getRedPocket');


