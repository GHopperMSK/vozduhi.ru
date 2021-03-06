<?php
use yii\widgets\ListView;
use yii\helpers\Html;

/**
 * @var $brandDataProvider
 * @var $alphabet
 */
?>

<?php if (count($alphabet)) : ?>

<?php $curLetter = $alphabet[0]; ?>
<div>
Алфавитный указатель:
<?php foreach ($alphabet as $letter) : ?>
    <?= Html::a($letter, '#' . $letter, []) ?>
<?php endforeach; ?>
</div>

<h3 id='<?= $curLetter ?>'><?= $curLetter ?></></h3><div>
<?= ListView::widget([
    'dataProvider' => $brandDataProvider,
    'layout' => '{items}',
    'options' => [
        'tag' => null,
    ],
    'itemOptions' => [
        'class' => 'brand-card',
    ],
    'beforeItem' => function ($model, $key, $index, $widget) use (&$curLetter) {
        if ($curLetter !== strtoupper($model->name[0])) {
            $curLetter = strtoupper($model->name[0]);
            return "</div><h3 id='{$curLetter}'>{$curLetter}</></h3><div>";
        }
    },
    'itemView' => function ($model, $key, $index, $widget) {
        return $this->render('@frontend/views/brand/_brand.php', ['brand' => $model]);
    },
]); ?>
</div>

<?php endif; ?>