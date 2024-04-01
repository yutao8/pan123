<?php

namespace Yutao\Pan123\Api;


class Download extends File
{

    /**
     * 添加离线下载任务
     * @param string $url 下载链接
     * @param string $fileName 文件名
     * @param string $callBackUrl 异步回调地址   url:下载资源地址 ;status:0成功,1失败 ;fileReason：失败原因
     * @return bool
     */
    function add(string $url, string $fileName='', string $callBackUrl=''): bool
    {
        $res = $this->http_request('/api/v1/offline/download', [
            'url' => $url,
            'fileName' => $fileName,
            'callBackUrl' => $callBackUrl,
        ]);
        return $res['code'] ==0;
    }
}