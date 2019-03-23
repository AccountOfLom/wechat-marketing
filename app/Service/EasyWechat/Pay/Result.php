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

use App\Exceptions\CustomException;
use App\Models\WxLogPay;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Redis;

/**
 * 支付结果
 * Class Result
 * @package App\Service\EasyWechat\Pay
 */
class Result
{
    const NOTIFY_STATUS_SUCCESS = 1;    //回调状态失败
    const PAY_STATE_SUCCESS = 1;        //支付失败
    const NOTIFY_STATUS_FAIL = 2;       //回调状态失败
    const PAY_STATE_FAIL = 3;           //支付失败

    private $config;

    public function index()
    {
        $this->config = config('wechat');
        $payment = Factory::payment($this->config);

        $response = $payment->handlePaidNotify(function ($message, $fail) {
            try {
                if ($message['appid'] != $this->config['app_id'] ||
                    $message['mch_id'] != $this->config['mch_id']) {
                    throw new \Exception('pay result: appid or mch_id error');
                }
                $log = (new WxLogPay())->where('out_trade_no', $message['out_trade_no'])->first();
                if (!$log) {
                    throw new \Exception('pay result: pay log no found');
                }
                if ($log->amount != $message['cash_fee']) {
                    throw new \Exception('pay result: amount error');
                }
                if ($message['result_code'] != 'SUCCESS' || $message['return_code'] != 'SUCCESS') {
                    //更新日志数据
                    $log->pay_state = self::PAY_STATE_FAIL;
                    $log->notify_status = self::NOTIFY_STATUS_FAIL;
                    $log->save();
                    throw new \Exception('pay result: result no success');
                }
                //业务逻辑处理
                $this->ProvideService($log->pay_id, $message);

                //更新日志数据
                $log->transaction_id = $message['transaction_id'] ?? '';
                $log->pay_state = self::PAY_STATE_SUCCESS;
                $log->notify_status = self::NOTIFY_STATUS_SUCCESS;
                $log->save();
                return true;
            } catch (\Exception $e) {
                (new CustomException())->writeLogError($e);
                $fail($e->getMessage());
            }
        });
        return $response;
    }

    /**
     * 业务逻辑处理
     * @param int $payId
     * @param array $message
     * @throws \Exception
     */
    public function ProvideService(int $payId, array $message)
    {
        $route = explode('@', $this->getProvideServiceToRedis($payId));
        $class = new \ReflectionClass($route[0]);
        $instance = $class->newInstanceArgs();
        if (!method_exists($instance, $route[1])) {
            throw new \Exception($route[1] . ' method no found');
        }
        $message['pay_id'] = $payId;
        $instance->{$route[1]}($message);
    }

    //获取本次支付的处理路由地址
    private function getProvideServiceToRedis($logId)
    {
        return Redis::get('ProvideService_' . $logId);
    }


}