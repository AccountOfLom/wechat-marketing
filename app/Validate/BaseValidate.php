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

namespace App\Validate;

use Illuminate\Http\Request;
use Validator;

/**
 * 数据校验类
 * Class BaseValidate
 * @package App\Validate
 */
class BaseValidate extends Validator
{

    /**
     * 校验规则
     * @var array
     */
    public $rules = [];

    /**
     * 错误信息
     * @var array
     */
    public $messages = [];

    /**
     * 场景
     * @var array
     */
    public $scene = [];


    /**
     * 规则属性中分配 bail 作为首规则，首次验证失败后将停止检查该属性的其它验证规则
     * @var string
     */
    public $bail = 'bail';

    public function __construct() {}

    /**
     * 是否返回所有错误信息
     * @param bool $state   true 为只返回一条 false 则返回全部
     * @return BaseValidate
     */
    public function setBail(bool $state): self
    {
        if (!$state) {
            $this->bail = '';
        }
        return $this;
    }

    /**
     * 统一校验方法
     * @param string $scene     场景
     * @param array $data       校验数据  未传则从 Request 中取值
     */
    public function execute(string $scene, array $data = [])
    {
        $data = $data ?: Request::capture()->all();
        $sceneRules = $this->getSceneRules($scene);
        $result = self::make($data, $sceneRules, $this->messages);
        if ($this->bail) {
            //抛出第一个错误信息
            $error = $result->errors()->first();
        } else {
            //抛出所有错误信息
            $error = json_encode($result->errors()->all());
        }
        throw_if_param(!empty($error), $error);
    }

    /**
     * 获取当前场景下的验证规则
     * @param string $scene
     * @return array
     */
    protected function getSceneRules(string $scene): array
    {
        throw_if_general(!array_key_exists($scene, $this->scene), $scene . ' scene no found');
        $sceneRules = [];
        foreach ($this->scene[$scene] as $key => $rule) {
            if (is_numeric($key)) {
                $sceneRules[$rule] = $this->buildRule($this->rules[$rule]);
            } else {
                $sceneRules[$key] = $this->buildRule($rule);
            }
        }
        return $sceneRules;
    }


    /**
     * 重构校验规则
     * @param $rules
     * @return mixed
     */
    protected function buildRule($rules)
    {
        foreach ($rules as $k => $v) {
            if (property_exists($this, $v)) {
                $rules[$k] = $this->$v;
            }
        }
        //加入 bail到首规则
        if ($this->bail) {
            array_unshift($rules, $this->bail);
        }
        return $rules;
    }
}