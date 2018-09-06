<?php

use yii\db\Migration;

/**
 * Handles adding slug to table `categories`.
 */
class m180716_071530_add_slug_column_to_categories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('categories', 'slug', $this->string()
            ->notNull()
            ->unique()
            ->after('name')
            ->defaultValue(new \yii\db\Expression('SUBSTRING(RANDOM()::text, 3)'))
        );

        $this->createIndex('categories-slug', '{{%categories}}', ['slug']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('categories', 'slug');
    }
}
