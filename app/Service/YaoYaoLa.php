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

namespace App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * 摇摇啦 微信营销平台
 * @domain http://www.yaoyaola.cn
 * Class YaoYaoLa
 * @package App\Service
 */
class YaoYaoLa
{
    /**
     * 单红包创建请求地址
     * @var string
     */
    private $createOneUrl = 'https://www.yaoyaola.cn/index.php/exapi/hbticket?';

    /**
     * 红包URL地址
     * @var string
     */
    private $redPocketUrl = 'https://www.yaoyaola.cn/index.php/exapi/gethb?';

    /**
     * 创建单红包
     * @param $param = [
     *      money       红包金额
     *      expire      红包超时时间，单位为秒，不指定则默认60秒
     *      title       红包活动名称(不能超过10个汉字或32个字符)
     *      type        红包类型，0使用红包接口，1表示使用企业付款接口
     *      sendname    红包发送方名称(不能超过10个汉字或32个字符)
     *      wishing 	红包祝福语
     *      rurl        红包领取结果跳转url
     *      cburl       服务器通知url
     * ];
     * @return array  [红包领取地址, 订单号]
     * @throws \Exception
     */
    public function createOne(array $param): array
    {
        $reqtick = time();
        $uid = config('system.yaoyaola.uid');
        $orderInd = $this->createOrderId();
        $yaoyaolaKey = md5(config('system.yaoyaola.apiKey'));
        $sign = md5($uid . $param['type'] . $orderInd . $param['money'] . $reqtick . $yaoyaolaKey);
        $data = [
            'uid'       => $uid,
            'type'      => $param['type'],
            'orderid'   => $orderInd,
            'money'     => $param['money'],
            'expire'    => $param['expire'],
            'sendname'  => $param['sendname'],
            'title'     => $param['title'],
            'wishing'   => $param['wishing'],
            'reqtick'   => $reqtick,
            'sign'      => $sign,
            'rurl'      => urlencode($param['rurl']),
            'cburl'     => $param['cburl']
        ];
        $firstKey = key($data);
        foreach ($data as $k => $v) {
            $k == $firstKey ? $this->createOneUrl .= "$k=$v" : $this->createOneUrl .= "&$k=$v";
        }
        $result = file_get_contents($this->createOneUrl);
        if (!$result) {
            throw new \Exception('红包创建接口调用失败');
        }
        $redPocket = json_decode($result);
        if ($redPocket->errcode != 0) {
            throw new \Exception('红包创建失败:'.$result);
        }
        $ticket = $redPocket->ticket;
        return [
            'url'       => $this->redPocketUrl . 'uid=' . $uid . '&ticket=' . $ticket,
            'order_id'  =>$orderInd,
            'ticket'    =>$ticket
        ];
    }

    /**
     * 批量生成红包
     */
    public function createMany()
    {
        //TODO 调用摇摇啦多红包创建接口
    }

    /**
     * 生成订单号
     * @return string
     */
    private function createOrderId()
    {
        return date('YmdHis') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
    }


}