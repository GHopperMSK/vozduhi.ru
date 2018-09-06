<?php

use yii\db\Migration;

/**
 * Handles the creation of table `orders`.
 * Has foreign keys to the tables:
 *
 * - `order_status`
 */
class m180722_085018_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('orders', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'tel' => $this->string(),
            'address' => $this->text(),
            'status_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `status_id`
        $this->createIndex(
            'idx-orders-status_id',
            'orders',
            'status_id'
        );

        // add foreign key for table `order_status`
        $this->addForeignKey(
            'fk-orders-status_id',
            'orders',
            'status_id',
            'order_status',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `order_status`
        $this->dropForeignKey(
            'fk-orders-status_id',
            'orders'
        );

        // drops index for column `status_id`
        $this->dropIndex(
            'idx-orders-status_id',
            'orders'
        );

        $this->dropTable('orders');
    }
}
