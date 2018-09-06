<?php
/**
 * Item gift list
 */

use yii\helpers\Html;
use common\models\PlaceholderImage;

/* @var $this yii\web\View */
/* @var $gift common\models\Gift */
?>

<div class="item-gift">
<?php
    $itemGift = $gift->itemGift;
    $images = $itemGift->images;
    if (count($images) > 0) {
        $imageUrl = array_shift($images)->getUrl(['width' => 70, 'height' => 70]);
    } else {
        $imageUrl = PlaceholderImage::getUrl(['width' => 70, 'height' => 70]);
    }
?>

<?= Html::img($imageUrl) ?>
<?= Html::a($itemGift->name, $itemGift->getUrl(), []) ?><br />
</div>