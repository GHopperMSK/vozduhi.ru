<?php

use yii\db\Migration;

/**
 * Handles the creation of table `recommended`.
 * Has foreign keys to the tables:
 *
 * - `items`
 * - `items`
 */
class m180702_071340_create_recommended_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('recommended', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer(),
            'recommended_item_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `item_id`
        $this->createIndex(
            'idx-recommended-item_id',
            'recommended',
            'item_id'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-recommended-item_id',
            'recommended',
            'item_id',
            'items',
            'id',
            'CASCADE'
        );

        // creates index for column `recommended`
        $this->createIndex(
            'idx-recommended-recommended_item_id',
            'recommended',
            'recommended_item_id'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-recommended-recommended_item_id',
            'recommended',
            'recommended_item_id',
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
            'fk-recommended-item_id',
            'recommended'
        );

        // drops index for column `item_id`
        $this->dropIndex(
            'idx-recommended-item_id',
            'recommended'
        );

        // drops foreign key for table `items`
        $this->dropForeignKey(
            'fk-recommended-recommended_item_id',
            'recommended'
        );

        // drops index for column `recommended`
        $this->dropIndex(
            'idx-recommended-recommended_item_id',
            'recommended'
        );

        $this->dropTable('recommended');
    }
}
