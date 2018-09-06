<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%relations}}".
 *
 * @property int $r1
 * @property int $r2
 *
 * @property Item $r10
 * @property Item $r20
 */
class Relation extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%relations}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['r1', 'r2'], 'required'],
            [['r1', 'r2'], 'default', 'value' => null],
            [['r1', 'r2'], 'integer'],
            [['r1'], 'exist', 'skipOnError' => true,
                'targetClass' => Item::className(),
                'targetAttribute' => ['r1' => 'id']
            ],
            [['r2'], 'exist', 'skipOnError' => true,
                'targetClass' => Item::className(),
                'targetAttribute' => ['r2' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'r1' => 'R1',
            'r2' => 'R2',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getR10()
    {
        return $this->hasOne(Item::className(), ['id' => 'r1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getR20()
    {
        return $this->hasOne(Item::className(), ['id' => 'r2']);
    }

    /**
     * Delete all relations with given Item
     * @param $itemId
     */
    public static function purgeItem($itemId) {
        Relation::deleteAll(
            'r1 = :r1',
            [':r1' => $itemId]
        );
    }

}
