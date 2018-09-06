<?php
namespace frontend\controllers;

use Yii;
use \yii\helpers\Url;
use common\models\Item;
use common\models\SessionCart;

/**
 * Cart controller
 */
class CartController extends BaseController
{
    public function actionAdd($item_id)
    {
        $item = Item::findOne(['id' => $item_id]);
        // TODO: 404 if there isn't $item

        $sessionCart = new SessionCart();
        $sessionCart->setSession(Yii::$app->session);
        $sessionCart->addItem($item);

        Yii::$app->session->addFlash("success", "Товар успешно добавлен в корзину.");

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    public function actionRemove($item_id)
    {
        $sessionCart = new SessionCart();
        $sessionCart->setSession(Yii::$app->session);
        $sessionCart->removeItem($item_id);

        Yii::$app->session->addFlash("success", "Товар успешно удален из корзины.");

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    public function actionPurge()
    {
        $sessionCart = new SessionCart();
        $sessionCart->setSession(Yii::$app->session);
        $sessionCart->purge();

        Yii::$app->session->addFlash("success", "Корзина очищена.");

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

}
