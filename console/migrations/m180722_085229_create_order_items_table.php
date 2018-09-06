<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order_items`.
 * Has foreign keys to the tables:
 *
 * - `orders`
 * - `items`
 */
class m180722_085229_create_order_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('order_items', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'item_id' => $this->integer()->notNull(),
            'count' => $this->integer()->defaultValue(1),
        ]);

        // creates index for column `order_id`
        $this->createIndex(
            'idx-order_items-order_id',
            'order_items',
            'order_id'
        );

        // add foreign key for table `orders`
        $this->addForeignKey(
            'fk-order_items-order_id',
            'order_items',
            'order_id',
            'orders',
            'id',
            'CASCADE'
        );

        // creates index for column `item_id`
        $this->createIndex(
            'idx-order_items-item_id',
            'order_items',
            'item_id'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-order_items-item_id',
            'order_items',
            'item_id',
            'items',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `orders`
        $this->dropForeignKey(
            'fk-order_items-order_id',
            'order_items'
        );

        // drops index for column `order_id`
        $this->dropIndex(
            'idx-order_items-order_id',
            'order_items'
        );

        // drops foreign key for table `items`
        $this->dropForeignKey(
            'fk-order_items-item_id',
            'order_items'
        );

        // drops index for column `item_id`
        $this->dropIndex(
            'idx-order_items-item_id',
            'order_items'
        );

        $this->dropTable('order_items');
    }
}
