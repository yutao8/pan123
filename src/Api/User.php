<?php

namespace Yutao\Pan123\Api;
use Yutao\Pan123\Api;

/**
 * Class User
 * @package Yutao\Pan123\Api
 */
class User extends  Api
{

    /**
     * 获取账号信息
     * @return array|mixed
     */
    function info()
    {
        $res = $this->http_request('/api/v1/user/info');
        return $res['data'] ?? [];
    }

}