<?php
/**
 * Attribute row within Item`s attribute list
 */

/* @var $this yii\web\View */
/* @var $attribute common\models\Attribute */
/* @var $item common\models\Item */
?>

<p>
    <b><?= $attribute->name ?>:</b> <?= $attribute->getValueByItem($item) ?><br />
</p>