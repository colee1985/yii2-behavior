# yii2-behavior
---
### model在保存前，处理附件字段，如果字段有附件上传，则将附件存到OSS，然后把访问路径设为字段值
> usage
```php
public function behaviors()
{
    return [
        'uploadbehavior' => [
            'class' => UploadBehavior::className(),
            'fields' => ['cover', 'avatar'],
        ]
    ];
}
```