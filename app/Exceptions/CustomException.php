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

namespace App\Exceptions;

use App\Exceptions\Traits\LogError;
use Exception;

/**
 * 自定义异常基类
 * Class CustomException
 * @package App\Exceptions
 */
class CustomException extends Exception
{
    use LogError;
}