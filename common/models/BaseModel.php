<?php
/** @noinspection UndetectableTableInspection */

namespace app\common\models;

use Exception;
use mootensai\relation\RelationTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class BaseModel
 * @property int $stock_category_id
 * @package app\common\models
 */
class BaseModel extends ActiveRecord
{
    use RelationTrait;

    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression('NOW()'),
            ]
        ];
    }
}
