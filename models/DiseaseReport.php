<?php

namespace app\models;

use \app\models\base\DiseaseReport as BaseDiseaseReports;

/**
 * This is the model class for table "disease_reports".
 */
class DiseaseReport extends BaseDiseaseReports
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['reported_by', 'country', 'phone_number', 'solution'], 'required'],
            [['solution'], 'string'],
            [['date_reported'], 'safe'],
            [['created_at', 'updated_at'], 'integer'],
            [['reported_by', 'disease_type'], 'string', 'max' => 255],
            [['country', 'phone_number'], 'string', 'max' => 15]
        ]);
    }
	
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'id' => 'ID',
            'reported_by' => 'Reported By',
            'country' => 'Country',
            'phone_number' => 'Phone Number',
            'disease_type' => 'Disease Type',
            'solution' => 'Solution',
            'date_reported' => 'Date Reported',
        ];
    }
}
