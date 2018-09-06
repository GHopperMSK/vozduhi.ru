<?php

use yii\db\Migration;

/**
 * Handles the creation of table `data_types`.
 */
class m180606_080715_create_data_types_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('data_types', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
        ]);

        $this->insert('data_types', [
            'name' => 'integer'
        ]);
        $this->insert('data_types', [
            'name' => 'string'
        ]);
        $this->insert('data_types', [
            'name' => 'list'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('data_types');
    }
}
