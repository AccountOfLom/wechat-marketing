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

namespace App\Http\Controllers\Frontend;

use App\Service\EasyWechat\Pay\Order;
use App\Validate\TestValidate;
use Validator;

class TestController extends BaseController
{
    /**
     * 微信支付测试
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pay()
    {
        // 支付成功后回调业务逻辑处理方法
        $callBackRoute = '\App\Http\Controllers\Frontend\TestController@ProvideService';

        $jsConfig = (new Order())
            ->setProvideService($callBackRoute)
            ->create(1, 'VIP');    // 支付金额 （分）, 商品名称


        //前端页面拉起微信支付控件所需参数
        $this->result['data'] = [
            'jsApiParameters'   => $jsConfig['jsApiParameters'],
            'editAddress'       => $jsConfig['editAddress']
        ];

//        return response()->json($this->result, 200);

        return view('test.pay', $this->result['data']);
    }


    /**
     * 支付成功后逻辑处理方法
     * @param $message 参数值示例： {"pay_id":87,"appid":"wxf78ca1151706487a","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1520903071","nonce_str":"5c9085148b708","openid":"oGrlv0XFfBbMhe2ViMEdMHHeCh1k","out_trade_no":"2019031913584452565656","result_code":"SUCCESS","return_code":"SUCCESS","sign":"1FEDDADC799F1D47BADAE548560996C9","time_end":"20190319135849","total_fee":"1","trade_type":"JSAPI","transaction_id":"4200000261201903199577319546"}
     */
    public function ProvideService($message)
    {
        //TODO 支付成功后的业务逻辑

        //修改支付记录，设置为 所购买服务生效
        // (new WxLogPayRepository())->setTakeEffect($message['pay_id]);
    }
}