<?php

namespace backend\controllers;

use backend\models\AttributeSearch;
use Yii;
use common\models\Category;
use backend\models\CategorySearch;
use common\models\Attribute;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use common\models\CategorySlug;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'up', 'down'],
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Moves node upstair
     * @param $id
     * @return \yii\web\Response
     */
    public function actionUp($id)
    {
        $cur = Category::findOne(['id' => $id]);
        if ($prev = $cur->prev()->one()) {
            $cur->insertBefore($prev);
        }

        return $this->redirect(['index']);
    }

    /**
     * Moves node downstair
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDown($id)
    {
        $cur = Category::findOne(['id' => $id]);
        if ($next = $cur->next()->one()) {
            $cur->insertAfter($next);
        }

        return $this->redirect(['index']);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $attributesDataProvider = new ActiveDataProvider([
            'query' => $model->getCategoryAttributes(),
        ]);

        return $this->render('view', [
            'model' => $model,
            'attributesDataProvider' => $attributesDataProvider,
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($model->formParent) {
                    $parent = Category::findOne(['id' => $model->formParent]);
                    if (!$parent) {
                        $model->addError('formParent', 'Can\'t find parent');
                        return $this->render('create', [
                            'model' => $model,
                        ]);
                    }
                    $model->appendTo($parent);
                } else {
                    if (Category::find()->roots()->all()) {
                        $model->addError('formParent', 'Only one root element can be created');
                        return $this->render('create', [
                            'model' => $model,
                        ]);
                    }
                    $model->makeRoot();
                }

                if (!$model->id) {
                    throw \Exception('Error');
                }

                // load ItemSlug model
                $categorySlug = $model->slug;
                if (!$categorySlug->load(Yii::$app->request->post()) || !$categorySlug->validate()) {
                    throw new UserException('Slug model error!');
                }

                if (!$categorySlug->save(false)) {
                    throw new UserException('Slug save error!');
                }

                // add attributes
                foreach (Yii::$app->request->post('Attribute', []) as $key => $attrId) {
                    $attribute = Attribute::findOne(['id' => $attrId]);
                    $model->link('categoryAttributes', $attribute, ['pos' => $key]);
                }

                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } catch(\Exception $exc) {
                $transaction->rollBack();

                $attributesSearchModel = new AttributeSearch();
                $attributesDataProvider = $attributesSearchModel->search(Yii::$app->request->queryParams);

                return $this->render('create', [
                    'model' => $model,
                    'attributesSearchModel' => $attributesSearchModel,
                    'attributesDataProvider' => $attributesDataProvider,
                ]);
            }

        }

        $attributesSearchModel = new AttributeSearch();
        $attributesDataProvider = $attributesSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('create', [
            'model' => $model,
            'attributesSearchModel' => $attributesSearchModel,
            'attributesDataProvider' => $attributesDataProvider,
        ]);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($model->formParent == $model->getOldAttribute('formParent')) {
                    $model->save();
                } else {
                    if ($model->formParent == $model->id) {
                        $model->addError('formParent', 'Node can\'t be parent of itself!');
                        return $this->render('update', [
                            'model' => $model,
                        ]);
                    }
                    $model->appendTo($this->findModel($model->formParent));
                }

                // load ItemSlug model
                $categorySlug = $model->slug;
                if (!$categorySlug->load(Yii::$app->request->post()) || !$categorySlug->validate()) {
                    throw new UserException('Current slug error!');
                }

                if ($categorySlug->isNew()) {
                    $newSlug = new CategorySlug();
                    $newSlug->category_id = $categorySlug->category_id;
                    $newSlug->slug = $categorySlug->slug;
                    if (!$newSlug->save()) {
                        throw new UserException('New slug error!');
                    }
                }

                // update attributes
                $oldCategoryAttributesIds = [];
                foreach ($model->categoryAttributes as $categoryAttribute) {
                    $oldCategoryAttributesIds[] = $categoryAttribute->id;
                }
                $newCategoryAttributesIds = [];
                foreach (Yii::$app->request->post('Attribute', []) as $categoryAttribute) {
                    $newCategoryAttributesIds[] = (int)$categoryAttribute['id'];
                }

                // link new attributes
                foreach (array_diff($newCategoryAttributesIds, $oldCategoryAttributesIds) as $key => $attrId) {
                    $attribute = Attribute::findOne(['id' => $attrId]);
                    $model->link('categoryAttributes', $attribute, ['pos' => $key]);
                }

                // unlink unused attributes
                foreach (array_diff($oldCategoryAttributesIds, $newCategoryAttributesIds) as $attrId) {
                    $attribute = Attribute::findOne(['id' => $attrId]);
                    $attribute->purgeValues();
                    $model->unlink('categoryAttributes', $attribute, true);
                }

                // update position of the attributes
                $dbConnection = Yii::$app->db;
                foreach (array_intersect($newCategoryAttributesIds, $oldCategoryAttributesIds) as $key => $attrId) {
                    $dbConnection->createCommand("
                        UPDATE {{%categories_attributes}}
                        SET {{pos}} = :pos
                        WHERE {{category_id}} = :category_id
                        AND {{attribute_id}} = :attribute_id
                    ", [
                        ':pos' => $key,
                        ':category_id' => $model->id,
                        ':attribute_id' => $attrId,
                    ])->execute();
                }

                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (\Exception $exc) {
                $transaction->rollBack();

                $attributesSearchModel = new AttributeSearch();
                $attributesDataProvider = $attributesSearchModel->search(Yii::$app->request->queryParams);

                return $this->render('update', [
                    'model' => $model,
                    'attributesSearchModel' => $attributesSearchModel,
                    'attributesDataProvider' => $attributesDataProvider,
                ]);
            }

        }

        $attributesSearchModel = new AttributeSearch();
        $attributesDataProvider = $attributesSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('update', [
            'model' => $model,
            'attributesSearchModel' => $attributesSearchModel,
            'attributesDataProvider' => $attributesDataProvider,
        ]);
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $menu = Category::findOne(['id' => $id]);

        // unlink Attributes and Values
        foreach($menu->categoryAttributes as $attribute) {
            $attribute = Attribute::findOne(['id' => $attribute->id]);
            $attribute->purgeValues();
            $menu->unlink('categoryAttributes', $attribute, true);
        }

        $menu->deleteWithChildren();

        CategorySlug::purgeItem($id);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
