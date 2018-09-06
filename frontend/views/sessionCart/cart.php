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
                <td width="70px"><span><?= $item['price'] ?> руб.</span></td>
                <td width="40px"><span><?= $item['count'] ?> шт.</span></td>
                <td width="20px"><?= Html::a('<span class="glyphicon glyphicon-remove"></span>',
                        ['cart/remove', 'item_id' => $itemId]) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2" align="right">Итого:</td>
            <td colspan="3"><?= $cart->totalSum ?> руб.</td>
        </tr>
    </table>
    <p>
    <?= Html::a("Оформить заказ", ['order/create']) ?>&nbsp;
    <?= Html::a('Отчистить корзину', ['cart/purge', []]) ?>
    </p>
<?php else : ?>
    <p class="empty_cart_notice">Нет товаров в корзине.</p>
<?php endif; ?>
