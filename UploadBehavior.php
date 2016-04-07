<?php
/**
 * ==============================================
 * Copy right 2015-2016
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * 处理 ActiveRecord 子类文件或图片上传时，自动存到OSS，并把访问地址设为字段值
 * 
 * public function behaviors()
 *  {
 *      return [
 *          'uploadbehavior' => [
 *              'class' => UploadBehavior::className(),
 *              'fields' => ['cover', 'avatar'],
 *          ]
 *      ];
 *  }
 * 
 * @param unknowtype
 * @return return_type
 * @author: CoLee
 */
namespace colee\behavior;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
class UploadBehavior extends Behavior
{
    public $fields = [];
    
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT=>'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE=>'beforeSave',
        ];
    }
    
    public function beforeSave($event)
    {
        $model = $this->owner;
        foreach ($this->fields as $field)
        {
            $this->uploadFile($model, $field);
        }
        return true;
    }
    
    /**
     * 自动处理上传的图片或附件
     * @return null
     */
    private function uploadFile($model, $field)
    {
        // 单张图
        $file = UploadedFile::getInstance($model, $field);
        if (!empty($file)){
            $path = \Yii::$app->oss->upload2oss($file->tempName);
            $model->$field = \Yii::$app->oss->getImageUrl($path);
            return true;
        }
        // 多图
        $files = UploadedFile::getInstances($model, $field);
        if (count($files)>0){
            $paths = [];
            foreach ($files as $file){
                $path = \Yii::$app->oss->upload2oss($file->tempName);
                $paths[] = \Yii::$app->oss->getImageUrl($path);
            }
            $model->$field = implode(',', $paths);
            return true;
        }
        $model->$field = $model->oldAttributes[$field];
        return true;
    }
}