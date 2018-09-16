<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Brand;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use ghopper\fileinput\FileInputWidget;

/* @var $this yii\web\View */
/* @var $item common\models\Item */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile("@web/js/transliterate.js");

$imageInitData = [];

// prepare initial data to be shown
$images = $item->images;
if (is_array($images) && count($images)) {
    foreach ($images as $image) {
        $imageInitData[] = [
            'name' => $image->name,
            'url' => $image->getUrl(['width' => 200, 'height' => 200]),
            'size' => $image->getSize(),
            'type' => $image->getType(),
            'dimension' => implode('x', $image->getDimension()),
        ];
    }
}

?>

<div class="item-form">

    <?php $form = ActiveForm::begin(['options' => [
            'enctype' => 'multipart/form-data',
            'id' => 'item-form'
        ]
    ]); ?>

    <?= $form->errorSummary($item) ?>

    <?php
    echo $form->field($item, 'brand_id')
        ->dropDownList(
            ArrayHelper::map(
                Brand::find()->all(),
                'id',
                'name'
            ),
            ['prompt'=>'Select brand']
        );
    ?>

    <?php
    echo $form->field($item, 'category_id')
        ->dropDownList(
            ArrayHelper::map(
                $availableCategories,
                'id',
                'title'
            ),
            ['prompt'=>'Select category']
        );
    ?>

    <?= $form->field($item, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($item->slug, 'slug', [
        'template' => '{label}<div class="input-group">
                {input}
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" onclick="setSlug()">Auto</button>
                </span></div>',
    ]) ?>

    <?= $form->field($item, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($item, 'price')->textInput(['type' => 'number']) ?>

    <?= $form->field($item->discount, 'price')->textInput(['type' => 'number']) ?>

    <?= $form->field($item, 'active')->checkbox() ?>

    <h2>Images</h2>
    <?= $form->field($item, 'uploadedFiles')->widget(FileInputWidget::className(), [
        'multi' => true,
        'initData' => $imageInitData,
    ]) ?>

    <h2>Attributes</h2>
    <?php foreach ($availableCategories as $category): ?>
        <?= GridView::widget([
            'id' => 'category_' . $category->id,
            'options' => [
                'class' => (!$item->isNewRecord && ($item->category_id == $category->id)) ? '' : 'hide',
            ],
            'dataProvider' => new ActiveDataProvider([ 'query' => $category->getCategoryTreeAttributes()]),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'name',
                    'label' => 'Attribute name',
                ],
                [
                    'label' => 'Attribute value',
                    'format' => 'raw',
                    'value' => function($attr) use ($item, $form) {
                        $tag = '';
                        switch($attr->dataType->name) {
                            case 'integer':
                                $tag = $form->field($attr, "[{$attr->id}]uploadedValue")
                                    ->textInput([
                                        'type' => 'number',
                                        'value'=> $attr->getValueByItem($item)
                                    ])
                                    ->label(false);
                                break;
                            case 'string':
                                $tag = $form->field($attr, "[{$attr->id}]uploadedValue")
                                    ->textInput(['value' => $attr->getValueByItem($item)])
                                    ->label(false);
                                break;
                            case 'list':
                                $tag = $form->field($attr, "[{$attr->id}]uploadedValue")
                                    ->textarea(['rows' => '6', 'value' => $attr->getValueByItem($item)])
                                    ->label(false);
                                break;
                            default:
                                $tag = 'Unknown value type!';
                        }
                        return $tag;
                    }
                ],

            ],
        ]); ?>
    <?php endforeach; ?>

    <h2>Relations</h2>
    <?= Html::beginTag('div', ['id' => 'relations']) ?>
        <?php foreach($item->relations as $relation): ?>
            <?= Html::beginTag('p') ?>
                <?= Html::tag('input', '', [
                    'type' => 'hidden',
                    'name' => $relation->formName() . '[][r2]',
                    'value' => $relation->r2,
                    'label' => '',
                ]) ?>
                <?= $relation->r20->name ?>
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

    <h2>Recommended</h2>
    <?= Html::beginTag('div', ['id' => 'recommended']) ?>
    <?php foreach($item->recommended as $recommended): ?>
        <?= Html::beginTag('p') ?>
        <?= Html::tag('input', '', [
            'type' => 'hidden',
            'name' => $recommended->formName() . '[][recommended_item_id]',
            'value' => $recommended->recommended_item_id,
            'label' => '',
        ]) ?>
        <?= $recommended->recommendedItem->name ?>
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

    <h2>Gifts</h2>
    <?= Html::beginTag('div', ['id' => 'gifts']) ?>
    <?php foreach($item->gifts as $gift): ?>
        <?= Html::beginTag('p') ?>
        <?= Html::tag('input', '', [
            'type' => 'hidden',
            'name' => $gift->formName() . '[][gift]',
            'value' => $gift->gift,
            'label' => '',
        ]) ?>
        <?= $gift->itemGift->name ?>
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

    <?php ActiveForm::end(); ?>

    <?php \yii\widgets\Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'brandName',
                'value' => 'brand.name'
            ],
            [
                'attribute' => 'categoryTitle',
                'value' => 'category.title'
            ],
            'name',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{add_relation} {add_recommended} {add_gift}',
                'buttons' => [
                    'add_relation' => function ($url, $item) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-retweet"></span>',
                            '',
                            [
                                'title' => 'Add relation',
                                'data-pjax' => '0',
                                'onclick' => "js:addRelation({$item->id}, '{$item->name}'); return false;"
                            ]
                        );
                    },
                    'add_recommended' => function ($url, $item) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-hand-up"></span>',
                            '',
                            [
                                'title' => 'Add recommended',
                                'data-pjax' => '0',
                                'onclick' => "js:addRecommended({$item->id}, '{$item->name}'); return false;"
                            ]
                        );
                    },
                    'add_gift' => function ($url, $item) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-gift"></span>',
                            '',
                            [
                                'title' => 'Add gift',
                                'data-pjax' => '0',
                                'onclick' => "js:addGift({$item->id}, '{$item->name}'); return false;"
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', [
            'class' => 'btn btn-success',
            'form' => 'item-form'
        ]) ?>
    </div>

