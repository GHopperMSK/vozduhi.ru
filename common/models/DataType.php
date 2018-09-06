<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%data_types}}".
 *
 * @property int $id
 * @property string $name
 *
 * @property Attributes[] $attributes0
 */
class DataType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%data_types}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributes0()
    {
        return $this->hasMany(Attributes::className(), ['data_type_id' => 'id']);
    }
}
