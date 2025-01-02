<?php

require_once __DIR__ . '/vendor/autoload.php';

use Yutao\Pan123\Client;

$config = require 'config.php';
$sdk = Client::boot($config['accessKeyId'], $config['accessKeySecret']);


//1.获取用户信息
$user = $sdk->user->info();
empty($user['uid']) ? die('获取用户信息失败') : var_dump("用户信息：昵称：" . $user['nickname'] . "，uid：" . $user['uid']);

$dirName = 'test';
$localFile = 'upload.txt';

//2.获取文件列表V2
$file_list = $sdk->file->list_v2(0, 0, 20, $dirName, 1);
$dirId = $file_list['fileList'][0]['fileId'] ?? 0;
if (empty($dirId)) {
	//创建目录
	$dirId = $sdk->file->mkdir($dirName);
	$dirId ? var_dump("创建目录成功，目录ID：" . $dirId) : die("创建目录失败：" . $sdk->getError());
} else {
	var_dump("目录已存在，目录ID：" . $dirId);
}

var_dump("在目录：" . $dirName . "下查找文件：" . $localFile);

//3.查找文件
$file = $sdk->file->find($dirId, $localFile);
if (!empty($file['fileID'])) {
	var_dump("文件存在，文件ID：" . $file['fileID']);
	//4.删除文件
	$res = $sdk->file->delete($file['fileID']);
	$res ? var_dump("删除文件成功") : die("删除文件失败：" . $sdk->getError());
} else {
	var_dump("文件不存在");
}

var_dump("开始上传文件");
file_put_contents($localFile, time());
//5.上传文件
$res = $sdk->upload->upload($localFile, $dirName . '/' . basename($localFile));
$preuploadID = $res['preuploadID'] ?? '';
if (empty($preuploadID)) {
	var_dump("上传文件失败：错误信息：" . $sdk->getError());
	die;
}
var_dump("上传文件成功，上传ID：" . $preuploadID, "等待1秒");


//6.异步查询等待1秒
sleep(1);
$res = $sdk->upload->status($preuploadID);
$fileID = $res['fileID'] ?? null;
if (empty($fileID)) {
	var_dump("上传文件失败：错误信息：" . $sdk->getError());
	die;
}
var_dump("上传文件成功，文件ID：" . $fileID);


$res = $sdk->file->move($fileID);
if (empty($res)) {
	var_dump($res);
	var_dump("移动文件失败：错误信息：" . $sdk->getError());
	die;
} else {
	var_dump("移动文件成功");
}

$res = $sdk->file->move($fileID, $dirId);
if (empty($res)) {
	var_dump($res);
	var_dump("移动文件失败：错误信息：" . $sdk->getError());
	die;
} else {
	var_dump("移动文件成功");
}

$res = $sdk->file->rename($fileID, 'test.txt');
if (empty($res)) {
	var_dump($res);
	var_dump("重命名文件失败：错误信息：" . $sdk->getError());
	die;
} else {
	var_dump("重命名文件成功");
}

//7.查询详情
$res = $sdk->file->detail($fileID);
if (empty($res['fileID'])) {
	var_dump("查询文件详情失败：错误信息：" . $sdk->getError());
	die;
}
var_dump("查询文件详情成功，文件ID：" . $res['fileID'] . "，文件名：" . $res['filename']);

//8.文件夹开启直连
$res = $sdk->link->enable($dirId);
$res ? var_dump("文件夹开启直连成功") : die("文件夹开启直连失败：" . $sdk->getError());

//9.获取链接
$res = $sdk->link->url($fileID);
$res ? var_dump("获取链接成功，链接：" . $res) : die("获取链接失败：" . $sdk->getError());

//10.禁用直连
$res = $sdk->link->disable($dirId);
$res ? var_dump("禁用直连成功") : die("禁用直连失败：" . $sdk->getError());

//11.开启直连
$res = $sdk->link->enable($dirId);
$res ? var_dump("开启直连成功") : die("开启直连失败：" . $sdk->getError());

//12.分享
$res = $sdk->share->create($fileID, 1111, 'test');
$res ? var_dump("分享成功，分享链接：" . $res['url'] . "密码：" . $res['pwd']) : die("分享失败：" . $sdk->getError());


unlink($localFile);
