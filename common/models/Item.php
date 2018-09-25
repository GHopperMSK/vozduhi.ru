<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\helpers\Url;
use common\models\ItemSlug;
use common\models\Discount;
use common\models\Gift;

/**
 * This is the model class for table "items".
 *
 * @property int $id
 * @property int $brand_id
 * @property int $category_id
 * @property string $name
 * @property string $description
 * @property int $price
 * @property timestamp $modified_at
 * @property bool $active
 *
 * @property Brand $brand
 * @property Categoryd $category
 */
class Item extends \yii\db\ActiveRecord
{
    const IMAGES_PATH = '/items';

    private $_slug;

    /**
     * Files, obtained from the web form
     * @var UploadedFile[]
     */
    public $uploadedFiles;

    /**
     * Obtained file names
     *
     * @var string[]
     */
    public $uploadedFilesName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%items}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'modified_at',
                'updatedAtAttribute' => 'modified_at',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['brand_id', 'category_id'], 'default', 'value' => null],
            [['brand_id', 'category_id', 'price'], 'integer'],
            [['active'], 'boolean'],
            [['name', 'brand_id', 'category_id'], 'required'],
            [['description'], 'string'],
            [['uploadedFilesName'], 'each', 'rule' => ['string', 'max' => 255]],
            [['name'], 'string', 'max' => 255],
            [['brand_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Brand::className(),
                'targetAttribute' => ['brand_id' => 'id']
            ],
            [['category_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Category::className(),
                'targetAttribute' => ['category_id' => 'id']
            ],
            [['uploadedFiles'], 'image',
                'minWidth' => ItemImage::IMAGE_UPLOAD_MIN_WIDTH,
                'minHeight' => ItemImage::IMAGE_UPLOAD_MIN_HEIGHT,
                'extensions' => 'png, jpg, jpeg',
                'maxFiles' => 6,
                'maxSize' => 1024 * 1024 * 2
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
            'category_id' => 'Category',
            'brandName' => 'Brand',
            'categoryTitle' => 'Category',
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
            'modified_at' => 'Modified at',
            'active' => 'Is active',
        ];
    }

    public function getRoute()
    {
        return ['item/view', 'slug' => $this->slug->slug];
    }

    public function getUrl()
    {
        return Url::to($this->getRoute());
    }

    public function init()
    {
        if (!is_array($this->uploadedFilesName)) {
            $this->uploadedFilesName = array($this->uploadedFilesName);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return common\models\ItemSlug
     */
    public function getSlug()
    {
        if (!isset($this->_slug)) {
            if ($this->id) {
                $slug = ItemSlug::find()
                    ->where(['item_id' => $this->id])
                    ->orderBy(['created_at' => SORT_DESC])
                    ->one();

                if (!$slug) {
                    $slug = new ItemSlug();
                    $slug->item_id = $this->id;
                }
            } else {
                $slug = new ItemSlug();
            }

            $this->_slug = $slug;
        }

        return $this->_slug;
    }

    /**
     * @return common\models\Discount
     */
    public function getDiscount()
    {
        if ($this->id) {
            $discount = Discount::find()
                ->where(['item_id' => $this->id])
                ->one();

            if (!$discount) {
                $discount = new Discount();
                $discount->item_id = $this->id;
            }
        } else {
            $discount = new Discount();
        }

        return $discount;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(ItemImage::className(), ['item_id' => 'id'])->orderBy(['pos' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGifts()
    {
        return $this->hasMany(Gift::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelations()
    {
        return $this->hasMany(Relation::className(), ['r1' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'r2'])
            ->viaTable('{{%relations}}', ['r1' => 'id'])->orderBy(['modified_at' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecommendedItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'recommended_item_id'])
            ->viaTable('{{%recommended}}', ['item_id' => 'id']);
        //->orderBy(['modified_at' => SORT_DESC]);
    }

    public function getRecommended()
    {
        return $this->hasMany(Recommended::className(), ['item_id' => 'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        Discount::purgeItem($this->id);
        Gift::purgeItem($this->id);
        ItemSlug::purgeItem($this->id);
        Value::purgeItem($this->id);
        ItemImage::purgeItem($this->id);
    }
}
