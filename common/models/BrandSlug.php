<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%brand_slug}}".
 *
 * @property int $id
 * @property int $brand_id
 * @property string $slug
 * @property string $created_at
 *
 * @property Brands $brand
 */
class BrandSlug extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%brand_slug}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['brand_id', 'slug'], 'required'],
            [['brand_id'], 'default', 'value' => null],
            [['brand_id'], 'integer'],
            [['created_at'], 'safe'],
            [['slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['brand_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Brand::className(),
                'targetAttribute' => ['brand_id' => 'id']
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
            'brand_id' => 'Brand ID',
            'slug' => 'Slug',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }

    public function isNew()
    {
        return self::findOne(['slug' => $this->slug]) ? false : true;
    }

    /**
     * Delete all slugs of the Brand
     *
     * @param int $brandId
     */
    static public function purgeItem($brandId)
    {
        self::deleteAll(
            'brand_id = :brand_id',
            [':brand_id' => $brandId]
        );
    }

    /**
     * Returns BrandSlug with last correct slug or false if slug not found
     * @param string $slug
     * @return BrandSlug/boolean
     */
    static public function findSlug($slug)
    {
        $brandSlug = self::find()
            ->where([
                'brand_id' => self::find()->where(['slug' => $slug])->select('brand_id')->limit(1)
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if ($brandSlug) {
            return $brandSlug;
        } else {
            return false;
        }
    }
}
