<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\OrderStatus;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/* @var $itemsDataProvider */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tel')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

    <?php
    echo $form->field($model, 'status_id')
        ->dropDownList(
            ArrayHelper::map(
                OrderStatus::find()->all(),
                'id',
                'name'
            ),
            ['prompt'=>'Select status']
        );
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <h2>Items</h2>
    <?= GridView::widget([
        'dataProvider' => $itemsDataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'item.name',
            ],

        ],
    ]); ?>

</div>
