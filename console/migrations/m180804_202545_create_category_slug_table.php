<?php

use yii\db\Migration;

/**
 * Handles the creation of table `category_slug`.
 * Has foreign keys to the tables:
 *
 * - `categories`
 */
class m180804_202545_create_category_slug_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category_slug', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'slug' => $this->string()->notNull()->unique(),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression('NOW()')),
        ]);

        // creates index for column `category_id`
        $this->createIndex(
            'idx-category_slug-category_id',
            'category_slug',
            'category_id'
        );

        // creates index for column `slug`
        $this->createIndex(
            'idx-category_slug-slug',
            'category_slug',
            'slug'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-category_slug-category_id',
            'category_slug',
            'category_id',
            'categories',
            'id',
            'CASCADE'
        );

        $sql = '
            INSERT INTO category_slug(category_id, slug)
            SELECT id, slug
            FROM categories
        ';

        $this->execute($sql);

        $this->dropColumn('categories', 'slug');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('categories', 'slug', $this->string()
            ->notNull()
            ->unique()
            ->after('name')
            ->defaultValue(new \yii\db\Expression('SUBSTRING(RANDOM()::text, 3)'))
        );

        $this->createIndex('categories-slug', '{{%categories}}', ['slug']);

        // drops foreign key for table `categories`
        $this->dropForeignKey(
            'fk-category_slug-category_id',
            'category_slug'
        );

//        $sql = '
//            UPDATE categories SET slug=catslug.slug
//            FROM (
//                SELECT DISTINCT category_id, (
//                    SELECT slug
//                    FROM category_slug
//                    WHERE category_id = sl.category_id
//                    ORDER BY created_at DESC LIMIT 1
//                ) as slug
//                FROM category_slug sl
//            ) as catslug
//            WHERE category_id = catslug.category_id
//        ';
//
//        $this->execute($sql);

        // drops index for column `category_id`
        $this->dropIndex(
            'idx-category_slug-category_id',
            'category_slug'
        );

        // drops index for column `item_id`
        $this->dropIndex(
            'idx-category_slug-slug',
            'category_slug'
        );

        $this->dropTable('category_slug');
    }
}
