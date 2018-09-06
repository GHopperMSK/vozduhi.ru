<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%item_slug}}".
 *
 * @property int $id
 * @property int $item_id
 * @property string $slug
 * @property string $created_at
 *
 * @property Items $item
 */
class ItemSlug extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%item_slug}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'slug'], 'required'],
            [['item_id'], 'default', 'value' => null],
            [['item_id'], 'integer'],
            [['created_at'], 'safe'],
            [['slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['item_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Item::className(),
                'targetAttribute' => ['item_id' => 'id']
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
            'slug' => 'Slug',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }

    public function isNew()
    {
        return self::findOne(['slug' => $this->slug]) ? false : true;
    }

    /**
     * Delete all slugs of the Item
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

    /**
     * Returns ItemSlug with last correct slug or false if slug not found
     * @param string $slug
     * @return ItemSlug/boolean
     */
    static public function findSlug($slug)
    {
        $itemSlug = self::find()
            ->where([
                'item_id' => self::find()->where(['slug' => $slug])->select('item_id')->limit(1)
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if ($itemSlug) {
            return $itemSlug;
        } else {
            return false;
        }
    }
}
