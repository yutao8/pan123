# 123pan sdk for php
    website : https://www.123pan.com/
    dev: https://www.123pan.com/developer
    doc : https://123yunpan.yuque.com/org-wiki-123yunpan-muaork/cr6ced

# composer
```bash
composer require yutao/pan123:dev-master
```

## demo  
[demo.php](https://github.com/yutao8/pan123/blob/master/demo.php)

## 目录程序
[123index](https://github.com/yutao8/123index)


## 网盘 API

### 使用
```php
use Yutao\Pan123\Client;
$sdk=  Client::boot('your clientID', 'your clientSecret');
```

### 账号
- 获取账号信息  `$sdk->user->info(); `

### 文件
- 获取文件列表 `$sdk->file->list();`
- 获取文件列表(新) `$sdk->file->list_v2();`
- 查找文件 `$sdk->file->find();`
- 创建文件夹  `$sdk->file->mkdir();`
- 移动文件 `$sdk->file->move();`
- 重命名文件 `$sdk->file->rename();`
- 删除文件 `$sdk->file->delete();`
- 恢复文件 `$sdk->file->recover();`
- 销毁文件 `$sdk->file->destroy();`

### 上传
- 一键上传 `$sdk->upload->upload();`
- 创建文件 `$sdk->upload->create();`
- 获取上传URL `$sdk->upload->getUploadUrl();`
- 上传文件 `$sdk->upload->chunk();`
- 获取分片列表 `$sdk->upload->queryChunk();`
- 结束上传 `$sdk->upload->finish();`
- 获取上传状态 `$sdk->upload->status();`

### 直链
- 开启文件夹直连  `$sdk->link->enable();`
- 关闭文件夹直连  `$sdk->link->disable();`
- 获取直链URL  ` $sdk->link->url();`
- 获取直链鉴权URL  ` $sdk->link->sign();`
- 执行视频文件转码  ` $sdk->link->doTrans();`
- 查询转码结果  ` $sdk->link->queryTrans();`
- 获取视频转码URL  ` $sdk->link->m3u8();`

### 离线下载
- 离线下载  `$sdk->download->add();`

### 分享
- 创建分享 `$sdk->share->create();`

### 公共

- 获取错误信息 `$sdk->getError();`
- 修改api域名 `$sdk->setUrlBase();`
- 修改缓存目录 `$sdk->setCacheDir();`


## 图床 API
### 使用
```php
use Yutao\Pan123\Oss;
$oss=  Oss::boot('your clientID', 'your clientSecret');
```

### 文件

- 获取文件列表 `$oss->file->list();`
- 查找文件 `$oss->file->find();`
- 获取文件详情 `$oss->file->detail();`
- 创建目录 `$oss->file->mkdir();`
- 移动文件 `$oss->file->move();`
- 删除文件 `$oss->file->delete();`

### 上传
- 上传文件 `$oss->upload->upload();`
- 创建文件 `$oss->upload->create();`
- 获取上传URL `$oss->upload->getUploadUrl();`
- 上传文件 `$oss->upload->chunk();`
- 获取分片列表 `$oss->upload->queryChunk();`
- 结束上传 `$oss->upload->finish();`
- 获取上传状态 `$oss->upload->status();`



### 公共

- 获取错误信息 `$oss->getError();`
- 修改api域名 `$oss->setUrlBase();`
- 修改缓存目录 `$oss->setCacheDir();`


