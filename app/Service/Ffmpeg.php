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

namespace App\Service;

/**
 * 音频和视频转换，缩略图生成器和元数据编辑器包
 * Class Ffmpeg
 * @package App\Service
 */
class Ffmpeg
{
    /**
     * 音频转码
     * @param $pathFile string  文件目录 . 文件名（不含文件后缀）  ，绝对路径
     * @return bool
     */
    public function speexToMp3(string $pathFile): bool
    {
        $describe = [
            ['pipe','r'],
            ['pipe','w'],
            ['pipe','w']
        ];
        $pipes = null;
        $speex = $pathFile . '.speex';
        $wav = $pathFile . '.wav';
        $mp3 = $pathFile . '.mp3';
        proc_open("/usr/local/speex/bin/speex_decode  $speex $wav", $describe, $pipes);
        $proc = proc_open("/usr/local/ffmpeg/bin/ffmpeg -i $wav $mp3", $describe, $pipes);
        proc_close($proc);
        if (file_exists($mp3)) {
            unlink($speex);
            unlink($wav);
            return true;
        }
        return false;
    }
}