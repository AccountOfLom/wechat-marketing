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

namespace App\Repositories;

use App\Models\WxUser;
use Illuminate\Support\Str;

class WxUserRepository extends BaseRepository
{
    protected $wxUserModel;

    public function __construct()
    {
        parent::__construct();
        $this->wxUserModel = new WxUser();
    }

    /**
     * 根据 unionid 获取用户信息
     * @param string $unionid
     */
    public function getUserByUnionid(string $unionid)
    {
        $userInfo = $this->wxUserModel->where('unionid', $unionid)->first();
        return $userInfo;
    }

    /**
     * 新增用户
     * @param array $data
     * @return string
     */
    public function store(array $data): string
    {
        $saveData = [
            'nickname'      => $data['nickname'],
            'wx_nickname'   => $data['nickname'],
            'headimgurl'    => $data['headimgurl'],
            'languages'     => $data['language'],
            'country'       => $data['country'],
            'province'      => $data['province'],
            'city'          => $data['city'],
            'sex'           => $data['sex']
        ];

        $wxUser = new WxUser($saveData);
        $wxUser->user_id = (string) Str::uuid();
        $wxUser->openid = $data['openid'];
        $wxUser->unionid = $data['unionid'];

        if (array_key_exists('is_follow', $data)) {
            $wxUser->is_follow = $data['is_follow'];
            $wxUser->subscribe_time = time();
        }

        $wxUser->save($saveData);
        return $wxUser->user_id;
    }

    /**
     * 微信用户登录后更新 昵称
     * @param array $data
     * @return bool
     */
    public function updateNickName(array $data)
    {
        $result = $this->wxUserModel->where('unionid', $data['unionid'])->update(['wx_nickname'   => $data['nickname']]);
        return $result;
    }

    /**
     * 处理用户关注公众号事件
     * @param $userInfo
     * @return bool
     */
    public function toSubscribe(array $userInfo)
    {
        $user = $this->getUserByMap([
            'openid'    => $userInfo['openid'],
            'unionid'   => $userInfo['unionid']
        ]);
        if ($user) {
            return $this->wxUserModel->where('user_id', $user->user_id)->update([
                'is_follow'         => 1,
                'subscribe_time'    => time()
            ]);
        }
        $userInfo['is_follow'] = 1;
        $this->store($userInfo);
        return true;
    }

    /**
     * 设置一个用户为取消关注
     * @param $userInfo
     * @return bool
     */
    public function unSubscribe($userInfo)
    {
        $user = $this->getUserByMap([
            'openid'    => $userInfo['openid'],
        ]);
        if ($user) {
            return $this->wxUserModel->where('user_id', $user->user_id)->update([
                'is_follow'         => 0,
                'updated_at'      => date('Y-m-d H:i:s', time())
            ]);
        }
        return true;
    }

    /**
     * 设置一个用户为扫码关注
     * @param $userInfo
     * @return bool
     */
    public function toScanSubscribe(array $userInfo): bool
    {
        $user = $this->getUserByMap([
            'openid'    => $userInfo['openid'],
            'unionid'   => $userInfo['unionid']
        ]);
        if ($user) {
            $user->is_qrscene = 1;
            $user->save();
        }
        return true;
    }

    /**
     * 根据查询条件获取单个用户信息
     * @param $map
     * @return mixed
     */
    public function getUserByMap($map)
    {
        return $this->wxUserModel->where($map)->first();
    }




}