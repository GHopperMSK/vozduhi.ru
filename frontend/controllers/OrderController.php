<?php
namespace frontend\controllers;

use Yii;
use \yii\helpers\Url;
use common\models\Order;
use common\models\OrderItems;
use common\models\SessionCart;
use yii\base\UserException;

/**
 * Order controller
 */
class OrderController extends BaseController
{
    public function actionView($id)
    {
        $order = Order::findOne(['id' => $id]);

        return $this->render('view', [
            'order' => $order,
        ]);
    }

    public function actionCreate()
    {
        $sessionCart = new SessionCart();
        $sessionCart->setSession(Yii::$app->session);

        $order = new Order();

        if ($order->load(Yii::$app->request->post()) && $order->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($order->save(false)) {

                    // load OrderItems models
                    $orderItems = $this->loadOrderItemsModels($order->id);
                    foreach ($orderItems as $item) {
                        $item->save(false);
                    }

                    $transaction->commit();

                    $session = Yii::$app->session;
                    $session->remove('cart');

                    $session->addFlash("success", "Заказ успешно создан.");

                    return $this->redirect(['view', 'id' => $order->id]);
                }
            } catch (\Exception $exc) {
                $transaction->rollBack();

                $order->addError('*', $exc->getMessage());

                return $this->render('form', [
                    'order' => $order,
                    'sessionCart' => $sessionCart,
                ]);
            }
            $transaction->rollBack();
        }

        return $this->render('form', [
            'order' => $order,
            'sessionCart' => $sessionCart,
        ]);
    }

    /**
     * Loads OrderItems models from $_POST
     * @return OrderItems[]
     * @throws UserException
     */
    private function loadOrderItemsModels($orderId)
    {
        $items = [];
        foreach(Yii::$app->request->post('Cart', []) as $item) {
            $cart = new OrderItems();
            $cart->order_id = $orderId;
            $items[] = $cart;
        }

        if (count($items)) {
            // populate Cart::item_id && Cart::count attributes
            $isLoad = OrderItems::loadMultiple(
                $items,
                Yii::$app->request->post(),
                'Cart'
            );

            if (!$isLoad || !OrderItems::validateMultiple($items)) {
                throw new UserException('Cart value error!');
            }
        }

        return $items;
    }

}
