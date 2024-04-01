<?php

namespace Yutao\Pan123\Api;

use Yutao\Pan123\Api;

class Link extends Api
{
    /**
     * 文件夹打开直连空间
     * @param int $dirID 文件夹ID
     * @return string|null 成功启用直链空间的文件夹的名称
     */
    function enable(int $dirID)
    {
        $res=$this->http_request('/api/v1/direct-link/enable',['fileID'=>$dirID]);
        return $res['data']['filename']??null;
    }

    /**
     * 文件夹关闭直连空间
     * @param int $dirID 文件夹ID
     * @return string|null 成功禁用直链空间的文件夹的名称
     */
    function disable(int $dirID)
    {
        $res=$this->http_request('/api/v1/direct-link/disable',['fileID'=>$dirID]);
        return $res['data']['filename']??null;
    }

    /**
     * 获取文件直链
     * @param int $fileID 文件ID
     * @return mixed|null 文件对应的直链链接
     */
    function url(int $fileID)
    {
        $res=$this->http_get('/api/v1/direct-link/url',['fileID'=>$fileID]);
        return $res['data']['url']??null;
    }

    /**
     * 获取m3u8
     * @param int $fileID 文件ID
     * @return mixed|null 文件对应的直链链接
     */
    function m3u8(int $fileID)
    {
        $res=$this->http_get('/api/v1/direct-link/get/m3u8',['fileID'=>$fileID]);
        return $res['data']['list']??null;
    }


    /**
     * 视频发起直链转码
     * @param array|int $fileIDs 文件ID
     * @return bool
     */
    function doTrans($fileIDs): bool
    {
        $res=$this->http_request('/api/v1/direct-link/doTranscode',['ids'=>is_array($fileIDs)?$fileIDs:[$fileIDs]]);
        return $res['code']==0;
    }

    /**
     * 查询视频直链转码进度
     * @param array|int $fileIDs 文件ID
     * @return array  noneList:未转码列表,errorList:错误的列表,success:已转码列表
     */
    function queryTrans($fileIDs): array
    {
        $res=$this->http_request('/api/v1/direct-link/queryTranscode',['ids'=>is_array($fileIDs)?$fileIDs:[$fileIDs]]);
        return $res['data']??[];
    }


}