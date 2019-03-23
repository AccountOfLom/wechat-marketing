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

/**
 * 微信相关配置
 */
return [
    'app_id'            => env('WX_APP_ID'),            // 公众号  绑定支付的APPID（必须配置，开户邮件中可查看）
    'secret'            => env('WX_SECRET'),
    'token'             => env('WX_TOKEN'),
    'response_type'     => 'array',
    'aes_key'           => env('WX_AES_KEY'),           //加解密密钥
    'oauth' => [
        'scopes'   => ['snsapi_userinfo'],              //授权类型
        'callback' => '/auth/callback',             //获取code后的回调
    ],
    'merchant_id'       => env('WX_MERCHANT_ID'),       // 商户号（必须配置，开户邮件中可查看）
    'mch_id'            => env('WX_MCH_ID'),
    'merchant_key'      => env('WX_MERCHANT_KEY'),      // 商户支付密钥  API 密钥
    'key'               => env('WX_PAY_KEY'),           // 商户支付密钥  API 密钥
    'app_secret'        => env('WX_APP_SECRET'),        // 公众帐号secert
    'cert_path'         => realpath('../cert').'/apiclient_cert.pem',                //商户证书
    'key_path'          => realpath('../cert').'/apiclient_key.pem',                 //证书私钥

];
