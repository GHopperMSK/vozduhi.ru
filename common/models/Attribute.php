<?php

namespace common\models;

use Yii;
use common\models\ValueInteger;
use common\models\ValueString;
use common\models\ValueList;

/**
 * This is the model class for table "{{%attributes}}".
 *
 * @property int $id
 * @property int $data_type_id
 * @property string $code
 * @property string $name
 *
 * @property DataType $dataType
 * @property CategoriesAttributes[] $categoriesAttributes
 * @property Categories[] $categories
 * @property ValuesInteger[] $valuesIntegers
 * @property ValuesList[] $valuesLists
 * @property ValuesString[] $valuesStrings
 */
class Attribute extends \yii\db\ActiveRecord
{
    /**
     * @var string
     */
    public $uploadedValue;

    public $dataTypeName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%attributes}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data_type_id', 'name', 'code'], 'required'],
            [['data_type_id'], 'default', 'value' => null],
            [['data_type_id'], 'integer'],
            [['name', 'code'], 'string', 'max' => 255],
            [['data_type_id'], 'exist', 'skipOnError' => true,
                'targetClass' => DataType::className(),
                'targetAttribute' => ['data_type_id' => 'id']
            ],
            [['uploadedValue'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'code' => 'Code',
            'dataTypeName' => 'Data type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDataType()
    {
        return $this->hasOne(DataType::className(), ['id' => 'data_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoriesAttributes()
    {
        return $this->hasMany(CategoriesAttributes::className(), ['attributes_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasMany(Categories::className(), ['id' => 'category_id'])
            ->viaTable('{{%category_attributes}}', ['attribute_id' => 'id']);
    }

    /**
     * Returns all existed values of the attribute
     * @return array
     */
    public function getFilterValues()
    {
        $dataTypeName = $this->dataType->name;
        $valueClass = 'common\models\Value' . ucfirst($dataTypeName);

        return $valueClass::getFilterValues();
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function getValueByItem(Item $item)
    {
        $dataTypeName = $this->dataType->name;
        $valueClass = 'common\models\Value' . ucfirst($dataTypeName);

        return $valueClass::get($item->id, $this->id);
    }

    /**
     * @param integer $valueId
     * @return mixed
     */
    public function getValueById($valueId)
    {
        $dataTypeName = $this->dataType->name;
        $valueClass = 'common\models\Value' . ucfirst($dataTypeName);

        return $valueClass::findOne(['id' => $valueId]);
    }

    /**
     * @param Item $item
     * @param string|null $attributeValue
     * @throws \Exception
     */
    public function setValue(Item $item, $attributeValue = null)
    {
        $dataTypeName = $this->dataType->name;
        $valueClass = 'common\models\Value' . ucfirst($dataTypeName);

        try {
            $valueClass::add($item->id, $this->id, isset($attributeValue) ? $attributeValue : $this->uploadedValue);
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Delete all Values of the Attribute
     */
    public function purgeValues()
    {
        // delete all values
        $dataTypes = DataType::find()->select('name')->asArray()->column();
        foreach ($dataTypes as $dataType) {
            $className = 'common\models\Value' . ucfirst($dataType);
            $className::deleteAll(
                'attribute_id = :attribute_id',
                [':attribute_id' => $this->id]
            );
        }
    }
}
