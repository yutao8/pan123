<?php

require_once __DIR__ . '/vendor/autoload.php';

use Yutao\Pan123\Client;

$sdk=  Client::boot('c747b667b1111111171db136ff','af3b9cb50954111111111e273de2');

$rootDirName=time();
$localFile='./upload.txt';


//获取用户信息
$user=$sdk->user->info();
var_dump($user);


//创建目录
$sdk->file->mkdir(time());

//获取文件列表
$root_file= $sdk->file->list(0,$rootDirName);
$rootDirId= $root_file[0]['fileID']??0;

//查询文件
$file_list= $sdk->file->list($rootDirId,$localFile);
$fileID=$file_list[0]['fileID']??0;

if($fileID){
    //删除文件
    $sdk->file->delete($fileID);
}

file_put_contents($localFile, time());
//上传文件
$res= $sdk->upload->upload($localFile,$rootDirId);
$fileID=$res['fileID']??0;


var_dump($res);

if($fileID){
    //文件夹开启直连
    $sdk->link->enable($rootDirId);
    //获取链接
    $res= $sdk->link->url($fileID);
    var_dump($res);
    $res= $sdk->share->create($fileID,1111,'test');
    var_dump($res);
}

//禁用直连
$sdk->link->disable($rootDirId);
//删除文件
$sdk->file->delete($fileID);
//删除文件夹
$sdk->file->delete($rootDirId);