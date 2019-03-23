<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://wx.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张柏川 <buff_collector@aliyun.com>
// +----------------------------------------------------------------------

Route::middleware('userToken')->group(function () {
    //获取用户信息
    Route::get('getUserInfo', 'UserController@getUserInfo');
    //保存用户信息 & 推送设置
    Route::post('store', 'UserController@store');
    //上传头像、二维码
    Route::post('uploadImg', 'UserController@uploadImg');

    //升级vip
    Route::post('upgrade', 'MemberController@upgrade');

    //获取文章内容
    Route::get('getArticelContent', 'ArticleController@getArticelContent');

    //访客数量
    Route::get('visitorCount','VisitorController@visitorCount');

    //访客列表
    Route::get('visitorList','VisitorController@visitorList');
});

//获取文章列表
Route::get('getArticelList', 'ArticleController@getArticelList');

//记录阅读结束时间
Route::get('setReadEndTime', 'ArticleController@setReadEndTime');

//获取公司列表
Route::post('companyList', 'UserController@companyList');

//获取省市区
Route::post('getRegion', 'UserController@getRegion');


Route::get('test', 'UserController@test');
Route::get('testCache', 'UserController@testCache');