<?php

namespace Yutao\Pan123\Api;


class Upload extends File
{


    //一步上传
    function upload($filePath,$remoteDirId=0){
        //预上传
        $preview=$this->create(basename($filePath),md5_file($filePath),filesize($filePath),$remoteDirId);
        if (isset($preview['reuse']) && $preview['reuse'] && $preview['fileID']){
            //秒传
            return $preview;
        }
        $result=[];
        if (isset($preview['preuploadID']) && $preview['preuploadID']){
            if($this->chunk($filePath,$preview['sliceSize'],$preview['preuploadID'])){
                $result = $this->finish($preview['preuploadID']);
            }
        }
        return $result;
    }


    /**
     * 创建文件
     * @param string $filename 文件名要小于128个字符且不能包含以下任何字符："\/:*?|><。（注：不能重名）
     * @param string $md5 文件md5
     * @param number $size 文件大小，单位为 byte 字节
     * @param int $parentFileID 父目录id，上传到根目录时填写 0
     * @return array|mixed
     */
    function create(string $filename, string $md5, $size, int $parentFileID=0)
    {
        $res=$this->http_request('/upload/v1/file/create',[
            'parentFileID'=>$parentFileID,
            'filename'=>$filename,
            'etag'=>$md5,
            'size'=>$size,
        ]);
        return $res['data']??[];
    }

    //获取上传URL
    function getUploadUrl($preuploadID,$sliceNo=1)
    {
        $res=$this->http_request('/upload/v1/file/get_upload_url',[
            'preuploadID'=>$preuploadID,
            'sliceNo'=>$sliceNo,
        ]);
        return $res['data']['presignedURL']??null;
    }


    //分片上传
    function chunk($file,$chunkSize,$preuploadID)
    {
        $handle = fopen($file, 'rb');
        $chunkIndex=0;
        do {
            $uploadUrl=$this->getUploadUrl($preuploadID,$chunkIndex+1);
            $chunkData = fread($handle, $chunkSize);
            $res=$this->curl_upload($uploadUrl,$chunkData);
            if (!$res){
                return false;
            }
            $chunkIndex++;
        }while(!feof($handle));
        fclose($handle);
        return $chunkIndex;
    }

    //已上传的分片列表
    function queryChunk($preuploadID)
    {
        $res=$this->http_request('/upload/v1/file/list_upload_parts',['preuploadID'=>$preuploadID,]);
        return $res['data']['parts']??null;
    }

    //上传完毕
    function finish($preuploadID)
    {
        $res=$this->http_request('/upload/v1/file/upload_complete',['preuploadID'=>$preuploadID,]);
        return $res['data']??[];
    }


    //上传状态
    function status($preuploadID)
    {
        $res=$this->http_request('/upload/v1/file/upload_async_result',['preuploadID'=>$preuploadID,]);
        return isset($res['data']['completed'])?$res:false;
    }




   protected function curl_upload($url,$chunk): bool
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS =>$chunk,
        ]);
        curl_exec($curl);
        $errCode=curl_errno($curl);
        return $errCode==0;
    }



}