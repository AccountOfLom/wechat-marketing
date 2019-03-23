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

// 公共函数


if (!function_exists('domain')) {
    /**
     * 获取当前环境域名
     * @return mixed
     */
    function domain() {
        return config('system.domain')[config('system.version')];
    }
};

if (!function_exists('throw_if_auth')) {
    /**
     * 权限异常
     * @param $result
     * @param $message
     */
    function throw_if_auth($result, $message) {
        throw_if($result, \App\Exceptions\AuthException::class, $message);
    }
}

if (!function_exists('throw_if_param')) {
    /**
     * 参数异常
     * @param $result
     * @param $message
     */
    function throw_if_param($result, $message) {
        throw_if($result, \App\Exceptions\ParamException::class, $message);
    }
}

if (!function_exists('throw_if_general')) {
    /**
     * 一般性异常
     * @param $result
     * @param $message
     */
    function throw_if_general($result, $message) {
        throw_if($result, \App\Exceptions\GeneralException::class, $message);
    }
}


if(!function_exists('xml_to_data'))
{
    /**
     * XML转数组
     * @param string $xml
     * @return mixed
     */
    function xml_to_data($xml)
    {
        $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return json_decode(json_encode($xml),true);
    }
}

if(!function_exists('is_json'))
{
    /**
     * 判断一个字符串是否为json
     * @param $string $xml
     * @return mixed
     */
    function is_json($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if(!function_exists('is_xml'))
{
    /**
     * 判断一个字符串是否为json
     * @param $string $xml
     * @return mixed
     */
    function is_xml($string)
    {
        $xml_parser = xml_parser_create();
        if(!xml_parse($xml_parser,$string,true)){
            xml_parser_free($xml_parser);
            return false;
        }
        return true;
    }
}

