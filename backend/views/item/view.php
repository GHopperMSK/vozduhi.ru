<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\ItemImage;

/* @var $this yii\web\View */
/* @var $model common\models\Item */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Brand',
                'value' => $model->brand ? $model->brand->name : null
            ],
            [
                'label' => 'Category',
                'value' => $model->category ? $model->category->title : null
            ],
            'name',
            [
                'label' => 'Slug',
                'value' => $model->slug ? $model->slug->slug : null,
            ],
            'description:ntext',
            'price',
            [
                'label' => 'Discount',
                'value' => $model->discount ? $model->discount->price : null,
            ],
            'modified_at:datetime'
        ],
    ]) ?>

    <h2>Images</h2>
    <?= GridView::widget([
        'dataProvider' => $imagesDataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'alt',
            [
                'label' => 'Image',
                'format' => 'html',
                'value' => function(ItemImage $img) use ($model) {
                    return Html::img($img->getUrl(['width' => 100, 'height' => 100]), [
                        'alt' => $img->alt,
                        'width' => '100px'
                    ]);
                },
            ],

        ],
    ]); ?>

    <h2>Attributes</h2>
    <?= GridView::widget([
        'dataProvider' => $attributesDataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
                'label' => 'Attribute name',
            ],
            [
                'label' => 'Attribute value',
                'format' => 'raw',
                'value' => function($attr) use ($model) {
                    return str_replace("\n", ', ', $attr->getValueByItem($model));
                }
            ],

        ],
    ]); ?>

    <h2>Relations</h2>
    <?= GridView::widget([
        'dataProvider' => $relationsDataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                    'attribute' => 'r20.name'
            ],

        ],
    ]); ?>

    <h2>Recommended</h2>
    <?= GridView::widget([
        'dataProvider' => $recommendedDataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'recommendedItem.name',
            ],

        ],
    ]); ?>

    <h2>Gifts</h2>
    <?= GridView::widget([
        'dataProvider' => $giftsDataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'itemGift.name',
            ],

        ],
    ]); ?>

</div>