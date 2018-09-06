<?php

use yii\db\Migration;

/**
 * Handles the creation of table `categories_attributes`.
 * Has foreign keys to the tables:
 *
 * - `categories`
 * - `attributes`
 */
class m180606_092740_create_junction_table_for_categories_and_attributes_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('categories_attributes', [
            'category_id' => $this->integer(),
            'attribute_id' => $this->integer(),
            'pos' => $this->integer(),
            'PRIMARY KEY(category_id, attribute_id)',
        ]);

        // creates index for column `categories_id`
        $this->createIndex(
            'idx-categories_attributes-categories_id',
            'categories_attributes',
            'category_id'
        );

        // add foreign key for table `categories`
        $this->addForeignKey(
            'fk-categories_attributes-categories_id',
            'categories_attributes',
            'category_id',
            'categories',
            'id',
            'CASCADE'
        );

        // creates index for column `attributes_id`
        $this->createIndex(
            'idx-categories_attributes-attributes_id',
            'categories_attributes',
            'attribute_id'
        );

        // add foreign key for table `attributes`
        $this->addForeignKey(
            'fk-categories_attributes-attributes_id',
            'categories_attributes',
            'attribute_id',
            'attributes',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `categories`
        $this->dropForeignKey(
            'fk-categories_attributes-categories_id',
            'categories_attributes'
        );

        // drops index for column `categories_id`
        $this->dropIndex(
            'idx-categories_attributes-categories_id',
            'categories_attributes'
        );

        // drops foreign key for table `attributes`
        $this->dropForeignKey(
            'fk-categories_attributes-attributes_id',
            'categories_attributes'
        );

        // drops index for column `attributes_id`
        $this->dropIndex(
            'idx-categories_attributes-attributes_id',
            'categories_attributes'
        );

        $this->dropTable('categories_attributes');
    }
}
