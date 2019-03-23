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


//根据用户 user_id 生成token   测试用
Route::get('testToken/{userId}', 'OfficialAccountsController@testToken');

//授权回调
Route::get('callback', 'OauthCallbackController@index');

//公众号授权
Route::get('OfficialAccounts', 'OfficialAccountsController@oauth');

//获取公众号JsSdk配置
Route::get('OfficialAccountsJsSdkConfig', 'OfficialAccountsController@JsSdkConfig');

Route::get('payTest', function () {
    return (new \App\Http\Controllers\Frontend\TestController())->pay();
})->middleware('userToken');


Route::any('test', '\App\Http\Controllers\Frontend\TestController@refund');

Route::any('t', function () {
    dd(date('ymd H:i:s', time()));
});


