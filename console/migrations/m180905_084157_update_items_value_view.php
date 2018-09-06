<?php

use yii\db\Migration;

/**
 * Class m180905_084157_update_items_value_view
 */
class m180905_084157_update_items_value_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW items_value");

        $sql = '
            CREATE VIEW items_value AS
                SELECT 
                    items.id,
                    items.brand_id,
                    items.category_id,
                    items.name,
                    items.description,
                    (CASE WHEN "discounts".price IS NULL THEN "items".price ELSE "discounts".price END) AS price,
                    items.modified_at,
                    attributes.code, 
                    values_integer.value :: varchar(255)
                FROM "items" 
                LEFT JOIN "discounts" ON "items".id = "discounts".item_id
                LEFT JOIN "values_integer" ON "items".id = "values_integer".item_id 
                LEFT JOIN "attributes" ON "values_integer".attribute_id = "attributes".id 
                WHERE values_integer.value IS NOT NULL
                UNION
                SELECT 
                    items.id,
                    items.brand_id,
                    items.category_id,
                    items.name,
                    items.description,
                    (CASE WHEN "discounts".price IS NULL THEN "items".price ELSE "discounts".price END) AS price,
                    items.modified_at,
                    attributes.code, values_string.value :: varchar(255)
                FROM "items" 
                LEFT JOIN "discounts" ON "items".id = "discounts".item_id
                LEFT JOIN "values_string" ON "items".id = "values_string".item_id 
                LEFT JOIN "attributes" ON "values_string".attribute_id = "attributes".id 
                WHERE values_string.value IS NOT NULL
                UNION
                SELECT 
                    items.id,
                    items.brand_id,
                    items.category_id,
                    items.name,
                    items.description,
                    (CASE WHEN "discounts".price IS NULL THEN "items".price ELSE "discounts".price END) AS price,
                    items.modified_at,
                    attributes.code, values_list.value :: varchar(255)
                FROM "items" 
                LEFT JOIN "discounts" ON "items".id = "discounts".item_id
                LEFT JOIN "values_list" ON "items".id = "values_list".item_id 
                LEFT JOIN "attributes" ON "values_list".attribute_id = "attributes".id 
                WHERE values_list.value IS NOT NULL
                UNION
                SELECT 
                    items.id,
                    items.brand_id,
                    items.category_id,
                    items.name,
                    items.description,
                    (CASE WHEN "discounts".price IS NULL THEN "items".price ELSE "discounts".price END) AS price,
                    items.modified_at,
                    NULL, NULL
                FROM "items" 
                LEFT JOIN "discounts" ON "items".id = "discounts".item_id
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
