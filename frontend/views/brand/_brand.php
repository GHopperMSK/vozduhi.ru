<?php
/**
 * Single item template for Brand list
 */

use yii\helpers\Html;
use \yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $brand common\models\Brand */

$imgSize = 300;
?>

<?= Html::beginTag('a', ['href' => $brand->getUrl()]) ?>
<?= Html::img($brand->image->getUrl(['width' => $imgSize, 'height' => $imgSize]), []) ?>
<h4>Подробнее о <?= $brand->name ?></h4>
<?= Html::endTag('a') ?>
