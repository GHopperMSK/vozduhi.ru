<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $item common\models\Item */

$this->title = 'Update Item: ' . $item->name;
$this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $item->name, 'url' => ['view', 'id' => $item->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'item' => $item,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'availableCategories' => $availableCategories,
    ]) ?>

</div>
