<?php

use yii\db\Migration;

/**
 * Handles adding date to table `items`.
 */
class m180701_205912_add_date_column_to_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('items', 'modified_at',
            $this->timestamp()
                ->notNull()
                ->after('price')
                ->defaultValue(new \yii\db\Expression('NOW()'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('items', 'modified_at');
    }
}
