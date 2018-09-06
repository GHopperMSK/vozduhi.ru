<?php

use yii\db\Migration;

/**
 * Handles the creation of table `items`.
 * Has foreign keys to the tables:
 *
 * - `brands`
 * - `categories`
 */
class m180531_143243_create_images_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('images', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'alt' => $this->string(255),
            'pos' => $this->integer(),
        ]);

        // creates index for column `item_id`
        $this->createIndex(
            'idx-image-item_id',
            'images',
            'item_id'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-items-id',
            'images',
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
            'fk-items-id',
            'images'
        );

        $this->dropTable('images');
    }
}
