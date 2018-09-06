<?php

use yii\db\Migration;

/**
 * Handles the creation of table `items`.
 * Has foreign keys to the tables:
 *
 * - `brands`
 * - `categories`
 */
class m180530_143556_create_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('items', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer(),
            'category_id' => $this->integer(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
        ]);

        // creates index for column `brand_id`
        $this->createIndex(
            'idx-items-brand_id',
            'items',
            'brand_id'
        );

        // add foreign key for table `brands`
        $this->addForeignKey(
            'fk-items-brand_id',
            'items',
            'brand_id',
            'brands',
            'id',
            'CASCADE'
        );

        // creates index for column `category_id`
        $this->createIndex(
            'idx-items-category_id',
            'items',
            'category_id'
        );

        // add foreign key for table `categories`
        $this->addForeignKey(
            'fk-items-category_id',
            'items',
            'category_id',
            'categories',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `brands`
        $this->dropForeignKey(
            'fk-items-brand_id',
            'items'
        );

        // drops index for column `brand_id`
        $this->dropIndex(
            'idx-items-brand_id',
            'items'
        );

        // drops foreign key for table `categories`
        $this->dropForeignKey(
            'fk-items-category_id',
            'items'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            'idx-items-category_id',
            'items'
        );

        $this->dropTable('items');
    }
}
