<?php
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $brand common\models\Brand */

$colsCount = 4;
?>

<?= $this->render('/filter/_category.php', [
    'filter' => $filter,
]) ?>

<?= ListView::widget([
    'dataProvider' => $itemDataProvider,
    'layout' => '{items}',
    'options' => ['class' => 'items-list'],
    'itemOptions'  => ['class' => "col-xs-6 col-sm-6 col-md-3 col-lg-3 col-xl-3"],
    'beforeItem' => function ($model, $key, $index, $widget) use ($colsCount) {
        if ($index % $colsCount === 0) {
            return "<div class='row'>";
        }
    },
    'itemView' => function ($model, $key, $index, $widget) {
        return $this->render('@frontend/views/item/_card.php', ['item' => $model]);
    },
    'afterItem' => function ($model, $key, $index, $widget) use ($colsCount) {
        $content = '';
        if (($index > 0) && ($index % $colsCount === $colsCount - 1)) {
            $content .= "</div>";
        }
        return $content;
    },
]); ?>

<?php if ($itemDataProvider->count % $colsCount !== 0) : ?>
    </div>
<?php endif; ?>

<?php if ($itemDataProvider->count) : ?>
    <?= ListView::widget([
        'dataProvider' => $itemDataProvider,
        'layout' => '<div class="pull-right">{summary}</div><div class="clearfix"></div>'
            . '<div class="text-center">{pager}</div>',
        'options' => ['class' => 'items-footer'],
        'summary' => 'Показано <b>{begin}-{end}</b> из <b>{totalCount}</b>',
    ]); ?>
<?php endif; ?>
