<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%category_slug}}".
 *
 * @property int $id
 * @property int $category_id
 * @property string $slug
 * @property string $created_at
 *
 * @property Categories $category
 */
class CategorySlug extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%category_slug}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'slug'], 'required'],
            [['category_id'], 'default', 'value' => null],
            [['category_id'], 'integer'],
            [['created_at'], 'safe'],
            [['slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['category_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Category::className(),
                'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'slug' => 'Slug',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function isNew()
    {
        return self::findOne(['slug' => $this->slug]) ? false : true;
    }

    /**
     * Delete all slugs of the Category
     *
     * @param int $categiryId
     */
    static public function purgeItem($categoryId)
    {
        self::deleteAll(
            'category_id = :category_id',
            [':category_id' => $categoryId]
        );
    }

    /**
     * Returns CategorySlug with last correct slug or false if slug not found
     * @param string $slug
     * @return CategorySlug/boolean
     */
    static public function findSlug($slug)
    {
        $categorySlug = self::find()
            ->where([
                'category_id' => self::find()->where(['slug' => $slug])->select('category_id')->limit(1)
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if ($categorySlug) {
            return $categorySlug;
        } else {
            return false;
        }
    }

}
