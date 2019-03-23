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

namespace App\Service\EasyWechat\Pay;
use App\Repositories\WxLogPayRepository;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * 支付订单
 * Class Pay
 * @package App\Service\EasyWechat
 */
class Order
{
    /**
     * 微信支付实例
     * @var
     */
    private $payment;

    /**
     * 支付配置
     * @var array
     */
    private $config = [];

    /**
     * 发起支付的方式
     * @var array
     */
    private $tradeType = [
        'JSAPI',   // JSAPI支付（或小程序支付）  （默认）
        'NATIVE',  // 原生
        'APP',    // app支付
        'MWEB'    // H5支付
    ];

    /**
     * 支付订单信息
     * @var array
     */
    private $orderInfo = [];

    /**
     * 支付成功后的逻辑处理路由
     * @var
     */
    private $ProvideService;


    public function __construct($config = [])
    {
        if (array_key_exists('sandbox', $config)) {
            $this->config['sandbox'] = $config['sandbox'];
        }
        $this->setPayment();

        $this->orderInfo['trade_type'] = 'JSAPI';
        $this->orderInfo['out_trade_no'] = $this->createOutTradeNo();
        $this->orderInfo['openid'] = session('userInfo.openid');
        $this->orderInfo['notify_url'] = domain() . '/notify/payResult';
    }


    /**
     * 获取支付实例
     * @return void
     */
    private function setPayment(): void
    {
        $this->config = config('wechat');
        $this->payment = Factory::payment($this->config);
    }


    /**
     * 设置支付方式，默认为 JSAPI
     * @param string $type
     * @return Order
     */
    public function setTradeType(string $type): self
    {
        throw_if_param(!in_array($type, $this->tradeType), 'trade_type error');
        $this->orderInfo['trade_type'] = $type;
        return $this;
    }

    /**
     * 支付结果通知地址
     * @param string $notifyUrl
     * @return Order
     */
    public function setNotifyUrl(string $notifyUrl): self
    {
        $this->orderInfo['notify_url'] = $notifyUrl;
        return $this;
    }

    /**
     * 设置支付成功后的逻辑处理路由
     * @param string $route
     * @return Order
     */
    public function setProvideService(string $route): self
    {
        $this->validateProvideServiceRoute($route);
        $this->ProvideService = $route;
        return $this;
    }

    /**
     * H5 支付，公众号支付，扫码支付 统一下单
     * @param int $totalFee     支付金额  单位 （分）
     * @param string $body      商品名
     * @return array
     */
    public function create(int $totalFee, string $body): array
    {
        DB::beginTransaction();
        try {
            $this->orderInfo['total_fee'] = $totalFee;
            $this->orderInfo['body'] = $body;
            //保存到支付日志表
            $logId = (new WxLogPayRepository())->storeOrder($this->orderInfo);
            $this->storeProvideServiceToRedis($logId);
            $result = $this->payment->order->unify($this->orderInfo);
            if ($result['return_code'] != 'SUCCESS') {
                throw new \Exception('Wechat pay order create be fail: ' . json_encode($result));
            }
            $response['pay_id'] = $logId;
            $response['jsApiParameters'] = $this->getJsConfig($this->orderInfo['trade_type'], $result['prepay_id']);
            $response['editAddress'] = $this->payment->jssdk->shareAddressConfig($this->getAccessTokenFromRedis(), false);
            DB::commit();
            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            throw_if(true, \Exception::class, $e->getMessage());
        }
    }

    /**
     * 生成支付 JS 配置
     * @param string $tradeType     支付方式
     * @param string $prepayId      微信预付订单号
     * @return array
     */
    private function getJsConfig(string $tradeType, string $prepayId): array
    {
        $config = [];
        switch ($tradeType) {
            case 'JSAPI':
                $config = $this->payment->jssdk->bridgeConfig($prepayId, false);
                break;
            case 'APP':
                $config = $this->payment->jssdk->appConfig($prepayId);
                break;
            default: break;
        }
        return $config;
    }


    /**
     * 生成商户订单号
     * @return string
     */
    private function createOutTradeNo(): string
    {
        return date('YmdHis') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
    }

    //保存本次支付成功后的处理路由地址
    private function storeProvideServiceToRedis(int $logId): void
    {
        Redis::set('ProvideService_' . $logId, $this->ProvideService);
    }

    /**
     * 读取 access_token
     * @return mixed
     */
    private function getAccessTokenFromRedis()
    {
        return Redis::get('access_token_' . session('userInfo.user_id'));
    }


    /**
     * 校验回调处理方法路由
     * @param string $route
     */
    private function validateProvideServiceRoute(string $route): void
    {
        $path = explode('@', $route);
        throw_if_param(count($path) != 2, 'Provide Service value error');
        $class = new \ReflectionClass($path[0]);
        $instance  = $class->newInstanceArgs();
        throw_if_param(!method_exists($instance, $path[1]), 'Provide Service method no found');
    }

}