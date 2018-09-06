<?php
namespace frontend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\models\Item;
use common\models\Gift;
use common\models\Category;
use yii\base\UserException;
use \yii\helpers\Url;
use yii\data\ActiveDataProvider;

/**
 * Sale controller
 */
class SaleController extends BaseController
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $discountQuery = Item::find()
            ->select(['{{%items}}.id'])
            ->rightJoin('{{%discounts}}', '{{%items.id}} = {{%discounts}}.item_id');

        $giftQuery = Item::find()
            ->select(['{{%items}}.id'])
            ->where(['in', 'id', Gift::find()->select(['item_id'])]);


        $query = Item::find()
            ->where(['in', 'id', $discountQuery->union($giftQuery)])
            ->orderBy(['modified_at' => SORT_DESC]);

        $saleDataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
        ]);

        return $this->render('index', [
            'saleDataProvider' => $saleDataProvider,
        ]);
    }

}
