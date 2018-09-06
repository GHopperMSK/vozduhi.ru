<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand_slug`.
 * Has foreign keys to the tables:
 *
 * - `brands`
 */
class m180816_091048_create_brand_slug_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('brand_slug', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer()->notNull(),
            'slug' => $this->string()->notNull()->unique(),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression('NOW()')),
        ]);

        // creates index for column `brand_id`
        $this->createIndex(
            'idx-brand_slug-brand_id',
            'brand_slug',
            'brand_id'
        );

        // creates index for column `slug`
        $this->createIndex(
            'idx-brand_slug-slug',
            'brand_slug',
            'slug'
        );

        $sql = '
            INSERT INTO brand_slug(brand_id, slug)
            SELECT id, SUBSTRING(RANDOM()::text, 3)
            FROM brands
        ';

        $this->execute($sql);

        // add foreign key for table `brands`
        $this->addForeignKey(
            'fk-brand_slug-brand_id',
            'brand_slug',
            'brand_id',
            'brands',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `brands`
        $this->dropForeignKey(
            'fk-brand_slug-brand_id',
            'brand_slug'
        );

        // drops index for column `brand_id`
        $this->dropIndex(
            'idx-brand_slug-brand_id',
            'brand_slug'
        );

        // drops index for column `slug`
        $this->dropIndex(
            'idx-brand_slug-slug',
            'brand_slug'
        );

        $this->dropTable('brand_slug');
    }
}
