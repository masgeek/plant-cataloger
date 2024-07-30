<?php

use yii\db\Migration;

/**
 * Class m240730_090608_add_disease_report_fk_to_images_table
 */
class m240730_090608_add_disease_report_fk_to_images_table extends \app\common\migration\BaseMigration
{
    public string $refTable = '{{%disease_reports}}';
    public string $tableName = '{{%disease_report_images}}';
    public string $fkName = 'disease-report-id-fk';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey(name: $this->fkName, table: $this->tableName,
            columns: 'disease_report_id',
            refTable: $this->refTable, refColumns: 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(name: $this->fkName, table: $this->tableName);
    }
}
