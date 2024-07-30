<?php

namespace app\models;

use \app\models\base\DiseaseReportImage as BaseDiseaseReportImages;

/**
 * This is the model class for table "disease_report_images".
 */
class DiseaseReportImage extends BaseDiseaseReportImages
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
            [
                [['disease_report_id', 'image_path'], 'required'],
                [['disease_report_id', 'created_at', 'updated_at'], 'integer'],
                [['image_path'], 'string', 'max' => 255]
            ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'id' => 'ID',
            'disease_report_id' => 'Disease Report ID',
            'image_path' => 'Image Path',
        ];
    }
}
