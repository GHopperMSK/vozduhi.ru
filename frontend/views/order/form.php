<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $sessionCart common\models\SessionCart */
?>

<h2>Оформление заказа</h2>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($order, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($order, 'tel')->textInput(['maxlength' => true]) ?>

<?= $form->field($order, 'address')->textarea(['rows' => 6]) ?>

<table class="table order_total">
    <thead>
        <td>№</td>
        <td>Изображение</td>
        <td>Название</td>
        <td>Цена</td>
        <td>Количество</td>
        <td></td>
    </thead>
    <tbody>
    <?php $pos = 0; ?>
    <?php foreach ($sessionCart->items as $itemId => $item) : ?>
    <tr>
        <?= Html::tag('input', '', [
            'type' => 'hidden',
            'name' => "Cart[{$pos}][item_id]",
            'value' => $itemId,
        ]) ?>
        <?= Html::tag('input', '', [
            'type' => 'hidden',
            'name' => "Cart[{$pos}][count]",
            'value' => $item['count'],
        ]) ?>
        <td class="text-center"><?= $pos + 1 ?></td>
        <td class="text-center"><?= Html::img($item['image']) ?></td>
        <td><?= $item['name'] ?></td>
        <td class="text-center">
            <?= Html::tag('span', Yii::$app->formatter->asCurrency(
                $item['price'],
                null,
                [\NumberFormatter::MAX_SIGNIFICANT_DIGITS => 100]
            ))?>
        </td>
        <td class="text-center"><?= $item['count'] ?></td>
        <td class="text-center">
            <?= Html::a('Удалить', Url::to(['cart/remove', 'item_id' => $itemId]),
                ['class' => 'btn btn-warning pull-right']) ?>
        </td>
    </tr>
    <?php $pos++; ?>
    <?php endforeach; ?>
    <tr><td class="text-right " colspan="3">
        <strong>Итого:</strong>
        </td>
        <td class="text-center"><strong>
        <?= Html::tag('span',
            Yii::$app->formatter->asCurrency(
                $sessionCart->totalSum,
                null,
                [\NumberFormatter::MAX_SIGNIFICANT_DIGITS => 100]
            ))?>
        </strong></td>
        <td class="text-center">
            <strong><?= $sessionCart->totalCount ?></strong>
        </td>
        <td class="text-center">
        </td>
    </tr>
    </tbody>
</table>
<?= Html::a('Удалить все', ['cart/purge', []], ['class' => 'btn btn-warning pull-right']) ?>
<?= Html::submitButton('Оформить заказ', ['class' => 'btn btn-primary']) ?>



<?php ActiveForm::end(); ?>
