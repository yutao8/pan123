<?php

namespace Yutao\Pan123;

use Yutao\Pan123\Api\Oss\File;
use Yutao\Pan123\Api\Oss\Upload;




/**
 * Class Client
 * @property File $file 文件操作类
 * @property Upload $upload 文件上传类
 * @method Api setUrlBase($url) 设置api域名
 * @method Api setCacheDir($dir) 设置缓存目录
 * @method array|string getError($type=null) 获取错误信息
 * @package Yutao\Pan123
 * */
class Oss extends Client
{
    const modulePath = 'Oss\\';
    public static function boot($clientID, $clientSecret): Oss
    {
        return parent::boot($clientID, $clientSecret);
    }
}
