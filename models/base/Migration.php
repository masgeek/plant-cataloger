<?php

namespace app\models\base;

use Yii;
use app\common\models\BaseModel;

/**
* This is the base model class for table "{{%migration}}".
*
* @property string $version
* @property integer|null $apply_time
*/
class Migration extends BaseModel
{

    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return '{{%migration}}';
    }

    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [['version'], 'required'],
            [['apply_time'], 'integer'],
            [['version'], 'string', 'max' => 180]
        ];
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'version' => 'Version',
            'apply_time' => 'Apply Time',
        ];
    }

}
