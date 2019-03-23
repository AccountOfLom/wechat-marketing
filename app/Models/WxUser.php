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

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WxUser extends BaseModel
{
    /**
     * 关联到模型的数据表
     * @var string
     */
    protected $table = 'wx_user';

    /**
     * 主键
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * 主键非自增
     * @var bool
     */
    public $incrementing = false;

    /**
     * 主键非 int
     * @var string
     */
    protected $keyType = 'string';

    /**
     * 自定义属性
     * @var array
     */
    protected $appends = ['vip_dif'];

    protected $fillable = [
        'nickname',
        'wx_nickname',
        'headimgurl',
        'languages',
        'country',
        'province',
        'city',
        'sex'
    ];




}