</div>

<?php
$this->registerJs(<<<EOT
    function addRelation(id, name) {
        var html = '<p>'
            + '<input type="hidden" name="Relation[][r2]" value="' + id + '" label="">'
            + name 
            + ' <a title="Remove" data-pjax="0" onclick="$(this).closest(\'p\').remove(); return false;">'
            + '<span class="glyphicon glyphicon-remove"></span>'
            + '</a>'
            + '</p>';
        $('#relations').after(html);
    };

    function addRecommended(id, name) {
        var html = '<p>'
            + '<input type="hidden" name="Recommended[][recommended_item_id]" value="' + id + '" label="">'
            + name 
            + ' <a title="Remove" data-pjax="0" onclick="$(this).closest(\'p\').remove(); return false;">'
            + '<span class="glyphicon glyphicon-remove"></span>'
            + '</a>'
            + '</p>';
        $('#recommended').after(html);
    };

    function addGift(id, name) {
        var html = '<p>'
            + '<input type="hidden" name="Gift[][gift]" value="' + id + '" label="">'
            + name 
            + ' <a title="Remove" data-pjax="0" onclick="$(this).closest(\'p\').remove(); return false;">'
            + '<span class="glyphicon glyphicon-remove"></span>'
            + '</a>'
            + '</p>';
        $('#gifts').after(html);
    };

    function showAttributes(categoryId) {
        // clear all values
        $("[name^='Attribute'][name$='[uploadedValue]']").val(null);
        // hide all GridViews
        $("div[id*='category_']").addClass('hide');
        // show the corresponding
        $('#category_' + categoryId).removeClass('hide');
    }

    function setSlug(e) {
        let name = $('#item-name').val();
        name = transliterate(name);
        const slug = name.toLowerCase()
            .replace(/[^\w ]+/g,'')
            .replace(/ +/g,'-');
        $('#itemslug-slug').val(slug);
    }

EOT
    , \yii\web\View::POS_HEAD
);

$this->registerJs(<<<EOT
    $(document).ready(function() {
        $('#item-category_id').change(function() {
            showAttributes(this.value);
        });
        $("#item-form").on("submit", function() {
            // remove all GridViews which don't relate to the category
            $("div[id*='category_'][class='hide']").each(function( index ) {
                this.remove();
            });
         })
    });
EOT
);
?>