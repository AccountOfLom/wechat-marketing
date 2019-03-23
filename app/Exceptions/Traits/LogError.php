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

namespace App\Exceptions\Traits;

use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait LogError
{
    public function writeLogError($exception)
    {
        try {
            $request = Request::capture();
            $params = $request->all() ?? [];
            $xmlParams = file_get_contents("php://input");
            if ($xmlParams) {
                $xmlParams = is_xml($xmlParams) ? xml_to_data($xmlParams) : $xmlParams;
                $params = array_merge($params, ['php://input' => $xmlParams]);
            }
            $params = json_encode($params);
            $requestInfo = [
                'user_id'       => session('userInfo.user_id') ?? 0,
                'ip'            => $request->ip(),
                'request_time'  => date('Y-m-d H:i:s', time()),
                'route'         => $request->path(),
                'url'           => $request->fullUrl(),
                'method'        => $request->method(),
                'params'        => $params,
                'header'        => json_encode($request->header()),
                'error_message' => $exception->getMessage(),
                'file'          => $exception->getFile(),
                'line'          => $exception->getLine()
            ];
            DB::table('wx_log_error')->insert($requestInfo);
        } catch (\Exception $e) {
//            \Log::error($e->getMessage());
        }
    }
}