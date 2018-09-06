<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%gifts}}".
 *
 * @property int $id
 * @property int $item_id
 * @property int $gift
 *
 * @property Items $item
 * @property Items $itemGift
 */
class Gift extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%gifts}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'gift'], 'required'],
            [['item_id', 'gift'], 'default', 'value' => null],
            [['item_id', 'gift'], 'integer'],
            [['item_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Item::className(),
                'targetAttribute' => ['item_id' => 'id']],
            [['gift'], 'exist', 'skipOnError' => true,
                'targetClass' => Item::className(),
                'targetAttribute' => ['gift' => 'id']],
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
            'gift' => 'Gift',
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
    public function getItemGift()
    {
        return $this->hasOne(Item::className(), ['id' => 'gift']);
    }

    /**
     * Delete all gifts of the Item
     * @param $itemId
     */
    public static function purgeItem($itemId) {
        Gift::deleteAll(
            'item_id = :item_id',
            [':item_id' => $itemId]
        );
    }
}
