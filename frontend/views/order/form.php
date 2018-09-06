<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $sessionCart common\models\SessionCart */
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($order, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($order, 'tel')->textInput(['maxlength' => true]) ?>

<?= $form->field($order, 'address')->textarea(['rows' => 6]) ?>

<table border="1">
    <thead>
        <td>№</td>
        <td>Изображение</td>
        <td>Название</td>
        <td>Цена</td>
        <td>Количество</td>
        <td>Функции</td>
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
        <td><?= $pos + 1 ?></td>
        <td><?= Html::img($item['image']) ?></td>
        <td><?= $item['name'] ?></td>
        <td><?= $item['price'] ?></td>
        <td><?= $item['count'] ?></td>
        <td><?= Html::a('<span class="glyphicon glyphicon-remove"></span>', [
                'cart/remove',
                'item_id' => $itemId
            ]) ?>
        </td>
    </tr>
    <?php $pos++; ?>
    <?php endforeach; ?>
    <tr><td colspan="6">Итого: <?= $sessionCart->totalSum ?> р.</td></tr>
    </tbody>
</table>
<?= Html::submitButton('Оформить', ['class' => 'btn btn-success']) ?>
<?= Html::a('Удалить все', ['cart/purge', []], ['class' => 'btn btn-danger']) ?>

<?php ActiveForm::end(); ?>
