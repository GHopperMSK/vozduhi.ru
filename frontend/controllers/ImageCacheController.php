<?php
/**
 * Controller for resizing images on the fly with cache.
 *
 * Query https://site_url/image/file_name_200x200.jpg returns file_name.jpg which resized to 200x200. If
 * file_name_200x200.jpg exists, it will be returned as is..
 */
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\imagine\Image as Imagine;
use yii\helpers\FileHelper;

/**
 * ImageCacheController controller
 */
class ImageCacheController extends BaseController
{

    public function init() {
    }


    public function actionCreate($path, $file)
    {
        if (!empty($path)) {
            $path = '/' . trim($path, '/');
        }
        $file = Yii::getAlias('@cacheImages') . "{$path}/{$file}";

        $ext = pathinfo($file)['extension'];

        if (!file_exists($file)) {
            preg_match("/.+\/([^\/]*)_(\d+)x(\d+)\.{$ext}/", $file, $matches);
            if (is_array($matches) && count($matches)) {
                $originFile = Yii::getAlias('@originImages') . "{$path}/{$matches[1]}.{$ext}";
                if (!file_exists($originFile)) {
                    throw new NotFoundHttpException("Image doen't exist!");
                }

                $pathInfo = pathinfo($file);
                if (!is_dir($pathInfo['dirname'])) {
                    FileHelper::createDirectory($pathInfo['dirname']);
                }

                Imagine::resize($originFile, $matches[2], $matches[3])
                    ->save($file, ['quality' => 80]);

                return $this->redirect(Yii::$app->params['imagesPath'] . "/{$path}/{$pathInfo['filename']}.{$ext}");
            } else {
                throw new NotFoundHttpException('Wrong URL params!');
            }
        }
    }

}
