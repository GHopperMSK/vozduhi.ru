<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ghopper\fileinput\FileInputWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Brand */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile("@web/js/transliterate.js");

// TODO: set proper stats
$initData = empty($model->logo) ? null : [[
    'name' => $model->logo,
    'url' => $model->image->getUrl(['width' => 200, 'height' => 200]),
    'size' => 0,
    'type' => 'not set',
    'dimension' => 'not set',
]];
?>

<div class="brand-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uploadedFile')->widget(FileInputWidget::className(), [
        'multi' => false,
        'initData' => $initData,
    ]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php
    echo $form->field($model->slug, 'slug', [
        'template' => '{label}<div class="input-group">
                {input}
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" onclick="setSlug()">Auto</button>
                </span></div>',
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs(<<<EOT
    function setSlug(e) {
        let name = $('#brand-name').val();
        name = transliterate(name);
        const slug = name.toLowerCase()
            .replace(/[^\w ]+/g,'')
            .replace(/ +/g,'-');
        $('#brandslug-slug').val(slug);
    }
EOT
    , \yii\web\View::POS_HEAD
);