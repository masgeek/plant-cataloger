<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%countries}}`.
 */
class m240730_120429_create_countries_table extends \app\common\migration\BaseMigration
{
    public string $tableName = '{{%countries}}';
    public bool $addTimestamps = true;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(table: $this->tableName, columns: [
            'id' => $this->bigPrimaryKey(),
            'country_name' => $this->string(50)->notNull()->unique(),
            'country_code' => $this->string(4)->notNull()->unique()->comment('Use the alpha2 code')
        ], options: $this->tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
