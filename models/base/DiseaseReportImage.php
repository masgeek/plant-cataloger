<?php

namespace app\models\base;

use Yii;
use app\common\models\BaseModel;

/**
* This is the base model class for table "{{%disease_report_images}}".
*
* @property integer $id
* @property integer $disease_report_id
* @property string $image_path
* @property string $created_at
* @property string $updated_at
*/
class DiseaseReportImage extends BaseModel
{

    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return '{{%disease_report_images}}';
    }

    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [['disease_report_id', 'image_path'], 'required'],
            [['disease_report_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['image_path'], 'string', 'max' => 255]
        ];
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'disease_report_id' => 'Disease Report ID',
            'image_path' => 'Image Path',
        ];
    }

}
