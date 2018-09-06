<?php
namespace frontend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use frontend\models\ItemSearch;
use common\models\Category;
use yii\base\UserException;
use \yii\helpers\Url;
use yii\data\ActiveDataProvider;
use common\models\Brand;
use common\models\BrandSlug;
use frontend\models\Filter;

/**
 * Brand controller
 */
class BrandController extends BaseController
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
        $alphabet = (new \yii\db\Query())
            ->select(['substring(name, 0, 2) as word'])
            ->distinct()
            ->from('{{%brands}}')
            ->orderBy('word')
            ->column();

        $brandDataProvider = new ActiveDataProvider([
            'query' => Brand::find(),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
        ]);

        return $this->render('index', [
            'alphabet' => $alphabet,
            'brandDataProvider' => $brandDataProvider,
        ]);
    }

    public function actionView($slug)
    {
        $brandSlug = BrandSlug::findSlug($slug);

        if (!$brandSlug) {
            throw new NotFoundHttpException('Запрашиваемый бренд не найден!');
        }

        if ($brandSlug->slug === $slug) {
            $brand = $brandSlug->brand;

            $filter = new Filter();
            $filter->load(Yii::$app->request->get());
            if (!$filter->validate()) {
                throw new UserException('Filter values error!');
            }
            $filter->getBrandFilters($brand);

            $itemSearch = new ItemSearch();
            $itemDataProvider = $itemSearch->brandSearch($filter, Yii::$app->request->queryParams);

            return $this->render('view', [
                'filter' => $filter,
                'brand' => $brand,
                'itemDataProvider' => $itemDataProvider,
            ]);
        } else {
            // slug obsolete, redirect to new one
            Yii::$app->response
                ->redirect(Url::to(['brand/view', 'slug' => $brandSlug->slug]), 301)
                ->send();
            Yii::$app->end();
            return;
        }
    }

}
