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

namespace App\Service\EasyWechat;

use App\Repositories\WxUserRepository;
use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Http\Request;

/**
 * 消息处理
 * Class OfficialAccountsMessage
 * @package App\Service\EasyWechat
 */
class OfficialAccountsMessage extends OfficialAccounts
{
    protected $defaultMsg = '亲，来者是客，请随便坐~';

    /**
     * 消息入口
     */
    public function entrance()
    {
        $this->easyWechat->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    return $this->onEvent($message);
                    break;
                case 'text':
                    return $this->defaultMsg;
                    break;
                case 'image':
                    return $this->defaultMsg;
                    break;
                case 'voice':
                    return $this->defaultMsg;
                    break;
                case 'video':
                    return $this->defaultMsg;
                    break;
                case 'location':
                    return $this->defaultMsg;
                    break;
                case 'link':
                    return $this->defaultMsg;
                    break;
                case 'file':
                    return $this->defaultMsg;
                // ... 其它消息
                default:
                    return $this->defaultMsg;
                    break;
            }
        });
        $response = $this->easyWechat->server->serve();
        $response->send();
        exit;
    }

    /**
     * 被动接受事件消息
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140454
     * @param array $data 事件消息数据
     * @return string
     */
    protected function onEvent($data)
    {
        $method = 'on' . $data['Event'];
        if (method_exists($this, $method))
            return $this->{$method}($data);
        return 'success';
    }

    /**
     * 处理点击菜单事件
     * @return string
     */
    protected function onClick($data)
    {
        $method = 'on' . $data['EventKey'];
        if(method_exists($this, $method))
            return $this->{$method}();
        return '你点击了菜单';
    }



    /**
     * 点击事件响应素材
     * 上传素材图片， 获得 media_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function materialImage()
    {
        $request = Request::capture();
        try {
            if (!$request->hasFile('image')) {
                throw new \Exception('image 图片文件为空');
            }
            $image = $request->file('image');
            $imgPath = UPLOAD_FILE_PATH . $image->store('upload/image');
            $result = $this->easyWechat->material->uploadImage($imgPath);
            unlink($imgPath);
        } catch (\Exception $e) {
            $result = [
                'state'         => 0,
                'message'       => $e->getMessage()
            ];
        }
        return response()->json($result, 201);
    }

    /**
     * 点击回复 保联夜听 公众号二维码
     */
    protected function onBaolianyeting()
    {
        return new Image('vHNbWr4p4gK_7yNDL4wiWj_ncT5jWfQXUHra4xvEfs8');
    }

    /**
     * 点击联系客服按钮  eventKey
     */
    protected function onConcatus()
    {
        return new Image('vHNbWr4p4gK_7yNDL4wiWn2nkwmiduIqFqgUpYxHiVI');
    }


    /**
     * 处理用户关注公众号事件
     * @param $data
     * @return string
     */
    protected function onSubscribe($data)
    {
        $userInfo = $this->easyWechat->user->get($data['FromUserName']);
        (new WxUserRepository())->toSubscribe($userInfo);

        if (!empty($data['EventKey'])) {
            return $this->onScanSubscribe($data['EventKey'], $userInfo);
        }

        return "欢迎老师！我们是致力于提高保险伙伴展业业绩的平台，已有30万伙伴加入！\n  \n 315诚信宣言：展示自己的诚信理念，让客户买保险更信任你！<a href='https://www.baolian360.cn/Frontend/315/html/fill.html'>马上发起>>></a>";
    }

    /**
     * 扫描二维码产生的关注事件
     * @param $scene   场景
     * @param $openUserInfo
     * @return string
     */
    protected function onScanSubscribe($scene, $openUserInfo)
    {
        (new WxUserRepository())->toScanSubscribe($openUserInfo);
        return $this->sceneQrcodeMessage($scene);
    }


    /**
     * 处理取消关注事件
     * @return mixed
     */
    protected function onUnsubscribe($data)
    {
        $userInfo = $this->easyWechat->user->get($data['FromUserName']);
        (new WxUserRepository())->unSubscribe($userInfo);
        return '你取消关注了公众号';

    }

    /**
     * 处理已经关注的扫码事件
     * @param $data
     * @return string
     */
    protected function onScan($data)
    {
        switch ($data['EventKey']) {
            case 'integrity315':
                $result = new Image('vHNbWr4p4gK_7yNDL4wiWg6Jm8Lkn18ldl2Ow4LyxoY');
                break;
            default:
                $result = '';
                break;
        }
        return $result;
    }

    /**
     * 处理上报位置事件
     * @return string
     */
    protected function onLocation($data)
    {
        return 'success';
    }

    /**
     * 扫不同场景下的二维码
     * 响应对应场景的消息
     * @param $scene
     * @return mixed
     */
    protected function sceneQrcodeMessage($scene)
    {
        // qrscene_  为微信拼接的前缀
        switch ($scene) {
            case 'qrscene_integrity315':
                $result = new Image('vHNbWr4p4gK_7yNDL4wiWg6Jm8Lkn18ldl2Ow4LyxoY');
                break;
            default:
                $result = '';
                break;
        }
        return $result;
    }

}