<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for Placeholder image".
 *
 */
class PlaceholderImage extends Model
{
    /**
     * @var string
     */
    public static function getUrl($options)
    {
        $nameWithParams = "noimage_{$options['width']}x{$options['height']}.png";
        return Yii::$app->params['domainFrontend'] . Yii::$app->params['imagesPath'] . '/' . $nameWithParams;
    }
}
