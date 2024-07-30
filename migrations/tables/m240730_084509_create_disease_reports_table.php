<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%disease_reports}}`.
 */
class m240730_084509_create_disease_reports_table extends \app\common\migration\BaseMigration
{
    public string $tableName = '{{%disease_reports}}';
    public bool $addTimestamps = true;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(table: $this->tableName, columns: [
            'id' => $this->bigPrimaryKey(),
            'reported_by' => $this->string()->notNull(),
            'country_code' => $this->string(4)->notNull(),
            'phone_number' => $this->string(15)->notNull(),
            'disease_type' => $this->string(),
            'solution' => $this->text()->notNull(),
            'date_reported' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),

        ], options: $this->tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(table: $this->tableName);
    }
}
