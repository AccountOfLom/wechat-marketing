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

use App\Http\Controllers\Controller;
use App\Service\EasyWechat\OfficialAccounts;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * 响应数据结构
     * @var array
     */
    public $result = [
        'state'         => 1,
        'error_code'    => 0,
        'data'          => [],
        'message'       => 'success'
    ];

    /**
     * http 状态码
     * @var int
     */
    public $httpCode = 200;

    protected $created_at;

    protected $updated_at;

    /**
     * EasyWechat 实例
     * @var array
     */
    protected $OfficialAccountsServer;

    public function __construct()
    {
        parent::__construct();
        $date = date('Y-m-d H:i:s', time());
        $this->created_at = $date;
        $this->updated_at = $date;

        $this->OfficialAccountsServer = (new OfficialAccounts())->easyWechat;
    }
}