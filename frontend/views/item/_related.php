<?php
/**
 * Item gift list
 */

use yii\helpers\Html;
use common\models\PlaceholderImage;

/* @var $this yii\web\View */
/* @var $item common\models\Item */

$imgSize = 70;
?>

<?php
    $images = $item->images;
    if (count($images) > 0) {
        $imageUrl = array_shift($images)->getUrl(['width' => $imgSize, 'height' => $imgSize]);
    } else {
        $imageUrl = PlaceholderImage::getUrl(['width' => $imgSize, 'height' => $imgSize]);
    }
?>
<tr>
    <td><?= Html::img($imageUrl) ?></td>
    <td><?= Html::a($item->name, $item->getUrl(), []) ?></td>
    <td><?= Yii::$app->formatter->asCurrency(
            $item->price,
            null,
            [\NumberFormatter::MAX_SIGNIFICANT_DIGITS => 100]
        ) ?></td>
</tr>