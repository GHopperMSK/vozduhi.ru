<?php

use yii\db\Migration;

/**
 * Class m181010_084430_alter_columns_from_items_table
 */
class m181010_084430_alter_columns_from_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'items',
            'article',
            $this->string(64)->unique()->after('category_id')
        );

        // creates index for column `article`
        $this->createIndex(
            'idx-items-article',
            'items',
            'article'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `article`
        $this->dropIndex(
            'idx-items-article',
            'items'
        );

        $this->dropColumn('items', 'article');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181010_084430_alter_columns_from_items_table cannot be reverted.\n";

        return false;
    }
    */
}
