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
use Imagine\Image\Box;
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

                $image = Imagine::resize($originFile, $matches[2], $matches[3]);
                $size = $image->getSize();

                if ($size->getWidth() > 300 && $size->getHeight() > 300) {
                    $wmImage = Imagine::getImagine()
                        ->open(Yii::getAlias('@webroot/images/watermark.png'));
                    $wmSize = $wmImage->getSize();

                    if($size->getWidth() - $wmSize->getWidth() <= 0 || $size->getHeight() - $wmSize->getHeight() <= 0) {
                        $wmImage = $wmImage->thumbnail(new Box($size->getWidth(), $size->getHeight()));
                        $wmSize = $wmImage->getSize();
                    }

                    $image = Imagine::watermark($image, $wmImage, [
                        ($size->getWidth() / 2) - ($wmSize->getWidth() / 2),
                        ($size->getHeight() / 2) - ($wmSize->getHeight() / 2)
                    ]);
                }

                $image->save($file, ['quality' => 80]);

                return $this->redirect(Yii::$app->params['imagesPath'] . "/{$path}/{$pathInfo['filename']}.{$ext}");
            } else {
                throw new NotFoundHttpException('Wrong URL params!');
            }
        }
    }

}
