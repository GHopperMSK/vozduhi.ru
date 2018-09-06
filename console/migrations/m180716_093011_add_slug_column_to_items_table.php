<?php

use yii\db\Migration;

/**
 * Handles adding slug to table `items`.
 */
class m180716_093011_add_slug_column_to_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('items', 'slug', $this->string()
            ->notNull()
            ->unique()
            ->after('name')
            ->defaultValue(new \yii\db\Expression('SUBSTRING(RANDOM()::text, 3)'))
        );

        $this->createIndex('items-slug', '{{%items}}', ['slug']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `slug`
        $this->dropIndex(
            'items-slug',
            'items'
        );

        $this->dropColumn('items', 'slug');
    }
}
