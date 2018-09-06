<?php
namespace frontend\controllers;

use Yii;
use frontend\models\ItemSearch;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

/**
 * Main controller
 */
class MainController extends BaseController
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

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $itemSearch = new ItemSearch();
        $lastItemsDataProvider = $itemSearch->lastItemsSearch(Yii::$app->request->queryParams);

        $recommendedItemsDataProvider = $itemSearch->recommendedItemsSearch(Yii::$app->request->queryParams);

        return $this->render('index', [
            'lastItemsDataProvider' => $lastItemsDataProvider,
            'recommendedItemsDataProvider' => $recommendedItemsDataProvider,
        ]);

    }

}
