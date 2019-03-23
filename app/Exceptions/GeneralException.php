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

use Throwable;

/**
 * 一般性的异常
 * Class GeneralException
 * @package App\Exceptions
 */
class GeneralException extends CustomException
{

    protected $error_code = 10040;

    protected $message = '';

    protected $code = 400;


    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }


    /**
     * 处理异常数据
     */
    public function report()
    {
        //  dd($this->getMessage());
    }

    /**
     * 响应
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        $result = [
            'state'         => 0,
            'error_code'    => $this->error_code,
            'data'          => [],
            'message'       => $this->message
        ];
        return response()->json($result, $this->code);
    }

}