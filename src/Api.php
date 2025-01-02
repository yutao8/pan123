<?php

namespace Yutao\Pan123;

class Api
{
    private $accessToken;
    private $clientID;
    private $clientSecret;
    private $cacheDir;
    private $error;


    private $urlBase = 'https://open-api.123pan.com';


    function __construct($clientID, $clientSecret)
    {
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $this->getAccessToken();
    }

    protected function setError($errMsg, $errCode, $errData = []): Api
    {
        $this->error = array_merge([
            'msg' => $errMsg,
            'code' => $errCode,
            'info' => ErrorCode::ERR_CODE[$errCode] ?? '',
        ], $errData);
        return $this;
    }

    function getError($type = 'msg')
    {
        return  empty($type) ? $this->error : ($this->error[$type] ?? '');
    }


    function setUrlBase($urlBase): Api
    {
        $this->urlBase = $urlBase;
        return $this;
    }

    function setCacheDir($dir): Api
    {
        $this->cacheDir = $dir;
        return $this;
    }

    protected function getAccessToken()
    {
        $key1 = 'accessToken' . $this->clientID;
        $cache = $this->cache($key1);
        if (!empty($cache)) {
            return $cache;
        }
        $res = $this->buildAccessToken();
        if (empty($res['accessToken'])) {
            return null;
        }
        $this->cache($key1, $res['accessToken'], strtotime($res['expiredAt']) - time());
        return $res['accessToken'];
    }


    private function buildAccessToken()
    {
        $url = '/api/v1/access_token';
        $post = [
            "clientID" => $this->clientID,
            "clientSecret" => $this->clientSecret,
        ];
        $res = $this->http_request($url, $post);
        return $res['data'] ?? [];
    }

    protected function http_get($url, $data = null, $header = [])
    {
        $url = $url . '?' . http_build_query($data);
        return $this->http_request($url, null, $header);
    }

    protected function http_request($url, $data = null, $header = [])
    {
        $header[] = 'Content-Type: application/json';
        $header[] = 'Platform: open_platform';
        $header[] = 'Authorization:Bearer ' . $this->accessToken;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); //超时时间
        curl_setopt($curl, CURLOPT_URL, $this->urlBase . $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        } else {
            curl_setopt($curl, CURLOPT_HTTPGET, 1);
        }
        if ($header && is_array($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $ret = json_decode($output, true);
        if (!isset($ret['code']) or curl_errno($curl)) {
            $ret['code'] = -1;
            $ret['message'] = "curl_error:" . curl_error($curl);
            $ret['_body'] = $output;
            $ret['_data'] = ['url' => $this->urlBase . $url];
        }
        $ret['code'] === 0 or $this->setError($ret['message'] ?? '', $ret['code'], $ret);
        curl_close($curl);
        return $ret;
    }

    public function cache($key, $value = null, $expire = null)
    {
        $cache_dir = $this->cacheDir ?? (sys_get_temp_dir() . '/pan123/');
        is_dir($cache_dir) or mkdir($cache_dir, 0777, true);
        $filename = $cache_dir . md5($key) . '.php';
        if (!is_null($value)) {
            $cache_data = serialize(['key' => $key, 'data' => $value, 'time' => time(), 'expire' => $expire]);
            return file_put_contents($filename, $cache_data);
        }
        if (is_file($filename)) {
            $data = unserialize(file_get_contents($filename));
            if (isset($data['expire']) && time() > $data['time'] + $data['expire']) {
                unlink($filename); //缓存过期删除缓存文件
                return false;
            }
            return $data['data'];
        }
        return false;
    }
}
