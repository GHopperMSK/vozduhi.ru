<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\DataType;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Attribute */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attribute-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    echo $form->field($model, 'data_type_id')
        ->dropDownList(
            ArrayHelper::map(
                DataType::find()->all(),
                'id',
                'name'
            ),
            ['prompt'=>'Select type']
        );
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
