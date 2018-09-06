<?php

namespace common\models;

use Yii;
use common\models\Attribute;

/**
 * This is the model class for table "{{%values_list}}".
 *
 * @property int $id
 * @property int $attribute_id
 * @property int $item_id
 * @property string $value
 *
 * @property Attributes $attribute0
 * @property Items $item
 */
class ValueList extends \common\models\Value
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%values_list}}';
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
        foreach (explode("\r\n", $value) as $val) {
            $val = trim($val);
            if (strlen($value)) {
                $valueObj = new ValueList();
                $valueObj->attribute_id = $attributeId;
                $valueObj->item_id = $itemId;
                $valueObj->value = $val;
                if (!$valueObj->save()) {
                    throw new UserException('Value error!');
                }
            }
        }
    }

    static function get($itemId, $attributeId)
    {
        $values = ValueList::find()
            ->select('value')
            ->where(['attribute_id' => $attributeId, 'item_id' => $itemId])
            ->asArray()
            ->column();
        $value = implode("\n", $values);

        return $value;
    }
}
