<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use yii\imagine\Image as Imagine;
use yii\helpers\FileHelper;
use \yii\helpers\Url;

/**
 * This is the model class for table "{{%images}}".
 *
 * @property int $id
 * @property int $item_id
 * @property string $name
 * @property string $alt
 * @property int $pos
 *
 * @property Items $item
 */
class ItemImage extends \yii\db\ActiveRecord
{
    /**
     * @var string
     */
    const IMAGES_PATH = '/items';
    const IMAGE_UPLOAD_MIN_WIDTH = 300;
    const IMAGE_UPLOAD_MIN_HEIGHT = 300;
    const IMAGE_UPLOAD_CHOP_WIDTH = 600;
    const IMAGE_UPLOAD_CHOP_HEIGHT = 600;
    const IMAGE_WITDH = 234;
    const IMAGE_HEIGHT = 234;

    /**
     * @var UploadedFile
     */
    public $uploadedFile;

    /**
     * Image's names, obtained from the web form
     * @var string
     */
    public $uploadedFileName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%images}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'pos'], 'default', 'value' => null],
            [['item_id', 'pos'], 'integer'],
            [['name'], 'required'],
            [['name', 'alt', 'uploadedFileName'], 'string', 'max' => 255],
            [['item_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Item::className(),
                'targetAttribute' => ['item_id' => 'id']
            ],
            [['uploadedFile'], 'safe'],
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
            'name' => 'Name',
            'alt' => 'Alt',
            'pos' => 'Pos',
        ];
    }

    public function getUrl($options)
    {
        $name = $this->getName();
        if (empty($this->item_id) || empty($name)) {
            return false;
        }

        $pathinfo = pathinfo($name);
        $nameWithParams = "{$pathinfo['filename']}_{$options['width']}x{$options['height']}.{$pathinfo['extension']}";
        return Yii::$app->params['domainFrontend'] . Yii::$app->params['imagesPath']
            . self::IMAGES_PATH . "/{$this->item_id}/{$nameWithParams}";
    }

    /**
     * Image file absulute path
     * @return bool|string
     */
    public function getPath() {
        $name = $this->getName();
        if (empty($this->item_id) || empty($name)) {
            return false;
        }
        return Yii::getAlias('@originImages') . self::IMAGES_PATH . "/{$this->item_id}/{$name}";
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }

    /**
     * Returns file name
     * @return bool|string
     */
    public function getName() {
        if (empty($this->name)) {
            if (empty($this->uploadedFile)) {
                return false;
            }

            if (!empty($this->uploadedFileName)) {
                $this->name = strtolower($this->uploadedFileName);
            } elseif (!empty($this->uploadedFile)) {
                $name = "{$this->uploadedFile->baseName}.{$this->uploadedFile->extension}";
                $this->name = strtolower($name);
            }
        }

        return $this->name;
    }

    public function getSize() {
        $file = $this->getPath();

        if (is_file($file)) {
            return filesize($file);
        }

        return false;
    }

    public function getType() {
        $file = $this->getPath();

        if (is_file($file)) {
            return image_type_to_mime_type(exif_imagetype($file));
        }

        return false;
    }

    public function getDimension() {
        $file = $this->getPath();

        if (is_file($file)) {
            list($width, $height, , ) = getimagesize($file);
            return [$width, $height];
        }

        return [false, false];
    }

    public function saveImage($validate = false)
    {
        $imageFullPath = $this->getPath();
        $directory = pathinfo($imageFullPath)['dirname'];
        if (!file_exists($directory)) {
            FileHelper::createDirectory($directory);
        };

        // TODO: check if file already exists
        Imagine::resize(
                $this->uploadedFile->tempName,
                self::IMAGE_UPLOAD_CHOP_WIDTH,
                self::IMAGE_UPLOAD_CHOP_HEIGHT)
            ->save($imageFullPath, ['quality' => 80]);

        $this->save($validate);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $file = $this->getPath();

        if (is_file($file)) {
            FileHelper::unlink($file);
        }
    }

    public static function purgeItem($itemId) {
        ItemImage::deleteAll(['item_id' => $itemId]);
        FileHelper::removeDirectory(Yii::getAlias('@originImages') . self::IMAGES_PATH . "/{$itemId}/");
        FileHelper::removeDirectory(Yii::getAlias('@cacheImages') . self::IMAGES_PATH . "/{$itemId}/");
    }
}
