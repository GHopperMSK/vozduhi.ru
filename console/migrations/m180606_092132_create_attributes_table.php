<?php

use yii\db\Migration;

/**
 * Handles the creation of table `attributes`.
 * Has foreign keys to the tables:
 *create
 * - `data_types`
 */
class m180606_092132_create_attributes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('attributes', [
            'id' => $this->primaryKey(),
            'data_type_id' => $this->integer()->notNull(),
            'code' => $this->string(255)->unique()->notNull(),
            'name' => $this->string(255)->notNull(),
        ]);

        // creates index for column `data_type_id`
        $this->createIndex(
            'idx-attributes-data_type_id',
            'attributes',
            'data_type_id'
        );

        // creates index for column `code`
        $this->createIndex(
            'idx-attributes-code',
            'attributes',
            'code'
        );

        // add foreign key for table `data_types`
        $this->addForeignKey(
            'fk-attributes-data_type_id',
            'attributes',
            'data_type_id',
            'data_types',
            'id',
            'CASCADE'
        );
    }

    /**create
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `data_types`
        $this->dropForeignKey(
            'fk-attributes-data_type_id',
            'attributes'
        );

        // drops index for column `data_type_id`
        $this->dropIndex(
            'idx-attributes-data_type_id',
            'attributes'
        );

        // drops index for column `code`
        $this->dropIndex(
            'idx-attributes-code',
            'attributes'
        );

        $this->dropTable('attributes');
    }
}
