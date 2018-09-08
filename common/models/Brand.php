<?php

namespace common\models;

use Yii;
use yii\base\UserException;
use common\models\BrandImage;
use common\models\BrandSlug;
use \yii\helpers\Url;

/**
 * This is the model class for table "brands".
 *
 * @property int $id
 * @property string $name
 * @property string $logo
 * @property string $description
 */
class Brand extends \yii\db\ActiveRecord
{
    /**
     * Obtained file
     *
     * @var UploadedFile
     */
    public $uploadedFile;

    /**
     * Obtained file name
     *
     * @var string
     */
    public $uploadedFileName;

    /**
     * @var BrandImage
     */
    public $image;

    public function init()
    {
        $this->image = new BrandImage();
    }

    public function afterFind()
    {
        $this->image->name = $this->logo;
        $this->image->oldName = $this->getOldAttribute('logo');

        return parent::afterFind();
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'brands';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'uploadedFileName'], 'string'],
            [['name'], 'unique'],
            [['name'], 'required'],
            [['uploadedFile'], 'image',
                'minWidth' => BrandImage::LOGO_WIDTH,
                'minHeight' => BrandImage::LOGO_HEIGHT,
                'extensions' => 'png, jpg',
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
            'name' => 'Brand name',
            'logo' => 'File name',
            'description' => 'Description',
        ];
    }

    public function getRoute()
    {
        return ['brand/view', 'slug' => $this->slug->slug];
    }

    public function getUrl()
    {
        return Url::to($this->getRoute());
    }

    /**
     * @return common\models\BrandSlug
     */
    public function getSlug()
    {
        if ($this->id) {
            $slug = BrandSlug::find()
                ->where(['brand_id' => $this->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            if (!$slug) {
                $slug = new BrandSlug();
                $slug->brand_id = $this->id;
            }
        } else {
            $slug = new BrandSlug();
        }

        return $slug;
    }

    public function beforeDelete()
    {
        $this->image->purge();

        foreach($this->items as $item) {
            $item->delete();
        }

        return parent::beforeDelete();
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!isset($this->uploadedFileName)) {
            // without logo
            if ($this->image->name) {
                $this->image->purge();
                $this->logo = null;
            }
        } elseif ($this->uploadedFile) {
            $logoName = empty($this->uploadedFileName)
                ? $this->uploadedFile->name
                : $this->uploadedFileName;
            $logoFullPath = "{$this->image->uploadPath}/{$logoName}";

            if (file_exists($logoFullPath)) {
                // the filename already exists
                $this->addError('uploadedFile', "The logo filename '{$logoFullPath}' already exists");
                throw new UserException(
                    "The logo filename '{$logoFullPath}' already exists! Choose another name."
                );
                return false;
            }

            Image::resize($this->uploadedFile->tempName, BrandImage::LOGO_WIDTH, BrandImage::LOGO_HEIGHT)
                ->save($logoFullPath, ['quality' => 80]);
            $this->logo = $logoName;

            $this->image->deleteOld();
        }

        return true;
    }

    public function getItems()
    {
        return $this->hasMany(Item::className(), ['brand_id' => 'id'])->orderBy(['modified_at' => SORT_DESC]);
    }

    public function getCategoryIds()
    {
        return Item::find()
            ->select('category_id')
            ->distinct()
            ->where(['=', 'brand_id', $this->id])
            ->column();
    }
}
