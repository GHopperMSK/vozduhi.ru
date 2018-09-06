<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%values_string}}".
 *
 * @property int $id
 * @property int $attribute_id
 * @property int $item_id
 * @property string $value
 *
 * @property Attributes $attribute0
 * @property Items $item
 */
class ValueString extends \common\models\Value
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%values_string}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_id', 'item_id'], 'required'],
            [['attribute_id', 'item_id'], 'default', 'value' => null],
            [['attribute_id', 'item_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attribute::className(), 'targetAttribute' => ['attribute_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_id' => 'Attribute ID',
            'item_id' => 'Item ID',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttribute0()
    {
        return $this->hasOne(Attribute::className(), ['id' => 'attribute_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    static function add($itemId, $attributeId, $value)
    {
        $value = trim($value);
        if (strlen($value)) {
            $valueObj = new ValueString();
            $valueObj->attribute_id = $attributeId;
            $valueObj->item_id = $itemId;
            $valueObj->value = $value;
            if (!$valueObj->save()) {
                throw new UserException('Value error!');
            }
        }
    }

    static function get($itemId, $attributeId)
    {
        $value = ValueString::find()
            ->select('value')
            ->where(['attribute_id' => $attributeId, 'item_id' => $itemId])
            ->asArray()
            ->one();

        if (!empty($value)) {
            $value = $value['value'];
        }

        return $value;
    }
}
