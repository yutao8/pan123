<?php

namespace Yutao\Pan123;

class ErrorCode
{
    const ERR_CODE = [
        0 => '成功',
        401 => 'access_token无效',
        429 => '请求太频繁',
    ];
}