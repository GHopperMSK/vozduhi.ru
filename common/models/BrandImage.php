<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;

/**
 * This is the model class for Brand Logo".
 *
 */
class BrandImage extends Model
{
    /**
     * @var string
     */
    const IMAGES_PATH = '/brands';

    const LOGO_WIDTH = 300;
    const LOGO_HEIGHT = 300;

    /**
     * Logo file name
     * @var string
     */
    public $name;

    /**
     * Previous logo file name
     * @var string
     */
    public $oldName;

    /**
     * @var string
     */
    public $uploadPath;

    public function init()
    {
        $this->uploadPath = Yii::getAlias('@originImages') . self::IMAGES_PATH;
    }

    /**
     * Returns absolute path to logo file
     * or false if it doesn't exist
     *
     * @return bool|string
     */
    public function getPath()
    {
        if (empty($this->name)) {
            return false;
        }

        $file = $this->uploadPath . '/' . $this->name;
        if (!file_exists($file)) {
            return false;
        }

        return $file;
    }

    /**
     * @return string|false
     */
    public function getUrl($options)
    {
        if (empty($this->name)) {
            $pathinfo = pathinfo('noimage.png');
            $path = '';
        } else {
            $pathinfo = pathinfo($this->name);
            $path = self::IMAGES_PATH;
        }

        $nameWithParams = "{$pathinfo['filename']}_{$options['width']}x{$options['height']}.{$pathinfo['extension']}";
        return Yii::$app->params['domainFrontend'] . Yii::$app->params['imagesPath']
            . $path . '/' . $nameWithParams;
    }

    /**
     * Delete old logo file and all cache data
     */
    public function deleteOld()
    {
        if ($this->oldName && is_file("{$this->uploadPath}/{$this->oldName}")) {
            FileHelper::unlink("{$this->uploadPath}/{$this->oldName}");

            $cachePath = Yii::getAlias('@originImages') . self::IMAGES_PATH;
            $pathinfo = pathinfo($this->oldName);
            $globMask = "{$cachePath}/{$pathinfo['filename']}_***x***.{$pathinfo['extension']}";
            array_map('unlink', glob($globMask));

            $this->oldName = null;
        }
    }

    /**
     * Purges current logo and all cache data
     */
    public function purge()
    {
        if (empty($this->name)) {
            return;
        }

        if (is_file($this->getPath())) {
            unlink($this->getPath());

            $cachePath = Yii::getAlias('@cacheImages') . self::IMAGES_PATH;
            $pathinfo = pathinfo($this->name);
            $globMask = "{$cachePath}/{$pathinfo['filename']}_***x***.{$pathinfo['extension']}";
            array_map('unlink', glob($globMask));
        }
    }
}
