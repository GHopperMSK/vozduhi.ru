<?php

namespace backend\controllers;

use Yii;
use common\models\Brand;
use backend\models\BrandSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\models\BrandSlug;
use yii\base\UserException;

/**
 * BrandController implements the CRUD actions for Brand model.
 */
class BrandController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Brand models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Brand model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Brand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Brand();

        $model->slug->load(Yii::$app->request->post());

        if ($model->load(Yii::$app->request->post())) {

            $model->uploadedFile = UploadedFile::getInstance($model, 'uploadedFile');

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // load ItemSlug model
                    $model->slug->brand_id = $model->id;
                    if (!$model->slug->validate()) {
                        throw new UserException('Slug model error!');
                    }

                    if (!$model->slug->save(false)) {
                        throw new UserException('Slug save error!');
                    }

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } catch (\Exception $exc) {
                $transaction->rollBack();
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $transaction->rollBack();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Brand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->slug->load(Yii::$app->request->post());

        if ($model->load(Yii::$app->request->post())) {

            $model->uploadedFile = UploadedFile::getInstance($model, 'uploadedFile');

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // load ItemSlug model
                    if (!$model->slug->validate()) {
                        throw new UserException('Current slug error!');
                    }

                    if ($model->slug->isNew()) {
                        $newSlug = new BrandSlug();
                        $newSlug->brand_id = $model->slug->brand_id;
                        $newSlug->slug = $model->slug->slug;
                        if (!$newSlug->save()) {
                            throw new UserException('New slug error!');
                        }
                    }

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } catch (\Exception $exc) {
                $transaction->rollBack();
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            $transaction->rollBack();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Brand model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        BrandSlug::purgeItem($id);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Brand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Brand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Brand::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
