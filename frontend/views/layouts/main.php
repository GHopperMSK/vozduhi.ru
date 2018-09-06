<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?= $this->render('@frontend/views/layouts/_menu.php', [
    'cart' => $this->params['sessionCart'],
    'menuItems' => $this->params['categories'],
]) ?>

<div class="wrap">
    <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label' => '<span class="glyphicon glyphicon-home"></span>',
                'url' => Yii::$app->homeUrl,
                'encode' => false,
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer>
    <div class="city_silhouette">
    </div>
    <div class="container">
        <div class="row">

            <div class="col-md-3 hidden-sm hidden-xs">
                <div class="logo"><a href=""><img src="/images/logo.png" title="vozduhi" alt="vozduhi" class="img-responsive"></a></div>
            </div>

            <div class="col-md-3 col-sm-4 col-xs-12">
                <ul class="list-unstyled">
                    <li><a href="/about_us">О нас</a></li>
                    <li><a href="/delivery">Доставка</a></li>
                    <li><a href="/terms">Способы оплаты</a></li>
                    <li><a href="/privacy">Конфиденциальность</a></li>
                    <li><a href="/index.php?route=account/return/add">Возврат товара</a></li>
                    <li><a href="/index.php?route=information/sitemap">Карта сайта</a></li>
                </ul>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-12">
                <ul class="list-unstyled">
                    <li><a href="tel:+74997532101"><i class="fa fa-phone"></i> +7 (499) 753-21-01</a></li>
                    <li class="last_in_group"><a href="tel:+79639615884"><i class="fa fa-phone"></i> +7 (963) 961-58-84</a></li>
                    <li class="last_in_group"><a href="mailto:info@vozduhi.ru"><i class="fa fa-envelope"></i> info@vozduhi.ru</a></li>
                    <li>ПН-ПТ 10:00-19:00</li>
                    <li>СБ-ВС 11:00-18:00</li>
                </ul>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-12">
                <div class="footer_icons_grp">
                    <a href="https://api.whatsapp.com/send?phone=79639615884"><img src="/images/whats_up.png" alt=""></a>
                    <a href="tg://user?id=79639615884"><img src="/images/telegram.png" alt=""></a>
                    <a href="https://www.facebook.com/vozduhi.ru/"><img src="/images/fb.png" alt=""></a>
                    <a href="https://vk.com/club158086489"><img src="/images/vk.png" alt=""></a>
                    <a href="https://www.instagram.com/vozduhi/"><img src="/images/insta.png" alt=""></a>
                    <a href="viber://add?number=79639615884"><img src="/images/viber.png" alt=""></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
