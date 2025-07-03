<?php

namespace Yutao\Pan123\Api;

use Yutao\Pan123\Api;

class File extends Api
{

    /**
     * 文件列表
     * @param int $parentFileId  父级文件夹ID
     * @param string $searchData  搜索关键字
     * @param int $page  页码
     * @param int $limit  每页数量
     * @param string $orderBy  排序字段（file_id、size、file_name）
     * @param string $orderDirection  排序方向:asc、desc
     * @param bool $trashed  是否显示回收站文件
     * @return array
     */
    function list(int $parentFileId = 0, string $searchData = '', int $page = 1, int $limit = 20, string $orderBy = 'file_name', string $orderDirection = 'asc', bool $trashed = false): array
    {
        $res = $this->http_get('/api/v1/file/list', [
            'parentFileId' => $parentFileId,
            'searchData' => $searchData,
            'page' => $page,
            'limit' => $limit,
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection,
            'trashed' => $trashed,
        ]);
        return $res['data']['fileList'] ?? [];
    }

    function find(int $parentFileId = 0, string $searchData = '', int $page = 1, int $limit = 100, string $orderBy = 'file_name', string $orderDirection = 'asc', bool $trashed = false)
    {
        while (true) {
            $list = $this->list($parentFileId, $searchData, $page, $limit, $orderBy, $orderDirection, $trashed);
            if (empty($list)) {
                return [];
            }
            foreach ($list as $item) {
                if ($item['filename'] === $searchData) {
                    return $item;
                }
            }
            if (count($list) < $limit) {
                return [];
            }
            $page++;
        }
    }


    /**
     * 文件列表v2
     *
     * @param int    $parentFileId 父级文件夹ID
     * @param int    $lastFileId   最后一个文件ID，用于翻页
     * @param int    $limit        每页数量 (<=100)
     * @param string $searchData   搜索关键字
     * @param int    $searchMode   搜索模式（0：全文搜索，1：文件名搜索）
     * @param bool   $trashed      是否显示回收站文件
     *
     * @return array
     */
    function list_v2(int $parentFileId = 0, int $lastFileId = 0, int $limit = 20,  string $searchData = '', int $searchMode = 0,bool  $trashed=false): array
    {
        $res = $this->http_get('/api/v2/file/list', [
            'parentFileId' => $parentFileId,
            'limit' => $limit,
            'searchData' => $searchData,
            'searchMode' => $searchMode,
            'lastFileId' => $lastFileId,
            'trashed'=> $trashed
        ]);
        return $res['data'] ?? [];
    }

    //获取一个文件


    /**
     * 文件详情
     *
     * @param int $fileID
     * @return mixed
     */
    function detail(int $fileID)
    {
        $res = $this->http_get('/api/v1/file/detail', ['fileID' => $fileID]);
        return $res['data'] ?? false;
    }
	
	
	/**
	 * 下载文件
	 *
	 * @param string $fileId
	 * @return array|false
	 */
	function download(string $fileId): array
	{
		$res = $this->http_get('/api/v1/file/download_info', ['fileId' => $fileId]);
		return $res['data'] ?? false;
	}


    /**
     * 创建文件夹
     * @param string $name 文件夹名称
     * @param int $parentID 父级文件夹ID
     * @return string|null 文件夹ID
     */
    function mkdir(string $name, int $parentID = 0)
    {
        $res = $this->http_request('/upload/v1/file/mkdir', ['name' => $name, 'parentID' => $parentID]);
        return $res['data']['dirID'] ?? null;
    }

    /**
     * 移动文件
     * @param $fileID array|string 文件ID
     * @param $toParentFileID int 目标文件夹ID
     * @return bool
     */
    function move($fileID, int $toParentFileID = 0): bool
    {
        $res = $this->http_request('/api/v1/file/move', [
            'fileIDs' => is_array($fileID) ? $fileID : [$fileID],
            'toParentFileID' => $toParentFileID,
        ]);
        return $res['code'] === 0;
    }

    function rename($fileID, $name): bool
    {
        $fileIdList = is_array($fileID) ? $fileID : [$fileID];
        $nameList = is_array($name) ? $name : [$name];
        $renameList = array_map(function ($fileId, $name) {
            return $fileId . '|' . $name;
        }, $fileIdList, $nameList);
        $res = $this->http_request('/api/v1/file/rename', ['renameList' => $renameList]);
        return $res['code'] === 0;
    }



    /**
     * 删除文件到回收站
     * @param $fileID array|string 文件ID
     * @return bool
     */
    function delete($fileID): bool
    {
        $res = $this->http_request('/api/v1/file/trash', ['fileIDs' => is_array($fileID) ? $fileID : [$fileID]]);
        return $res['code'] == 0;
    }

    /**
     * 从回收站恢复文件
     * @param $fileID array|string 文件ID
     * @return bool
     */
    function recover($fileID): bool
    {
        $res = $this->http_request('/api/v1/file/recover', ['fileIDs' => is_array($fileID) ? $fileID : [$fileID]]);
        return $res['code'] == 0;
    }

    /**
     * 从回收站彻底删除文件
     * @param $fileID array|string 文件ID
     * @return bool
     */
    function destroy($fileID): bool
    {
        $res = $this->http_request('/api/v1/file/delete', ['fileIDs' => is_array($fileID) ? $fileID : [$fileID]]);
        return $res['code'] == 0;
    }
}
