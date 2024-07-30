<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%disease_report_images}}`.
 */
class m240730_084944_create_disease_report_images_table extends \app\common\migration\BaseMigration
{
    public string $tableName = '{{%disease_report_images}}';
    public bool $addTimestamps = true;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(table: $this->tableName, columns: [
            'id' => $this->bigPrimaryKey(),
            'disease_report_id' => $this->bigInteger()->notNull(),
            'image_path' => $this->string()->notNull(),
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
