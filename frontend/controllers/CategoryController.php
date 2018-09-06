<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use frontend\models\ItemSearch;
use common\models\Category;
use frontend\models\Filter;
use yii\base\UserException;
use common\models\CategorySlug;
use \yii\helpers\Url;

/**
 * Category controller
 */
class CategoryController extends BaseController
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
     * Display Items from the category
     * @param $id
     * @return string
     */
    public function actionView($slug)
    {
        $categorySlug = CategorySlug::findSlug($slug);

        if (!$categorySlug) {
            throw new NotFoundHttpException('Запрашиваемый товар не найден!');
        }

        if ($categorySlug->slug === $slug) {
            $category = Category::findOne($categorySlug->category_id);

            $filter = new Filter();
            $filter->load(Yii::$app->request->get());
            if (!$filter->validate()) {
                throw new UserException('Filter values error!');
            }
            $filter->getFilters($category);

            $itemSearch = new ItemSearch();
            $itemDataProvider = $itemSearch->categorySearch($filter, Yii::$app->request->queryParams);

            return $this->render('view', [
                'filter' => $filter,
                'itemDataProvider' => $itemDataProvider,
            ]);
        } else {
            // slug obsolete, redirect to new one
            Yii::$app->response
                ->redirect(Url::to(['category/view', 'slug' => $categorySlug->slug]), 301)
                ->send();
            Yii::$app->end();
            return;
        }
    }

}
