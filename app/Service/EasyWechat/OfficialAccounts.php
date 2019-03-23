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

use App\Service\Ffmpeg;
use EasyWeChat\Factory;
use Illuminate\Http\Request;

/**
 * 公众号服务处理
 * Class OfficialAccounts
 * @package app\Wechat
 */
class OfficialAccounts
{
    /**
     * EasyWechat 实例
     * @var array
     */
    public $easyWechat;


    public function __construct()
    {
        $this->initEasyWechat();
    }

    /**
     * 实例化 easyWechat
     */
    protected function initEasyWechat()
    {
        $config = $this->initConfig();
        $this->easyWechat = Factory::officialAccount($config);
    }

    /**
     * 初始化配置
     * 1、动态设置授权后同步跳转的前端地址
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function initConfig()
    {
        // 配置参数
        $config = config('wechat');
        //如果传参中有前端回调地址,则设置为微信回调的参数
        if (Request::capture()->has('back_url')) {
            $backUrl = '?back_url=' . urlencode(Request::capture()->input('back_url'));
            $config['oauth']['callback'] .= $backUrl;
        }
        return $config;
    }


    /**
     * 根据media_id从微信服务器下载音频文件
     * @param $path  string  音频文件保存地址  例：/storage/upload/record/
     * @param $mediaId  string 微信语音文件id
     * @return string
     */
    public function downloadSpeexToMp3($mediaId, $path)
    {
        $stream = $this->easyWechat->media->getJssdkMedia($mediaId);
        //文件绝对地址
        $absolutePath = PUBLIC_PATH . $path;
        $fileName = md5($mediaId);
        $stream->saveAs($absolutePath, $fileName . '.speex');
        $result = (new Ffmpeg())->speexToMp3($absolutePath . $fileName);
        if ($result) {
            return  $path . $fileName . '.mp3';
        }
        return false;
    }

    /**
     * 发送模板消息
     * @param string $openId
     * @param string $templateId
     * @param string $url
     * @param array $msg
     */
    public function templateMessage(string $openId, string $templateId, string $url = '', array $msg)
    {
        $this->easyWechat->template_message->send([
            'touser' => $openId,
            'template_id' => $templateId,
            'url' => $url,
            'data' => $msg
        ]);
    }

    /**
     * 设置公众号菜单
     */
    public function setMenu()
    {
        $buttons = [
            [
                "type" => "view",
                "name" => "诚信宣言",
                "url"  => "https://www.baolian360.cn/Frontend/315/html/fill.html?channel=default"
            ],
            [
                "type" => "click",
                "name" => "保联夜听",
                "key"  => "baolianyeting"
            ],
            [
                "name"          => "点我",
                "sub_button"    => [
                    [
                        "type" => "view",
                        "name" => "保联app",
                        "url"  => "http://www.ehuimeng.com/Public/Uploads/app/download.html"
                    ],
                    [
                        "type" => "click",
                        "name" => "联系客服",
                        "key"  => "concatus"
                    ]
                ]
            ]
        ];
        return $this->easyWechat->menu->create($buttons);
    }

    /**
     * 生成二维码
     * @param $scene        场景
     * @param $expireDate   过期时间  * 天后
     * @return string
     */
    public function createSceneQrcode($scene, $expireDate)
    {
        try {
            $result = $this->easyWechat->qrcode->temporary($scene, $expireDate * 24 * 3600);
            return $this->easyWechat->qrcode->url($result['ticket']);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}