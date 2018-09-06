<?php

use yii\db\Migration;

/**
 * Handles the creation of table `values_integer`.
 * Has foreign keys to the tables:
 *
 * - `attributes`
 * - `items`
 */
class m180607_153657_create_values_integer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('values_integer', [
            'id' => $this->primaryKey(),
            'attribute_id' => $this->integer()->notNull(),
            'item_id' => $this->integer()->notNull(),
            'value' => $this->integer(),
        ]);

        // creates index for column `attribute_id`
        $this->createIndex(
            'idx-values_integer-attribute_id',
            'values_integer',
            'attribute_id'
        );

        // add foreign key for table `attributes`
        $this->addForeignKey(
            'fk-values_integer-attribute_id',
            'values_integer',
            'attribute_id',
            'attributes',
            'id',
            'CASCADE'
        );

        // creates index for column `item_id`
        $this->createIndex(
            'idx-values_integer-item_id',
            'values_integer',
            'item_id'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-values_integer-item_id',
            'values_integer',
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
            'fk-values_integer-attribute_id',
            'values_integer'
        );

        // drops index for column `attribute_id`
        $this->dropIndex(
            'idx-values_integer-attribute_id',
            'values_integer'
        );

        // drops foreign key for table `items`
        $this->dropForeignKey(
            'fk-values_integer-item_id',
            'values_integer'
        );

        // drops index for column `item_id`
        $this->dropIndex(
            'idx-values_integer-item_id',
            'values_integer'
        );

        $this->dropTable('values_integer');
    }
}
