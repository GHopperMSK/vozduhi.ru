<?php

use yii\db\Migration;

/**
 * Handles the creation of table `gifts`.
 * Has foreign keys to the tables:
 *
 * - `items`
 * - `items`
 */
class m180809_215907_create_gifts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('gifts', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer()->notNull(),
            'gift' => $this->integer()->notNull(),
        ]);

        // creates index for column `item_id`
        $this->createIndex(
            'idx-gifts-item_id',
            'gifts',
            'item_id'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-gifts-item_id',
            'gifts',
            'item_id',
            'items',
            'id',
            'CASCADE'
        );

        // creates index for column `gift`
        $this->createIndex(
            'idx-gifts-gift',
            'gifts',
            'gift'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-gifts-gift',
            'gifts',
            'gift',
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
            'fk-gifts-item_id',
            'gifts'
        );

        // drops index for column `item_id`
        $this->dropIndex(
            'idx-gifts-item_id',
            'gifts'
        );

        // drops foreign key for table `items`
        $this->dropForeignKey(
            'fk-gifts-gift',
            'gifts'
        );

        // drops index for column `gift`
        $this->dropIndex(
            'idx-gifts-gift',
            'gifts'
        );

        $this->dropTable('gifts');
    }
}
