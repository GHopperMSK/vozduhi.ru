<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brands`.
 */
class m180521_081040_create_brands_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('brands', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->unique(),
            'file' => $this->string(255),
            'description' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('brands');
    }
}
