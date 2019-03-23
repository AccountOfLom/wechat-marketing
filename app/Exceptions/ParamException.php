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
 * 参数错误
 * Class ParamException
 * @package App\Exceptions
 */
class ParamException extends CustomException
{

    protected $error_code = 10030;

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
            'data'          => $request->all(),   //将所接收到的参数返回，便于前端页面数据回填
            'message'       => is_json($this->message) ? json_decode($this->message) : $this->message
        ];
        return response()->json($result, $this->code);
    }


}