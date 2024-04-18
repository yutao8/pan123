# 123pan sdk for php
    website : https://www.123pan.com/
    dev: https://www.123pan.com/developer
    doc : https://123yunpan.yuque.com/org-wiki-123yunpan-muaork/cr6ced

# composer
`composer require yutao/pan123`

## demo  
[demo.php](https://github.com/yutao8/pan123/blob/master/demo.php)

## new
```php
use Yutao\Pan123\Client;
$sdk=  Client::boot('your clientID', 'your clientSecret');
```

## API

### 账号
- 获取账号信息  `$sdk->user->info(); `

### 文件
- 获取文件列表 `$sdk->file->list();`
- 创建文件夹  `$sdk->file->mkdir();`
- 移动文件 `$sdk->file->move();`
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
- 执行视频文件转码  ` $sdk->link->doTrans();`
- 查询转码结果  ` $sdk->link->queryTrans();`
- 获取视频转码URL  ` $sdk->link->m3u8();`

### 离线下载
- 离线下载  `$sdk->download->add();`

### 分享
- 创建分享 `$sdk->share->create();`


### 其他

- 获取错误信息 `$sdk->getError();`
- 修改api域名 `$sdk->setUrlBase();`
- 修改缓存目录 `$sdk->setCacheDir();`


