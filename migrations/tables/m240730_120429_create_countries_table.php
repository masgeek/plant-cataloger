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
            'country_code' => $this->string(2)->notNull()->unique()->comment('Use the alpha2 code'),
            'country_code_3' => $this->string(4)->notNull()->unique()->comment('Use the alpha2 code'),
            'country_name' => $this->string(50)->notNull(),
        ], options: $this->tableOptions);

        $this->addPrimaryKey(name: 'pk_country_code', table: $this->tableName, columns: 'country_code');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
