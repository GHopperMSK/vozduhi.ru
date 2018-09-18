<?php

use yii\db\Migration;

/**
 * Handles adding modified_at to table `orders`.
 */
class m180918_101328_add_modified_at_column_to_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('orders', 'modified_at',
            $this->timestamp()
                ->notNull()
                ->after('status_id')
                ->defaultValue(new \yii\db\Expression('NOW()'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('orders', 'modified_at');
    }
}
