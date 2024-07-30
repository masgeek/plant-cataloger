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
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'id' => 'ID',
            'reported_by' => 'Reported By',
            'country_code' => 'Country',
            'phone_number' => 'Phone Number',
            'disease_type' => 'Disease Type',
//            'solution' => 'Solution',
//            'date_reported' => 'Date Reported',
        ];
    }
}
