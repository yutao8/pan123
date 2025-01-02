<?php

namespace Yutao\Pan123\Api\Oss;

use Yutao\Pan123\Api;

class File extends Api
{
	
	
	/**
	 * 文件列表
	 *
	 * @param string $parentFileId 父级文件夹ID
	 * @param string $lastFileId   上一次请求的最后一个文件ID
	 * @param int    $limit        每页数量
	 * @param int    $startTime    开始时间(时间戳)
	 * @param int    $endTime      结束时间(时间戳)
	 * @return array|false
	 */
    function list(string $parentFileId = "", string $lastFileId = "", int $limit = 20, int $startTime = 0, int $endTime = 0)
    {
        $res = $this->http_request('/api/v1/oss/file/list', [
            'parentFileId' => $parentFileId,
            'lastFileId' => $lastFileId,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'limit' => $limit,
            'type' => 1
        ]);
        return $res['data']['fileList'] ?? false;
    }
	
	/**
	 * 查找文件
	 *
	 * @param             $filename
	 * @param bool        $isLike       是否模糊查找
	 * @param string|null $parentFileId 父级文件夹ID
	 * @param string      $lastFileId   上一次请求的最后一个文件ID
	 * @param int         $limit        每页数量
	 * @param int         $startTime    开始时间(时间戳)
	 * @param int         $endTime      结束时间(时间戳)
	 * @return array
	 */
    function find($filename, bool $isLike = false, string $parentFileId = null, string $lastFileId = '', int $limit = 100, int $startTime = 0, int $endTime = 0): array
    {
        while (true) {
            $list = $this->list($parentFileId, $lastFileId, $limit, $startTime, $endTime);
            if (empty($list)) {
                return [];
            }
            foreach ($list as $item) {
                if ($isLike ? strpos($item['filename'], $filename) !== false :  $item['filename'] === $filename) {
                    return $item;
                }
            }
            if (count($list) < $limit) {
                return [];
            }
            $lastFileId = $list[count($list) - 1]['fileId'];
        }
    }


    /**
     * 文件详情
     *
     * @param string $fileID
     * @return mixed
     */
    function detail(string $fileID)
    {
        $res = $this->http_get('/api/v1/oss/file/detail', ['fileID' => $fileID]);
        return $res['data'] ?? false;
    }
	
	
	/**
	 * 创建文件夹
	 *
	 * @param string|array $name     文件夹名称 (数组时批量创建)
	 * @param string|null  $parentID 父级文件夹ID
	 * @return string|array 文件夹ID
	 */
    function mkdir($name, string $parentID = null)
    {
        $res = $this->http_request('/upload/v1/oss/file/mkdir', ['name' => $name,  'parentID' => $parentID, 'type' => 1]);
        $list = $res['data']['list'] ?? [];
        return is_array($name) ? $list : ($list[0]['dirID'] ?? []);
    }

    /**
     * 移动文件
     * @param $fileID string|array 文件ID
     * @param $toParentFileID string 目标文件夹ID
     * @return bool
     */
    function move($fileID, string $toParentFileID = ""): bool
    {
        $res = $this->http_request('/api/v1/oss/file/move', [
            'fileIDs' => $fileID,
            'toParentFileID' => $toParentFileID,
        ]);
        return $res['code'] === 0;
    }



    /**
     * 删除文件到回收站
     * @param $fileID array|string 文件ID
     * @return bool
     */
    function delete($fileID): bool
    {
        $res = $this->http_request('/api/v1/oss/file/delete', ['fileIDs' => is_array($fileID) ? $fileID : [$fileID]]);
        return $res['code'] === 0;
    }
}
