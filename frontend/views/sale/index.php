<?php
use yii\widgets\ListView;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $saleDataProvider
 */

/**
 * Number of columns in the row
 * @var integer
 */
$colsCount = 4;
?>

<?= ListView::widget([
    'dataProvider' => $saleDataProvider,
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

<?php if ($saleDataProvider->count % $colsCount !== 0) : ?>
    </div>
<?php endif; ?>

<?php if ($saleDataProvider->count) : ?>
    <?= ListView::widget([
        'dataProvider' => $saleDataProvider,
        'layout' => '<div class="pull-right">{summary}</div><div class="clearfix"></div>'
            . '<div class="text-center">{pager}</div>',
        'options' => ['class' => 'items-footer'],
        'summary' => 'Показано <b>{begin}-{end}</b> из <b>{totalCount}</b>',
    ]); ?>
<?php endif; ?>

