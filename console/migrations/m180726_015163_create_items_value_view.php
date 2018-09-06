<?php

use yii\db\Migration;

/**
 * Handles the creation of view `itemsValue`.
 */
class m180726_015163_create_items_value_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = '
            CREATE VIEW itemsValue AS
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
        $this->execute("DROP VIEW itemsValue");
    }
}
