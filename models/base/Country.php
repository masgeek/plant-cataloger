<?php

namespace app\models\base;

use Yii;
use app\models\base\BaseModel;

/**
* This is the base model class for table "{{%countries}}".
*
* @property string $country_code
* @property string $country_code_3
* @property string $country_name
* @property string $created_at
* @property string $updated_at
*/
class Country extends BaseModel
{

    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return '{{%countries}}';
    }

    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [['country_code', 'country_code_3', 'country_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['country_code'], 'string', 'max' => 2],
            [['country_code_3'], 'string', 'max' => 4],
            [['country_name'], 'string', 'max' => 50],
            [['country_code'], 'unique'],
            [['country_code_3'], 'unique']
        ];
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'country_code' => 'Use the alpha2 code',
            'country_code_3' => 'Use the alpha2 code',
            'country_name' => 'Country Name',
        ];
    }

}
