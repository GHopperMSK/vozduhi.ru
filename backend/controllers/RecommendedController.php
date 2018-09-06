<?php

namespace backend\controllers;

use Yii;
use common\models\Recommended;
use backend\models\RecommendedSearch;
use yii\base\UserException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\rootRecommentedItemSearch;

/**
 * RecommendedController implements the CRUD actions for Recommended model.
 */
class RecommendedController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Recommended models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RecommendedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $itemModel = new rootRecommentedItemSearch();
        $itemDataProvider = $itemModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'itemModel' => $itemModel,
            'itemDataProvider' => $itemDataProvider,
        ]);
    }

    public function actionAdd($id)
    {
        $model = new Recommended();

        $model->recommended_item_id = $id;

        if (!$model->save()) {
            throw new UserException('Can\'t add Item to recommended!');
        }

        return $this->redirect(['index']);
    }


    /**
     * Deletes an existing Recommended model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Recommended model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Recommended the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Recommended::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
