<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Category;
use yii\grid\GridView;
use common\models\DataType;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile("@web/js/transliterate.js");
?>

<div class="menu-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
        echo $form->field($model, 'formParent')
            ->dropDownList(
                ArrayHelper::map(
                    Category::find()->orderBy(['lft' => SORT_ASC])->all(),
                    'id',
                    'title'
                ),
                ['prompt'=>'Select parent category']
            )->label('Parent category');
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php
        echo $form->field($model->slug, 'slug', [
            'template' => '{label}<div class="input-group">
                {input}
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" onclick="setSlug()">Auto</button>
                </span></div>',
        ]);
    ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <h2>Attributes</h2>

    <?= Html::beginTag('div', ['id' => 'attributes']) ?>
    <?php foreach($model->categoryAttributes as $attribute): ?>
        <?= Html::beginTag('p') ?>
        <?= Html::activeHiddenInput($attribute, '[]id', [
            'value' => $attribute->id,
            'label' => ''
        ]) ?>
        <?= $attribute->name ?>
        <?= Html::a(
            '<span class="glyphicon glyphicon-remove"></span>',
            null,
            [
                'title' => 'Remove',
                'data-pjax' => '0',
                'onclick' => "$(this).closest('p').remove(); return false;"
            ]
        ) ?>
        <?= Html::endTag('p') ?>
    <?php endforeach; ?>
    <?= Html::endTag('div') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php \yii\widgets\Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $attributesDataProvider,
        'filterModel' => $attributesSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'attribute' => 'dataTypeName',
                'filter' => ArrayHelper::map(
                    DataType::find()->all(),
                    'id',
                    'name'
                ),
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{add}',
                'buttons' => [
                    'add' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-plus"></span>',
                            '',
                            [
                                'title' => 'Add',
                                'data-pjax' => '0',
                                'onclick' => "js:addAttribute({$model->id}, '{$model->name}'); return false;"
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>

<?php
$this->registerJs(<<<EOT
    function addAttribute(id, name) {
        var html = '<p>'
            + '<input type="hidden" id="item-formrelations" name="Attribute[][id]" value="' + id + '" label="">'
            + name 
            + ' <a title="Remove" data-pjax="0" onclick="$(this).closest(\'p\').remove(); return false;">'
            + '<span class="glyphicon glyphicon-remove"></span>'
            + '</a>'
            + '</p>';
        $('#attributes').after(html);
    };
    
    function setSlug(e) {
        let name = $('#category-name').val();
        name = transliterate(name);
        const slug = name.toLowerCase()
            .replace(/[^\w ]+/g,'')
            .replace(/ +/g,'-');
        $('#categoryslug-slug').val(slug);
    }

EOT
    , \yii\web\View::POS_HEAD
);