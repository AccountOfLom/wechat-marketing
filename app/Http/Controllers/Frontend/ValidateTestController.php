<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/3/23 
// +----------------------------------------------------------------------


namespace App\Http\Controllers\Frontend;

use App\Validate\Frontend\TestValidate;

/**
 * 校验类使用范例
 * Class ValidateTestController
 * @package App\Http\Controllers\Frontend
 */
class ValidateTestController extends BaseController
{
    public function index()
    {
        //  返回所有错误消息
//        (new TestValidate())->setBail(false)->execute('pay');

        //返回第一条错误消息
        (new TestValidate())->execute('pay');
    }

}