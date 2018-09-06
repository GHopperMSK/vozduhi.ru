<?php

use yii\db\Migration;

/**
 * Handles the creation of table `values_list`.
 * Has foreign keys to the tables:
 *
 * - `attributes`
 * - `items`
 */
class m180607_153715_create_values_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('values_list', [
            'id' => $this->primaryKey(),
            'attribute_id' => $this->integer()->notNull(),
            'item_id' => $this->integer()->notNull(),
            'value' => $this->string(255),
        ]);

        // creates index for column `attribute_id`
        $this->createIndex(
            'idx-values_list-attribute_id',
            'values_list',
            'attribute_id'
        );

        // add foreign key for table `attributes`
        $this->addForeignKey(
            'fk-values_list-attribute_id',
            'values_list',
            'attribute_id',
            'attributes',
            'id',
            'CASCADE'
        );

        // creates index for column `item_id`
        $this->createIndex(
            'idx-values_list-item_id',
            'values_list',
            'item_id'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-values_list-item_id',
            'values_list',
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
        // drops foreign key for table `attributes`
        $this->dropForeignKey(
            'fk-values_list-attribute_id',
            'values_list'
        );

        // drops index for column `attribute_id`
        $this->dropIndex(
            'idx-values_list-attribute_id',
            'values_list'
        );

        // drops foreign key for table `items`
        $this->dropForeignKey(
            'fk-values_list-item_id',
            'values_list'
        );

        // drops index for column `item_id`
        $this->dropIndex(
            'idx-values_list-item_id',
            'values_list'
        );

        $this->dropTable('values_list');
    }
}
