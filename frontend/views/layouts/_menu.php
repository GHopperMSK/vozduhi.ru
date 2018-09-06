<?php
/**
 * Top menu
 */

/* @var $this yii\web\View */
/* @var $cart common\models\SessionCart */
/* @var $menuItems array */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use app\assets\NavBarAsset;

NavBarAsset::register($this);

NavBar::begin([
    'brandLabel' => Html::img('@web/images/logo-top.png',
        ['id' => 'logo', 'style' => 'height: 40px; filter: invert(100%);']),
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-inverse navbar-static-top',
    ],
]);

if (count($menuItems)) {
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $menuItems,
    ]);
}
?>
<ul class="nav navbar-nav navbar-right">

    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;
            Корзина (<?= $this->params['sessionCart']->totalCount ?>)<span class="caret"></span>
        </a>
        <ul class="dropdown-menu dropdown-cart shopping-cart" role="menu">
            <?= $this->render('@frontend/views/sessionCart/cart.php', ['cart' => $cart]) ?>
        </ul>
    </li>

</ul>
<form class="navbar-form navbar-right" action="/action_page.php">
    <div class="form-group has-feedback search">
        <input type="text" class="form-control" placeholder="Поиск" />
        <i class="glyphicon glyphicon-search form-control-feedback"></i>
    </div>
</form>

<?php NavBar::end(); ?>
