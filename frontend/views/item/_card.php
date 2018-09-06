<?php
/**
 * Single Item template for Item lists
 */

use yii\helpers\Html;
use common\models\PlaceholderImage;
use common\models\ItemImage;

/* @var $this yii\web\View */
/* @var $item common\models\Item */
?>

<?php
$images = $item->images;
?>
<div class="item-card">

<?php if ($item->gifts) : ?>
    <div class='item-gift-background'><i class="glyphicon glyphicon-certificate"></i></div>
    <div class='item-gift-logo'><i class="glyphicon glyphicon-gift"></i></div>
<?php endif; ?>

<?= Html::beginTag('a', ['href' => $item->url,]) ?>
    <?php if (count($images) === 0) : ?>
        <div class="card_img_wrapper_blink">
        <?= Html::img(PlaceholderImage::getUrl(
            ['width' => ItemImage::IMAGE_WITDH, 'height' => ItemImage::IMAGE_HEIGHT]),
            ['alt' => 'нет изображения'])
        ?>
    <?php elseif (count($images) === 1) : ?>
        <div class="card_img_wrapper_blink">
        <?= Html::img($images[0]->getUrl(
            ['width' => ItemImage::IMAGE_WITDH, 'height' => ItemImage::IMAGE_HEIGHT]),
            ['alt' => $images[0]->alt])
        ?>
    <?php else : ?>
        <div class="card_img_wrapper_switch">
        <?php for ($i = 0; $i < 2; $i++) : ?>
            <?= Html::img($images[$i]->getUrl(
                ['width' => ItemImage::IMAGE_WITDH, 'height' => ItemImage::IMAGE_HEIGHT]),
                [
                    'alt' => $images[$i]->alt,
                ])
            ?>
        <?php endfor; ?>
    <?php endif; ?>
    </div>
    <?= Html::tag('p', $item->name, ['class' => 'item-cart-name'] )?>
<?= Html::endTag('a') ?>
<?php if ($item->discount->price) : ?>
    <?= Html::tag('p', Html::tag('s', $item->price) . ' '
        . Yii::$app->formatter->asCurrency(
            $item->discount->price,
            null,
            [\NumberFormatter::MAX_SIGNIFICANT_DIGITS => 100]
        )
    ); ?>
<?php else: ?>
    <?= Html::tag('p', Yii::$app->formatter->asCurrency(
        $item->price,
        null,
        [\NumberFormatter::MAX_SIGNIFICANT_DIGITS => 100])
    ) ?>
<?php endif; ?>
</div>