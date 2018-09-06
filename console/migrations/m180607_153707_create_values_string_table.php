<?php

use yii\db\Migration;

/**
 * Handles the creation of table `values_string`.
 * Has foreign keys to the tables:
 *
 * - `attributes`
 * - `items`
 */
class m180607_153707_create_values_string_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('values_string', [
            'id' => $this->primaryKey(),
            'attribute_id' => $this->integer()->notNull(),
            'item_id' => $this->integer()->notNull(),
            'value' => $this->string(255),
        ]);

        // creates index for column `attribute_id`
        $this->createIndex(
            'idx-values_string-attribute_id',
            'values_string',
            'attribute_id'
        );

        // add foreign key for table `attributes`
        $this->addForeignKey(
            'fk-values_string-attribute_id',
            'values_string',
            'attribute_id',
            'attributes',
            'id',
            'CASCADE'
        );

        // creates index for column `item_id`
        $this->createIndex(
            'idx-values_string-item_id',
            'values_string',
            'item_id'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-values_string-item_id',
            'values_string',
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
            'fk-values_string-attribute_id',
            'values_string'
        );

        // drops index for column `attribute_id`
        $this->dropIndex(
            'idx-values_string-attribute_id',
            'values_string'
        );

        // drops foreign key for table `items`
        $this->dropForeignKey(
            'fk-values_string-item_id',
            'values_string'
        );

        // drops index for column `item_id`
        $this->dropIndex(
            'idx-values_string-item_id',
            'values_string'
        );

        $this->dropTable('values_string');
    }
}
