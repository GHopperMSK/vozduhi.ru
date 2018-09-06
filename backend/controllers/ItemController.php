<?php

namespace backend\controllers;

use Yii;
use common\models\Item;
use backend\models\ItemSearch;
use common\models\ItemImage;
use common\models\Relation;
use common\models\Recommended;
use common\models\Attribute;
use common\models\Category;
use yii\base\UserException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use common\models\Value;
use common\models\ItemSlug;
use common\models\Gift;

/**
 * ItemController implements the CRUD actions for Item model.
 */
class ItemController extends Controller
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
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Item model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $imagesDataProvider = new ActiveDataProvider([
            'query' => $model->getImages()->orderBy(['pos' => SORT_ASC]),
        ]);

        $relationsDataProvider = new ActiveDataProvider([
            'query' => Relation::find()
                ->joinWith('r20')
                ->where(['r1' => $model->id])
        ]);

        $recommendedDataProvider = new ActiveDataProvider([
            'query' => Recommended::find()
                ->joinWith('recommendedItem')
                ->where(['item_id' => $model->id])
        ]);

        $attributesDataProvider = new ActiveDataProvider([
            'query' => $model->category->getCategoryTreeAttributes()
        ]);

        $giftsDataProvider = new ActiveDataProvider([
            'query' => $model->getGifts()
        ]);

        return $this->render('view', [
            'model' => $model,
            'relationsDataProvider' => $relationsDataProvider,
            'recommendedDataProvider' => $recommendedDataProvider,
            'imagesDataProvider' => $imagesDataProvider,
            'attributesDataProvider' => $attributesDataProvider,
            'giftsDataProvider' => $giftsDataProvider,
        ]);
    }

    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $item = new Item();

        if ($item->load(Yii::$app->request->post()) && $item->validate()) {

            $item->uploadedFiles = UploadedFile::getInstances($item, 'uploadedFiles');
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($item->save(false)) {
                    // load ItemSlug model
                    $itemSlug = $item->slug;
                    if (!$itemSlug->load(Yii::$app->request->post()) || !$itemSlug->validate()) {
                        throw new UserException('Slug model error!');
                    }

                    if (!$itemSlug->save(false)) {
                        throw new UserException('Slug save error!');
                    }

                    // load Discount model
                    $discount = $item->discount;
                    if (!$discount->load(Yii::$app->request->post())) {
                        throw new UserException('Discount loading error!');
                    }
                    if (!empty($discount->price)) {
                        $discount->save();
                    }

                    // load Gift models
                    $gifts = $this->loadGiftModels($item->id);
                    if (count($gifts)) {
                        foreach ($gifts as $gift) {
                            $gift->save();
                        }
                    }

                    // load Image models
                    $images = $this->loadImageModels($item);

                    if (count($images)) {
                        foreach ($images as $image) {
                            $image->saveImage();
                        }
                    }

                    // load Relation models
                    $relations = $this->loadRelationModels($item->id);
                    foreach ($relations as $relation) {
                        $relation->save(false);
                    }

                    // load Attribute models
                    $attributes = $this->loadAttributeModels();
                    foreach ($attributes as $itemAttribute){
                        $itemAttribute->setValue($item);
                    }

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $item->id]);
                }
            } catch (\Exception $exc) {
                $transaction->rollBack();

                $item->addError('*', $exc->getMessage());

                $searchModel = new ItemSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('create', [
                    'item' => $item,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'availableCategories' => Category::find()->orderBy(['lft' => SORT_ASC])->all(),
                ]);
            }
            $transaction->rollBack();
        }

        $searchModel = new ItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('create', [
            'item' => $item,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'availableCategories' => Category::find()->orderBy(['lft' => SORT_ASC])->all(),
        ]);
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $item = $this->findModel($id);

        if ($item->load(Yii::$app->request->post()) && $item->validate()) {

            $item->uploadedFiles = UploadedFile::getInstances($item, 'uploadedFiles');
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($item->save(false)) {
                    // load ItemSlug model
                    $itemSlug = $item->slug;
                    if (!$itemSlug->load(Yii::$app->request->post()) || !$itemSlug->validate()) {
                        throw new UserException('Current slug error!');
                    }

                    if ($itemSlug->isNew()) {
                        $newSlug = new ItemSlug();
                        $newSlug->item_id = $itemSlug->item_id;
                        $newSlug->slug = $itemSlug->slug;
                        if (!$newSlug->save()) {
                            throw new UserException('New slug error!');
                        }
                    }

                    // load Discount model
                    $discount = $item->discount;
                    $discount->delete();
                    if (!$discount->load(Yii::$app->request->post())) {
                        throw new UserException('Discount loading error!');
                    }
                    if (!empty($discount->price)) {
                        $discount->save();
                    }

                    // load Gift models
                    Gift::purgeItem($item->id);

                    $gifts = $this->loadGiftModels($item->id);
                    if (count($gifts)) {
                        foreach ($gifts as $gift) {
                            $gift->save();
                        }
                    }

                    // load Image models
                    $currentImagesCount = 0;
                    $currentImages = $item->images;
                    foreach ($currentImages as $key => $img) {
                        if (!in_array($img->name, $item->uploadedFilesName)) {
                            $img->delete();
                        } else {
                            // update file pos
                            if ($img->pos !== $key) {
                                $img->pos = $key;
                                $img->update();
                            }

                            // increase uploaded files counter
                            $currentImagesCount++;
                        }
                    }

                    /** @var $images Image[] */
                    $images = $this->loadImageModels($item, $currentImagesCount);
                    foreach($images as $image) {
                        $image->saveImage();
                    }

                    // delete old relations
                    Relation::purgeItem($item->id);

                    // load Relation models
                    /** @var $relations Relation[] */
                    $relations = $this->loadRelationModels($item->id);
                    foreach ($relations as $relation){
                        $relation->save(false);
                    }

                    // delete old recommended
                    Recommended::purgeItem($item->id);

                    // load Relation models
                    /** @var $recommended Recommended[] */
                    $recommended = $this->loadRecommendedModels($item->id);
                    foreach ($recommended as $recommendedItem){
                        $recommendedItem->save(false);
                    }

                    // delete all attribute values
                    Value::purgeItem($item->id);

                    // load Attribute models
                    /** @var $attributes Attribute[] */
                    $attributes = $this->loadAttributeModels();
                    foreach ($attributes as $itemAttribute){
                        $itemAttribute->setValue($item);
                    }

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $item->id]);
                }
            } catch (\Exception $exc) {
                $transaction->rollBack();

                $item->addError('*', $exc->getMessage());

                $searchModel = new ItemSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('update', [
                    'item' => $item,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'availableCategories' => Category::find()->orderBy(['lft' => SORT_ASC])->all(),
                ]);
            }
            $transaction->rollBack();
        }

        $searchModel = new ItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('update', [
            'item' => $item,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'availableCategories' => Category::find()->orderBy(['lft' => SORT_ASC])->all(),
        ]);
    }

    /**
     * Deletes an existing Item model.
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
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($item = Item::findOne($id)) !== null) {
            return $item;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param Item $item
     * @return Image[]
     */
    private function loadImageModels(Item $item, $curIndex = 0)
    {
        $images = [];
        $uploadedImages = (is_array($item->uploadedFiles) && count($item->uploadedFiles))
            ? $item->uploadedFiles
            : [];

        // can't use Image::loadMultiple due to quite difficult images management (delete, update)
        foreach ($uploadedImages as $key => $file) {
            $img = new ItemImage();
            $img->item_id = $item->id;
            $img->pos = $key + $curIndex;
            $img->alt = '';
            $img->uploadedFile = $file;
            $img->uploadedFileName = $item->uploadedFilesName[$curIndex + $key];
            $images[] = $img;
        }

        return $images;
    }

    /**
     * @param $itemId integer
     * @return Relation[]
     */
    private function loadRelationModels($itemId)
    {
        $relations = [];

        foreach(Yii::$app->request->post('Relation', []) as $relation) {
            $rel = new Relation();
            $rel->r1 = $itemId;
            $relations[] = $rel;
        }

        if (count($relations)) {
            $isLoad = Attribute::loadMultiple(
                $relations,
                Yii::$app->request->post(),
                'Relation'
            );

            if (!$isLoad || !Relation::validateMultiple($relations)) {
                throw new UserException('Relation value error!');
            }
        }

        return $relations;
    }

    /**
     * @param $itemId integer
     * @return Gift[] array
     */
    private function loadGiftModels($itemId)
    {
        $gifts = [];

        foreach(Yii::$app->request->post('Gift', []) as $gift) {
            $tmpGift = new Gift();
            $tmpGift->item_id = $itemId;
            $gifts[] = $tmpGift;
        }

        if (count($gifts)) {
            $isLoad = Attribute::loadMultiple(
                $gifts,
                Yii::$app->request->post(),
                'Gift'
            );

            if (!$isLoad || !Gift::validateMultiple($gifts)) {
                throw new UserException('Gift error!');
            }
        }

        return $gifts;
    }

    /**
     * @param $item Item
     * @return Recommended[]
     */
    private function loadRecommendedModels($itemId)
    {
        $recommended = [];

        foreach(Yii::$app->request->post('Recommended', []) as $relation) {
            $item = new Recommended();
            $item->item_id = $itemId;
            $recommended[] = $item;
        }

        if (count($recommended)) {
            $isLoad = Attribute::loadMultiple(
                $recommended,
                Yii::$app->request->post(),
                'Recommended'
            );

            if (!$isLoad || !Recommended::validateMultiple($recommended)) {
                throw new UserException('Relation value error!');
            }
        }

        return $recommended;
    }

    /**
     * Loads Attribute models from $_POST
     * @return Attribute[]
     * @throws UserException
     */
    private function loadAttributeModels()
    {
        $attributes = [];
        foreach(Yii::$app->request->post('Attribute', []) as $key => $attrValue) {
            $attributes[$key] = Attribute::findOne(['id' => $key]);
        }

        if (count($attributes)) {
            // populate Attribute::uploadedValue attributes
            $isLoad = Attribute::loadMultiple(
                $attributes,
                Yii::$app->request->post(),
                'Attribute'
            );

            if (!$isLoad || !Attribute::validateMultiple($attributes)) {
                throw new UserException('Attribute value error!');
            }
        }

        return $attributes;
    }
}
