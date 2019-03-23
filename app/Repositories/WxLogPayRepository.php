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

namespace App\Repositories;

use App\Models\WxLogPay;
use App\Models\WxUser;
use Illuminate\Http\Request;

/**
 * 支付日志
 * Class WxLogPayRepository
 * @package App\Repositories
 */
class WxLogPayRepository extends BaseRepository
{
    protected $wxLogPayModel;

    public function __construct()
    {
        parent::__construct();
        $this->wxLogPayModel = new WxLogPay();
    }

    /**
     * 保存支付订单
     * @param array $orderInfo
     * @return int
     */
    public function storeOrder(array $orderInfo): int
    {
        $orderInfo['amount'] = $orderInfo['total_fee'];
        $orderInfo['remark'] = $orderInfo['remark'] ?? $orderInfo['body'];
        $orderInfo['user_id'] = session('userInfo.user_id');
        $log = WxLogPay::create($orderInfo);
        return $log->pay_id;
    }

    /**
     * 设置一笔支付记录为是否已生效 （所购买的服务是否已发放）
     * @param int $payId
     * @param bool $state
     * @return bool
     */
    public function setTakeEffect(int $payId, $state = true): bool
    {
        return $this->wxLogPayModel->where('pay_id', $payId)->update(['is_take_effect' => (int) $state]);
    }


}