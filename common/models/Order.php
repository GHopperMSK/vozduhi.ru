<?php

namespace common\models;

use Yii;
use common\models\OrderItems;


/**
 * This is the model class for table "{{%orders}}".
 *
 * @property int $id
 * @property string $name
 * @property string $tel
 * @property string $address
 * @property int $status_id
 *
 * @property Cart[] $carts
 * @property OrderStatus $status
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_IN_PROCESS = 2;
    const STATUS_SENT = 3;
    const STATUS_RECEIVED = 4;
    const STATUS_CANCELLED = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address'], 'string'],
            [['status_id'], 'required'],
            [['status_id'], 'default', 'value' => null],
            [['status_id'], 'integer'],
            [['name', 'tel'], 'string', 'max' => 255],
            // TODO: 'exist', 'skipOnError' => true, 'targetClass' => OrderStatus::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['status_id'], 'default', 'value' => self::STATUS_NEW],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'tel' => 'Телефон',
            'address' => 'Адрес',
            'status_id' => 'Status ID',
        ];
    }

    public function init()
    {
        $this->status_id = self::STATUS_NEW;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCart()
    {
        return $this->hasMany(OrderItems::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['id' => 'status_id']);
    }
}
