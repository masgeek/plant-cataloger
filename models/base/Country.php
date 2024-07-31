<?php

namespace app\models\base;

use Yii;

/**
 * This is the base model class for table "countries".
 *
 * @property string $country_code
 * @property string $country_name
 * @property string $created_at
 * @property string $updated_at
 */
class Country extends \app\common\models\BaseModel
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_code', 'country_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['country_code'], 'string', 'max' => 4],
            [['country_name'], 'string', 'max' => 50],
            [['country_code'], 'unique']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'countries';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'country_code' => 'Country Code',
            'country_name' => 'Country Name',
        ];
    }
}
