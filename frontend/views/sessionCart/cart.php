<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $sessionCart common\models\SessionCart */
?>
<?php if ($cart->totalCount) : ?>
    <table border="0">
        <?php foreach ($cart->items as $itemId => $item) : ?>
            <tr>
                <td width="70px"><?= Html::img($item['image']) ?></td>
                <td><?= $item['name'] ?></td>
                <td width="70px">
                    <?= Html::tag('span', Yii::$app->formatter->asCurrency(
                        $item['price'],
                        null,
                        [\NumberFormatter::MAX_SIGNIFICANT_DIGITS => 100]
                    ))?>
                </td>
                <td width="40px"><span><?= $item['count'] ?> шт.</span></td>
                <td width="20px"><?= Html::a('<span class="glyphicon glyphicon-remove"></span>',
                        ['cart/remove', 'item_id' => $itemId]) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2" align="right">Итого:</td>
            <td colspan="3"><?= Yii::$app->formatter->asCurrency(
                    $cart->totalSum,
                    null,
                    [\NumberFormatter::MAX_SIGNIFICANT_DIGITS => 100]
                ) ?>
            </td>
        </tr>
    </table>
    <p>
    <?= Html::a("Оформить заказ", ['order/create']) ?>&nbsp;
    <?= Html::a('Отчистить корзину', ['cart/purge', []]) ?>
    </p>
<?php else : ?>
    <p class="empty_cart_notice">Нет товаров в корзине.</p>
<?php endif; ?>
