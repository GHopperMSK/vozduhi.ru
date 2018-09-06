<?php

use yii\db\Migration;

/**
 * Handles adding price to table `items`.
 */
class m180627_123106_add_price_column_to_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('items', 'price', $this->integer()->after('description'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('items', 'price');
    }
}
