<?php

use yii\db\Migration;

/**
 * Class m240731_062047_add_country_code_fk_to_disease_reports_table
 */
class m240731_062047_add_country_code_fk_to_disease_reports_table extends \app\common\migration\BaseMigration
{
    public string $refTable = '{{%countries}}';
    public string $tableName = '{{%disease_reports}}';
    public string $fkName = 'country-code-fk';

    public function safeUp()
    {
        $this->addForeignKey(name: $this->fkName, table: $this->tableName,
            columns: 'country_code',
            refTable: $this->refTable,
            refColumns: 'country_code', delete: 'RESTRICT', update: 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(name: $this->fkName, table: $this->tableName);
    }
}
