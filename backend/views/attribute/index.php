<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\DataType;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AttributeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attributes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Attribute', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'code',
            [
                'attribute' => 'dataTypeName',
                'filter' => ArrayHelper::map(
                    DataType::find()->all(),
                    'id',
                    'name'
                ),
            ],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
