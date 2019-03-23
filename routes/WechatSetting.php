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

// 微信设置相关 需验证tokrn

use App\Service\EasyWechat\OfficialAccounts;
use App\Service\EasyWechat\OfficialAccountsMessage;
use \Illuminate\Http\Request;


//设置公众号菜单
Route::put('OfficialAccountsMenu', function (OfficialAccounts $officialAccounts) {
    return $officialAccounts->setMenu();
});

// 上传素材图片， 获得 media_id
Route::post('materialImage', function (OfficialAccountsMessage $officialAccountsMessage) {
    return $officialAccountsMessage->materialImage();
});


// 生成场景二维码
Route::post('sceneQrcode', function (OfficialAccounts $officialAccounts, Request $request) {
    return $officialAccounts->createSceneQrcode($request->post('scene'), $request->post('expireDate'));
});



