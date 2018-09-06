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

    function transliterate(text) {
    
        text = text
            .replace(/\u0401/g, 'YO')
            .replace(/\u0419/g, 'I')
            .replace(/\u0426/g, 'TS')
            .replace(/\u0423/g, 'U')
            .replace(/\u041A/g, 'K')
            .replace(/\u0415/g, 'E')
            .replace(/\u041D/g, 'N')
            .replace(/\u0413/g, 'G')
            .replace(/\u0428/g, 'SH')
            .replace(/\u0429/g, 'SCH')
            .replace(/\u0417/g, 'Z')
            .replace(/\u0425/g, 'H')
            .replace(/\u042A/g, '')
            .replace(/\u0451/g, 'yo')
            .replace(/\u0439/g, 'i')
            .replace(/\u0446/g, 'ts')
            .replace(/\u0443/g, 'u')
            .replace(/\u043A/g, 'k')
            .replace(/\u0435/g, 'e')
            .replace(/\u043D/g, 'n')
            .replace(/\u0433/g, 'g')
            .replace(/\u0448/g, 'sh')
            .replace(/\u0449/g, 'sch')
            .replace(/\u0437/g, 'z')
            .replace(/\u0445/g, 'h')
            .replace(/\u044A/g, "'")
            .replace(/\u0424/g, 'F')
            .replace(/\u042B/g, 'I')
            .replace(/\u0412/g, 'V')
            .replace(/\u0410/g, 'a')
            .replace(/\u041F/g, 'P')
            .replace(/\u0420/g, 'R')
            .replace(/\u041E/g, 'O')
            .replace(/\u041B/g, 'L')
            .replace(/\u0414/g, 'D')
            .replace(/\u0416/g, 'ZH')
            .replace(/\u042D/g, 'E')
            .replace(/\u0444/g, 'f')
            .replace(/\u044B/g, 'i')
            .replace(/\u0432/g, 'v')
            .replace(/\u0430/g, 'a')
            .replace(/\u043F/g, 'p')
            .replace(/\u0440/g, 'r')
            .replace(/\u043E/g, 'o')
            .replace(/\u043B/g, 'l')
            .replace(/\u0434/g, 'd')
            .replace(/\u0436/g, 'zh')
            .replace(/\u044D/g, 'e')
            .replace(/\u042F/g, 'Ya')
            .replace(/\u0427/g, 'CH')
            .replace(/\u0421/g, 'S')
            .replace(/\u041C/g, 'M')
            .replace(/\u0418/g, 'I')
            .replace(/\u0422/g, 'T')
            .replace(/\u042C/g, "'")
            .replace(/\u0411/g, 'B')
            .replace(/\u042E/g, 'YU')
            .replace(/\u044F/g, 'ya')
            .replace(/\u0447/g, 'ch')
            .replace(/\u0441/g, 's')
            .replace(/\u043C/g, 'm')
            .replace(/\u0438/g, 'i')
            .replace(/\u0442/g, 't')
            .replace(/\u044C/g, "'")
            .replace(/\u0431/g, 'b')
            .replace(/\u044E/g, 'yu');
    
        return text;
    };
EOT
    , \yii\web\View::POS_HEAD
);