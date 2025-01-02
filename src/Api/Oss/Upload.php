<?php

namespace Yutao\Pan123\Api\Oss;


class Upload extends File
{


    /**
     * 快捷上传
     *
     * @param string     $filePath   本地文件路径
     * @param string $remoteDirId 远程目录ID(int是ID，string路径)
     * @return array
     */
    function upload(string $filePath, string $remoteDirId): array
    {
        //预上传
        $preview = $this->create(basename($filePath), md5_file($filePath), filesize($filePath), $remoteDirId);
        if (!empty($preview['reuse'])  && !empty($preview['fileID'])) {
            //秒传
            return $preview;
        }
        if (!empty($preview['preuploadID'])) {
            if ($this->chunk($filePath, $preview['sliceSize'], $preview['preuploadID'])) {
                $result = $this->finish($preview['preuploadID']);
                $result && $result['preuploadID'] = $preview['preuploadID'];
            }
        }
        return $result ?? [];
    }


    /**
     * 创建文件
     *
     * @param string $filename     文件名要小于128个字符且不能包含以下任何字符："\/:*?|><。（注：不能重名）
     * @param string $md5          文件md5
     * @param int    $size         文件大小，单位为 byte 字节
     * @param string    $parentFileID 父目录id，上传到根目录时填写 0
     * @param bool  $containDir   是否包含目录，如果为true，filename是远程路径，否则是文件名
     * @return array|mixed
     */
    function create(string $filename, string $md5, int $size, string $parentFileID = "", bool $containDir = false)
    {
        $res = $this->http_request('/upload/v1/oss/file/create', [
            'parentFileID' => $parentFileID,
            'filename' => $filename,
            'etag' => $md5,
            'size' => $size,
            'containDir' => $containDir,
            'type' => 1,
        ]);
        return $res['data'] ?? [];
    }

    /**
     * 获取上传URL
     *
     * @param string $preuploadID  预上传ID
     * @param int $sliceNo      分片序列号
     * @return mixed|null
     */
    function getUploadUrl(string $preuploadID, int $sliceNo = 1)
    {
        $res = $this->http_request('/upload/v1/oss/file/get_upload_url', [
            'preuploadID' => $preuploadID,
            'sliceNo' => $sliceNo,
        ]);
        return $res['data']['presignedURL'] ?? null;
    }


    /**
     * 分片上传
     *
     * @param string $file        本地文件路径
     * @param int    $chunkSize   分片大小
     * @param string    $preuploadID 预上传ID
     * @return false|int
     */
    function chunk(string $file, int $chunkSize, string $preuploadID)
    {
        $handle = fopen($file, 'rb');
        $chunkIndex = 0;
        do {
            $uploadUrl = $this->getUploadUrl($preuploadID, $chunkIndex + 1);
            $chunkData = fread($handle, $chunkSize);
            $res = $this->curl_upload($uploadUrl, $chunkData);
            if (!$res) {
                return false;
            }
            $chunkIndex++;
        } while (!feof($handle));
        fclose($handle);
        return $chunkIndex;
    }

    /**
     * 查询已上传的分片列表
     *
     * @param string $preuploadID
     * @return mixed|null
     */
    function queryChunk(string $preuploadID)
    {
        $res = $this->http_request('/upload/v1/oss/file/list_upload_parts', ['preuploadID' => $preuploadID,]);
        return $res['data']['parts'] ?? null;
    }

    /**
     * 上传完毕
     *
     * @param string $preuploadID
     * @return array|mixed
     */
    function finish(string $preuploadID)
    {
        $res = $this->http_request('/upload/v1/oss/file/upload_complete', ['preuploadID' => $preuploadID,]);
        return $res['data'] ?? [];
    }


    /**
     * 上传状态
     *
     * @param string $preuploadID
     * @return false|mixed
     */
    function status(string $preuploadID)
    {
        $res = $this->http_request('/upload/v1/oss/file/upload_async_result', ['preuploadID' => $preuploadID,]);
        return isset($res['data']['completed']) ? $res['data'] : false;
    }


    /**
     * curl分片上传
     * @param string $url URL
     * @param string $chunk 分片内容
     * @return bool
     */
    protected function curl_upload($url, $chunk): bool
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $chunk,
        ]);
        curl_exec($curl);
        $errCode = curl_errno($curl);
        return $errCode === 0;
    }
}
