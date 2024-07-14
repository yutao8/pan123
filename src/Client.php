<?php

namespace Yutao\Pan123;

use Exception;
use Yutao\Pan123\Api\Download;
use Yutao\Pan123\Api\Link;
use Yutao\Pan123\Api\Share;
use Yutao\Pan123\Api\Upload;
use Yutao\pan123\Api\User;
use Yutao\pan123\Api\File;


/**
 * Class Client
 * @property File $file 文件操作类
 * @property User $user 用户操作类
 * @property Download $download 文件下载类
 * @property Share $share 文件分享类
 * @property Upload $upload 文件上传类
 * @property Link $link 文件链接
 * @method Api setUrlBase($url) 设置api域名
 * @method Api setCacheDir($dir) 设置缓存目录
 * @method array getError($type=null) 获取错误信息
 * @package Yutao\Pan123
 * */
class Client
{
    private static $instance;

    private static $constants;
    private $clientID;
    private $clientSecret;
    private $lastClass;


    private function __construct($clientID, $clientSecret)
    {
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;
    }

    public static function boot($clientID, $clientSecret): Client
    {
        if (!isset(self::$instance[$clientID])) {
            self::$instance[$clientID] = new self($clientID, $clientSecret);
        }
        return self::$instance[$clientID];
    }


    /**
     * @throws Exception
     */
    public function __get($name)
    {
        if (!isset(self::$constants[$name])) {
            $className = __NAMESPACE__ . "\\Api\\" . ucfirst($name);
            if (!class_exists($className)){
                throw new Exception("Method $name not found");
            }
            self::$constants[$name]=new $className($this->clientID, $this->clientSecret);
        }
        return $this->lastClass = self::$constants[$name];
    }

    function __call($name,$args){
        return call_user_func_array([$this->lastClass,$name],$args);
    }

}
