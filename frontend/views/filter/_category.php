<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\slider\Slider;
use frontend\models\Filter;

/* @var $this yii\web\View */

$priceStart = isset($filter->priceStart) ? $filter->priceStart : Filter::DEFAULT_PRICE_MIN;
$priceEnd = isset($filter->priceEnd) ? $filter->priceEnd : Filter::DEFAULT_PRICE_MAX;

$filterCount = $filter->count ? " (выбрано {$filter->count})" : '';
?>

<?php $form = ActiveForm::begin([
    'action' => Url::current(['Filter' => false]),
    'method' => 'get',
]); ?>

<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" class="collapsed" href="#collapse1">Фильтр для поиска<?= $filterCount ?></a>
            </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse">
            <ul class="list-group">
                <li class="list-group-item">
                    <div>
                        <label class="control-label">Цена</label><br />
                        <b class="badge"><?= $filter->priceMin ?></b>&nbsp;&nbsp;
                    <?=
                    Slider::widget([
                        'name' => $filter->formName() . '[price]',
                        'value' => "{$priceStart},{$priceEnd}",
                        'sliderColor' => Slider::TYPE_GREY,
                        'pluginOptions' => [
                            'min' => $filter->priceMin,
                            'max' => $filter->priceMax,
                            'step' => 10,
                            'range' => true,
                        ]
                    ]);
                    ?>
                    &nbsp;&nbsp;<b class="badge"><?= $filter->priceMax ?></b></div>

                </li>
                <?php if (count($filter->brandsFilter)) : ?>
                <li class="list-group-item">
                    <?= $form->field($filter, "brands")
                        ->checkboxList($filter->brandsFilter, ['uncheck' => false])
                        ->label('Бренды') ?>
                </li>
                <?php endif; ?>
                <?php foreach ($filter->filters as $attrFilter) : ?>
                    <li class="list-group-item">
                    <?= $form->field($filter, "attr[{$attrFilter['id']}]")
                        ->checkboxList($attrFilter['values'], ['uncheck' => false])
                        ->label($attrFilter['name']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="panel-footer">
                <?= Html::a('Сбросить',
                    Url::to('/' . Yii::$app->request->pathInfo),
                    ['class' => 'btn btn-warning',]
                ) ?>
                <?= Html::submitButton('Искать', ['class' => 'btn btn-primary pull-right',]) ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>