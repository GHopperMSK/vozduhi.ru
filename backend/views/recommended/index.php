<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RecommendedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Recommendeds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recommended-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'itemName',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
            ],

        ],
    ]); ?>

    <br />

    <?= GridView::widget([
        'dataProvider' => $itemDataProvider,
        'filterModel' => $itemModel,
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
                'template' => '{add}',
                'buttons' => [
                    'add' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-plus"></span>',
                            $url,
                            [
                                'title' => 'Add',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>
