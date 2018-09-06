<?php

use yii\db\Migration;

/**
 * Class m180809_220256_rename_categories_attrubutes_table
 */
class m180809_220256_rename_categories_attrubutes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('{{%categories_attributes}}', '{{%category_attributes}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('{{%category_attributes}}', '{{%categories_attributes}}');
    }
}
