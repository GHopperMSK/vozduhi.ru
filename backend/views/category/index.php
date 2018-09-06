<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{up} {down} {view} {update} {delete}',
                'buttons' => [
                    'up' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-up"></span>',
                            $url,
                            [
                                'title' => 'Up',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                    'down' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-arrow-down"></span>',
                            $url,
                            [
                                'title' => 'Down',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>

</div>
