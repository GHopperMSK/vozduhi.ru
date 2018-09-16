<?php

use yii\db\Migration;

/**
 * Class m180916_085443_add_isactive_columt_to_items_table
 */
class m180916_085443_add_isactive_columt_to_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('items', 'active', $this->boolean()
            ->defaultValue(false)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('items', 'active');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180916_085443_add_isactive_columt_to_items_table cannot be reverted.\n";

        return false;
    }
    */
}
