<?php
use yii\helpers\Html;
use common\models\Cart;
use yii\widgets\ListView;
use common\models\PlaceholderImage;
use \metalguardian\fotorama\Fotorama;

/* @var $this yii\web\View */
/* @var $item common\models\Item */

$colsCount = 4;
$this->params['breadcrumbs'] = $breadcrumbs;
?>
<div class="row">
    <div class="col-md-6 ">
    <?php if (count($item->images)) {
        $images = [];
        foreach($item->images as $image) {
            $images[] = [
                'full' => $image->getUrl(['width' => 650, 'height' => 650]),
                'img' => $image->getUrl(['width' => 450, 'height' => 450]),
                'thumb' => $image->getUrl(['width' => 160, 'height' => 160]),
            ];
        }
        echo Fotorama::widget(
            [
                'items' => $images,
                'options' => [
                    'nav' => 'thumbs',
                    'allowfullscreen' => 'true',
                    'fit' => 'scaledown',
                ],
                'htmlOptions' => [
                    'class' => 'fotorama pull-right-md',
                ],
            ]
        );
    } else {
        echo Html::img(PlaceholderImage::getUrl(
            ['width' => 450, 'height' => 450]),
            ['alt' => 'нет изображения']);
    } ?>
    </div>
    <div class="col-md-6">
        <h3><?= $item->name ?></h3>

        <p><b>Бренд:</b> <?= $item->brand->name ?></p>

        <p><b>Категория:</b> <?= $item->category->name ?></p>

        <?= ListView::widget([
            'dataProvider' => $attributesDataProvider,
            'layout' => '{items}',
            'itemView' => function ($model, $key, $index, $widget) use ($item) {
                return $this->render('_attribute',[
                    'item' => $item,
                    'attribute' => $model,
                ]);
            },
        ]); ?>

        <p><b>Цена:</b>
        <?php if ($item->discount->price) : ?>
            <?= Html::tag('span', Html::tag('s', $item->price) . ' '
                . Yii::$app->formatter->asCurrency(
                    $item->discount->price,
                    null,
                    [\NumberFormatter::MAX_SIGNIFICANT_DIGITS => 100]
                )
            ) ?>
        <?php else: ?>
            <?= Html::tag('span', Yii::$app->formatter->asCurrency(
                $item->price,
                null,
                [\NumberFormatter::MAX_SIGNIFICANT_DIGITS => 100]
            ))?>
        <?php endif; ?>
        </p>

        <p><b>Описание:</b> <?= $item->description ?></p>

        <p><?= Html::a('В корзину', ['cart/add', 'item_id' => $item->id], ['class' => 'btn btn-primary']) ?></p>

        <?php if ($giftsDataProvider->getTotalCount()) : ?>
        <div class="alert alert-info margin-top-xl">
        <h4>При покупке данного товара вы получаете <b>в подарок</b>:</h4>
        <?= ListView::widget([
            'dataProvider' => $giftsDataProvider,
            'layout' => '{items}',
            'itemView' => function ($model, $key, $index, $widget) use ($item) {
                return $this->render('_gift', ['gift' => $model]);
            },
        ]); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($relationsDataProvider->getTotalCount()) : ?>
    <h4 class="module-title">Похожие предложения:</h4>
    <table class="table table-striped related-items">
    <thead>
    <tr>
        <th width="100px" scope="col"></th>
        <th scope="col">Название</th>
        <th scope="col">Цена</th>
    </tr>
    </thead>
    <tbody>
    <?= ListView::widget([
        'dataProvider' => $relationsDataProvider,
        'layout' => '{items}',
        'itemView' => function ($model, $key, $index, $widget) use ($item) {
            return $this->render('_related.php', ['item' => $model]);
        },
    ]); ?>
    </tbody>
    </table>
<?php endif; ?>

<?php if ($recommendedDataProvider->getTotalCount()) : ?>
<h4 class="module-title">Рекомендованные</h4>
<?= ListView::widget([
    'dataProvider' => $recommendedDataProvider,
    'layout' => '{items}{pager}',
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
<?php
if ($recommendedDataProvider->count % $colsCount !== 0) {
    echo "</div>";
}
?>

<?php endif; ?>
