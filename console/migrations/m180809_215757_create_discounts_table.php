<?php

use yii\db\Migration;

/**
 * Handles the creation of table `discounts`.
 * Has foreign keys to the tables:
 *
 * - `items`
 */
class m180809_215757_create_discounts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('discounts', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer()->unique()->notNull(),
            'price' => $this->integer()->notNull(),
        ]);

        // creates index for column `item_id`
        $this->createIndex(
            'idx-discounts-item_id',
            'discounts',
            'item_id'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-discounts-item_id',
            'discounts',
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
        // drops foreign key for table `items`
        $this->dropForeignKey(
            'fk-discounts-item_id',
            'discounts'
        );

        // drops index for column `item_id`
        $this->dropIndex(
            'idx-discounts-item_id',
            'discounts'
        );

        $this->dropTable('discounts');
    }
}
