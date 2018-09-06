<?php
use yii\widgets\ListView;

/**
 * Main page template
 */

//use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $cart common\models\Cart */
/* @var $lastItems common\models\Item[] */
/* @var $recommendedItems common\models\Item[] */

/**
 * Amount of data cols on the page
 * @var $colsCount int
 */
$colsCount = 4;

/**
 * The page modules
 * @var $modules array
 */
$modules = [
    [
        'title' => 'Новинки',
        'data' => $lastItemsDataProvider,
    ],
    [
        'title' => 'Рекомендованные',
        'data' => $recommendedItemsDataProvider,
    ],
];

?>

<?php foreach ($modules as $module) : ?>

    <div class="container">
        <h3 class="module-title"><?= $module['title'] ?></h3>

        <?= ListView::widget([
            'dataProvider' => $module['data'],
            'layout' => '{items}',
            'options' => ['class' => 'items-list'],
            'itemOptions'  => ['class' => "col-xs-6 col-sm-6  col-md-3 col-lg-3 col-xl-3"],
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

        <?php
        if ( $module['data']->totalCount % $colsCount != 0 ) {
            echo "</div>";
        }
        ?>

    </div>

<?php endforeach; ?>