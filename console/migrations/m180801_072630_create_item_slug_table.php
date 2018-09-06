<?php

use yii\db\Migration;

/**
 * Handles the creation of table `item_slug`.
 * Has foreign keys to the tables:
 *
 * - `items`
 */
class m180801_072630_create_item_slug_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('item_slug', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer()->notNull(),
            'slug' => $this->string()->notNull()->unique(),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression('NOW()')),
        ]);

        // creates index for column `item_id`
        $this->createIndex(
            'idx-item_slug-item_id',
            'item_slug',
            'item_id'
        );

        // creates index for column `slug`
        $this->createIndex(
            'idx-item_slug-slug',
            'item_slug',
            'slug'
        );

        // add foreign key for table `items`
        $this->addForeignKey(
            'fk-item_slug-item_id',
            'item_slug',
            'item_id',
            'items',
            'id',
            'CASCADE'
        );

        $sql = '
            INSERT INTO item_slug(item_id, slug)
            SELECT id, slug
            FROM items
        ';

        $this->execute($sql);

        $this->execute("DROP VIEW items_value");

        // drops index for column `slug`
        $this->dropIndex(
            'items-slug',
            'items'
        );

        $this->dropColumn('items', 'slug');

        $sql = '
            CREATE VIEW items_value AS
                SELECT items.*, attributes.code, values_integer.value :: varchar(255)
                FROM "items" 
                LEFT JOIN "values_integer" ON "items".id = "values_integer".item_id 
                LEFT JOIN "attributes" ON "values_integer".attribute_id = "attributes".id 
                WHERE values_integer.value IS NOT NULL
                UNION
                SELECT items.*, attributes.code, values_string.value :: varchar(255)
                FROM "items" 
                LEFT JOIN "values_string" ON "items".id = "values_string".item_id 
                LEFT JOIN "attributes" ON "values_string".attribute_id = "attributes".id 
                WHERE values_string.value IS NOT NULL
                UNION
                SELECT items.*, attributes.code, values_list.value :: varchar(255)
                FROM "items" 
                LEFT JOIN "values_list" ON "items".id = "values_list".item_id 
                LEFT JOIN "attributes" ON "values_list".attribute_id = "attributes".id 
                WHERE values_list.value IS NOT NULL
                UNION
                SELECT items.*, NULL, NULL
                FROM "items" 
                LEFT JOIN "values_integer" ON "items".id = "values_integer".item_id
                LEFT JOIN "values_string" ON "items".id = "values_string".item_id
                LEFT JOIN "values_list" ON "items".id = "values_list".item_id
                WHERE values_integer.value IS NULL AND values_string.value IS NULL AND values_list.value IS NULL;        
        ';

        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('items', 'slug', $this->string()
            ->notNull()
            ->unique()
            ->after('name')
            ->defaultValue(new \yii\db\Expression('SUBSTRING(RANDOM()::text, 3)'))
        );

        $this->createIndex('items-slug', '{{%items}}', ['slug']);

        // drops foreign key for table `items`
        $this->dropForeignKey(
            'fk-item_slug-item_id',
            'item_slug'
        );

        // drops index for column `item_id`
        $this->dropIndex(
            'idx-item_slug-item_id',
            'item_slug'
        );

        // drops index for column `slug`
        $this->dropIndex(
            'idx-item_slug-slug',
            'item_slug'
        );

        $this->dropTable('item_slug');

        $this->execute("DROP VIEW items_value");

        $sql = '
            CREATE VIEW items_value AS
                SELECT items.*, attributes.code, values_integer.value :: varchar(255)
                FROM "items" 
                LEFT JOIN "values_integer" ON "items".id = "values_integer".item_id 
                LEFT JOIN "attributes" ON "values_integer".attribute_id = "attributes".id 
                WHERE values_integer.value IS NOT NULL
                UNION
                SELECT items.*, attributes.code, values_string.value :: varchar(255)
                FROM "items" 
                LEFT JOIN "values_string" ON "items".id = "values_string".item_id 
                LEFT JOIN "attributes" ON "values_string".attribute_id = "attributes".id 
                WHERE values_string.value IS NOT NULL
                UNION
                SELECT items.*, attributes.code, values_list.value :: varchar(255)
                FROM "items" 
                LEFT JOIN "values_list" ON "items".id = "values_list".item_id 
                LEFT JOIN "attributes" ON "values_list".attribute_id = "attributes".id 
                WHERE values_list.value IS NOT NULL
                UNION
                SELECT items.*, NULL, NULL
                FROM "items" 
                LEFT JOIN "values_integer" ON "items".id = "values_integer".item_id
                LEFT JOIN "values_string" ON "items".id = "values_string".item_id
                LEFT JOIN "values_list" ON "items".id = "values_list".item_id
                WHERE values_integer.value IS NULL AND values_string.value IS NULL AND values_list.value IS NULL;        
        ';

        $this->execute($sql);

    }
}
