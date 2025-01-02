<?php

require_once __DIR__ . '/vendor/autoload.php';

use Yutao\Pan123\Oss;

$config = require 'config.php';

$oss =  Oss::boot($config['accessKeyId'], $config['accessKeySecret']);

//创建目录
$dirId = $oss->file->mkdir('test' . date('YmdHis'));
$dirId ? var_dump("创建目录成功，目录ID：" . $dirId) : die("创建目录失败：" . $oss->getError());

$localFile = createRandImage();
var_dump("开始上传文件" . $localFile);

//5.上传文件
$res = $oss->upload->upload($localFile, $dirId);
$preuploadID = $res['preuploadID'] ?? '';
if (empty($preuploadID)) {
    var_dump($res);
    var_dump("上传文件失败：错误信息：" . $oss->getError());
    die;
}
var_dump("上传文件成功，上传ID：" . $preuploadID, "等待3秒");


//6.异步查询等待3秒
sleep(3);
$res = $oss->upload->status($preuploadID);
$fileID = $res['fileID'] ?? null;
if (empty($fileID)) {
    var_dump($res);
    var_dump("上传文件失败：错误信息：" . $oss->getError());
    die;
}
var_dump("上传文件成功，文件ID：" . $fileID);

$res = $oss->file->list($dirId);
if (empty($res)) {
    var_dump($res);
    var_dump("查询文件列表失败：错误信息：" . $oss->getError());
    die;
} else {
    var_dump("查询文件列表成功，文件数量：" . count($res));
}

$newDirId = $oss->file->mkdir('test' . date('YmdHis'));
$newDirId ? var_dump("创建目录成功，目录ID：" . $newDirId) : die("创建目录失败：" . $oss->getError());


$res = $oss->file->move($fileID,$newDirId);
if (empty($res)) {
    var_dump($res);
    var_dump("移动文件失败：错误信息：" . $oss->getError());
    die;
} else {
    var_dump("移动文件成功");
}

//7.查询详情
$res = $oss->file->detail($fileID);
if (empty($res['fileId'])) {
    var_dump($res);
    var_dump("查询文件详情失败：错误信息：" . $oss->getError());
    die;
}
var_dump("查询文件详情成功，文件ID：" . $res['fileId'] . "，文件名：" . $res['filename'] . "链接：" . $res['downloadURL']);



$res = $oss->file->find(basename($localFile), false, $newDirId);
if (!empty($res['fileId'])) {
    var_dump("文件查找成功，文件ID：" . $res['fileId']);
} else {
    var_dump($res);
    var_dump("文件查找失败：" . $oss->getError());
    die;
}

$res = $oss->file->delete($fileID);
$res ? var_dump("删除文件成功") : var_dump("删除文件失败：错误信息：" . $oss->getError());

sleep(1);

$res = $oss->file->delete([$dirId,$newDirId]);
$res ? var_dump("删除目录成功") : var_dump("删除目录失败：错误信息：" . $oss->getError());


unlink($localFile);


/**
 * 生成随机图片
 * @param mixed $filename
 * @param mixed $width
 * @param mixed $height
 * @return string
 */
function createRandImage($filename = null, $width = 100, $height = 100)
{
    $img = imagecreatetruecolor($width, $height);
    $color = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
    imagefill($img, 0, 0, $color);
    $filename or $filename = tempnam(sys_get_temp_dir(), 'img') . '.png';
    imagepng($img, $filename);
    imagedestroy($img);
    return $filename;
}
