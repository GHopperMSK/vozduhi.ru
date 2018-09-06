<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%recommended}}".
 *
 * @property int $id
 * @property int $item_id
 * @property int $recommended_item_id
 *
 * @property Items $item
 * @property Items $recommendedItem
 */
class Recommended extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%recommended}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'recommended_item_id'], 'default', 'value' => null],
            [['item_id', 'recommended_item_id'], 'integer'],
            [['recommended_item_id'], 'required'],
            [['item_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Item::className(),
                'targetAttribute' => ['item_id' => 'id']
            ],
            [['recommended_item_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Item::className(),
                'targetAttribute' => ['recommended_item_id' => 'id']
            ],
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
            'recommended_item_id' => 'Recommended Item ID',
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
     * @return \yii\db\ActiveQuery
     */
    public function getRecommendedItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'recommended_item_id']);
    }

    /**
     * Delete all recommended with given Item
     * @param $itemId
     */
    public static function purgeItem($itemId) {
        Recommended::deleteAll(
            'item_id = :item',
            [':item' => $itemId]
        );
    }
}
