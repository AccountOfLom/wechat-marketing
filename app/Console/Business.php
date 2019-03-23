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

namespace App\Console;


use App\Console\Traits\Integrity315;

/**
 * 集合要执行的计划任务
 * Class Business
 * @package App\Console
 */
class Business
{
    use Integrity315;

    /**
     * 活动
     * @var array
     */
    private $businessList = [
        'Integrity315',
    ];

    /**
     * 要执行的任务方法
     * @var array
     */
    private $methods = [];

    /**
     * 执行各业务在有效期间的任务
     * @param $schedule
     */
    public function execute($schedule)
    {
        $this->methodMerge();
        foreach ($this->methods as $method) {
            $this->$method($schedule);
        }
    }

    /**
     * 合并所有符合条件的方法
     */
    private function methodMerge(): void
    {
        foreach ($this->businessList as $business) {
            //过滤已过期的 trait 任务集
            if (!$this->businessvalid($business)) {
                continue;
            }
            //添加处于有效期的任务方法
            $this->addValidMethod($business);
        }
    }

    /**
     * 校验是否在活动期限内
     * @param $business
     * @return bool
     */
    private function businessValid(string $business): bool
    {
        $validDate = $business . 'Valid';
        if (!property_exists($this, $validDate)) {
            return false;
        }
        if (!$this->isValid($this->$validDate)) {
            return false;
        }
        return true;
    }

    /**
     * 将处于有效期的任务方法 添加到 methods 属性
     * @param $business
     */
    private function addValidMethod(string $business): void
    {
        $methods = $business . 'Methods';
        if (!property_exists($this, $methods)) {
            return;
        }
        foreach ($this->$methods as $method => $validDate) {
            if (!method_exists($this, $method)) {
                continue;
            }
            if (!$this->isValid($validDate)) {
                continue;
            }
            array_push($this->methods, $method);
        }
        return;
    }


    /**
     * 校验今天是否在活动期限内
     * @param array $date = ['0000-00-00', '0000-00-00'] || ['0000-00-00']
     * @return bool
     */
    private function isValid(array $date): bool
    {
        $today = date('Y-m-d', time());
        //未填写有效期间，为有效
        if (empty($date)) {
            return true;
        }
        if (count($date) == 1) {
            return $today == $date[0];
        }

        if ($today < $date[0] || $today > $date[1]) {
            return false;
        }
        return true;
    }
}