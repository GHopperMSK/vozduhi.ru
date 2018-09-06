<?php

namespace common\models;

use Yii;
use common\models\Attribute;
use common\models\DataType;
use common\models\Item;

/**
 * This is the base model class for any type of Values
 *
 * @property int $id
 * @property int $attribute_id
 * @property int $item_id
 * @property int $value
 *
 * @property Attributes $attribute0
 * @property Items $item
 */
abstract class Value extends \yii\db\ActiveRecord
{
    /**
     * Returns all existed values
     * @return array
     */
    static public function getFilterValues()
    {
        /**
         * If don't specify `$tableName`, `self::tableName()` will resolve the name
         * by `ActiveRecord::tableName()` which based on class name and returns incorrect
         * value due to class name doesn't corelate with its table name.
         */
        $className = self::className();
        $tableName = $className::tableName();

        return Yii::$app->db->createCommand("
            SELECT 
                DISTINCT value, 
                (
                    SELECT id
                    FROM {$tableName}
                    WHERE value = vtbl.value
                    LIMIT 1
                ) AS id
            FROM " . new \yii\db\Expression( "{$tableName} vtbl"))
            ->queryAll();
    }

    static public function addValue(Item $item, Attribute $attribute, $attributeValue)
    {
        $dataTypeName = $attribute->dataType->name;
        $valueClass = 'common\models\Value' . ucfirst($dataTypeName);
        $valueClass::add($item->id, $attribute->id, $attributeValue);
    }

    /**
     * Delete all values of the Item
     *
     * @param int $itemId
     */
    static public function purgeItem($itemId)
    {
        // delete all attributes
        $dataTypes = DataType::find()->select('name')->asArray()->column();
        foreach ($dataTypes as $dataType) {
            $className = 'common\models\Value' . ucfirst($dataType);
            $className::deleteAll(
                'item_id = :item_id',
                [':item_id' => $itemId]
            );
        }
    }

    /**
     * @param $itemId
     * @param $attributeId
     * @param $value
     * @throw UserException
     */
    abstract static function add($itemId, $attributeId, $value);

    /**
     * @param $itemId
     * @param $attributeId
     * @return string
     */
    abstract static function get($itemId, $attributeId);
}
