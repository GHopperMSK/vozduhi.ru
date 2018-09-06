<?php

use yii\db\Migration;

/**
 * Handles the creation of table `relations`.
 * Has foreign keys to the tables:
 *
 * - `items`
 * - `items`
 */
class m180602_184428_create_relations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('relations', [
            'r1' => $this->integer()->notNull(),
            'r2' => $this->integer()->notNull(),
            'PRIMARY KEY(r1, r2)'
        ]);

        // creates index for column `r1`
        $this->createIndex(
            'idx-relations-r1',
            'relations',
            'r1'
        );

        // add foreign key for table `item`
        $this->addForeignKey(
            'fk-relations-r1',
            'relations',
            'r1',
            'items',
            'id',
            'CASCADE'
        );

        // creates index for column `r2`
        $this->createIndex(
            'idx-relations-r2',
            'relations',
            'r2'
        );

        // add foreign key for table `item`
        $this->addForeignKey(
            'fk-relations-r2',
            'relations',
            'r2',
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
            'fk-relations-r1',
            'relations'
        );

        // drops index for column `r1`
        $this->dropIndex(
            'idx-relations-r1',
            'relations'
        );

        // drops foreign key for table `items`
        $this->dropForeignKey(
            'fk-relations-r2',
            'relations'
        );

        // drops index for column `r2`
        $this->dropIndex(
            'idx-relations-r2',
            'relations'
        );

        $this->dropTable('relations');
    }
}
