<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%discounts}}".
 *
 * @property int $id
 * @property int $item_id
 * @property int $price
 *
 * @property Items $item
 */
class Discount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%discounts}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id'], 'required'],
            [['item_id', 'price'], 'default', 'value' => null],
            [['item_id', 'price'], 'integer'],
            [['item_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Item::className(),
                'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Item ID',
            'price' => 'Discount',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * Delete discount of the Item
     *
     * @param int $itemId
     */
    static public function purgeItem($itemId)
    {
        self::deleteAll(
            'item_id = :item_id',
            [':item_id' => $itemId]
        );
    }
}
