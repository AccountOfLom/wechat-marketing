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

namespace App\Validate\Backend;

use App\Validate\BaseValidate;

/**
 * 验证测试 范例
 * Class TestValidate
 * @package App\Validate
 */
class TestValidate extends BaseValidate
{
    /**
     * 自定义规则
     * 命名 *Rule
     * @var \Closure
     */
    public $testRule;

    public function __construct()
    {
        parent::__construct();

        /**
         * 自定义规则范例
         * @param $attribute    string  被校验的字段名
         * @param $value        mixed   字段值
         * @param $fail         string  回调函数
         * @return mixed
         */
        $this->testRule = function ($attribute, $value, $fail) {
            if (strpos($value, '敏感词') !== false) {
                return $fail($attribute . '包含了系统禁用的敏感词');
            }
        };
    }

    public $rules = [
        'pay_id'        => ['required'],
        'total_fee'     => ['required', 'min:1', 'Integer'],
        'body'          => ['required', 'testRule'],    // testRule 为自定义规则
    ];


    public $messages = [
        'total_fee.required'    => '请输入金额',
        'total_fee.mix'         => '金额不能小于0.01元',
        'body.required'         => '请输入商品名称',
    ];

    /**
     * 场景验证规则
     * @var array
     */
    public $scene = [
        'refund' => [
            'pay_id'    => ['required', 'Integer'],   // 覆盖规则
        ],
        'pay'   => [
            'total_fee',    // 使用 $this->rules 规则
            'body',
        ],
    ];

}