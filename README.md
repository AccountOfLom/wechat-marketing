<h2>Laravel 微信营销项目</h2>

### 简述
一个微信公众号的营销项目。
做营销项目，尤其是有着很多需要快速迭代的活动的项目，如何使各个活动之间的业务逻辑解耦是提高代码质量的一个关键。

- 本项目非 MVC 设计模式
- 基本思路：分层、解耦、内聚

#### 任务调度
营销活动中，有着大量的消息推送需要计划任务去执行，且这些活动和推送都只在一段期间内需要被执行，由此出发，做了如下封装对活动之间的任务解耦：
```php
//Kernel.php
protected function schedule(Schedule $schedule)
{
    //集合所有当前需要执行的任务并执行
    (new Business())->execute($schedule);
}
```

#### 异常处理

系统错误的全局捕获、自定义异常分别处理并自动响应，见 `app/Exceptions` 中的异常处理类，助手函数：
```php
//权限异常
throw_if_auth(true, 'message');
//参数异常
throw_if_param(true, 'message');
//一般性异常
throw_if_general(true, 'message');
```

#### 数据验证
```php
class ValidateTestController extends BaseController
{
    public function index()
    {
        //  返回所有错误消息   pay 为场景值
//        (new TestValidate())->setBail(false)->execute('pay');

        //返回第一条错误消息
        (new TestValidate())->execute('pay');
    }

}
```
