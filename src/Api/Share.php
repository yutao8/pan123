<?php

namespace Yutao\Pan123\Api;

use Yutao\Pan123\Api;

class Share extends Api
{

    const SHARE_HOST='https://www.123pan.com/s/';
    const SHARE_EXPIRE=[0=>'永久', 1=>'一天', 7=>'一周', 30=>'一个月'];

    /**
     * 创建分享
     * @param array|int $fileID 文件ID
     * @param string $sharePwd 分享密码
     * @param string $shareName 分享名称
     * @param int $shareExpire 分享有效期 :0:永久,1:一天,7:一周,30:一个月
     * @return array
     */
    function create($fileID, string $sharePwd='', string $shareName='', int $shareExpire=0): array
    {
        $res=$this->http_request('/api/v1/share/create',[
            'fileIDList'=>is_array($fileID)?implode(',',$fileID):$fileID,
            'sharePwd'=>$sharePwd,
            'shareName'=>$shareName,
            'shareExpire'=>isset(self::SHARE_EXPIRE[$shareExpire])?$shareExpire:0,
        ]);
        return isset($res['data']['shareID'])?(array_merge($res['data'],['pwd'=>$sharePwd,'url'=>self::SHARE_HOST.$res['data']["shareKey"]])):[];
    }
}