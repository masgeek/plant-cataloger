<?php

namespace app\models\base;

use Yii;
use app\common\models\BaseModel;

/**
* This is the base model class for table "{{%disease_reports}}".
*
* @property integer $id
* @property string $reported_by
* @property string $country_code
* @property string $phone_number
* @property string|null $disease_type
* @property string $solution
* @property string|null $date_reported
* @property string $created_at
* @property string $updated_at
*
* @property DiseaseReportImage[] $diseaseReportImages
*/
class DiseaseReport extends BaseModel
{

    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return '{{%disease_reports}}';
    }

    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [['reported_by', 'country_code', 'phone_number', 'solution'], 'required'],
            [['solution'], 'string'],
            [['date_reported', 'created_at', 'updated_at'], 'safe'],
            [['reported_by', 'disease_type'], 'string', 'max' => 255],
            [['country_code'], 'string', 'max' => 4],
            [['phone_number'], 'string', 'max' => 15]
        ];
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reported_by' => 'Reported By',
            'country_code' => 'Country Code',
            'phone_number' => 'Phone Number',
            'disease_type' => 'Disease Type',
            'solution' => 'Solution',
            'date_reported' => 'Date Reported',
        ];
    }
    /**
    * Record relations here
    * @return yii\db\ActiveQuery
    */
    public function getDiseaseReportImages()
    {
        return $this->hasMany(\app\models\base\DiseaseReportImage::class, ['disease_report_id' => 'id']);
    }

}
