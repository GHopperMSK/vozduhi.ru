<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use common\models\Item;
use common\models\Category;
use common\models\ItemSlug;
use yii\data\ActiveDataProvider;
use \yii\helpers\Url;

/**
 * Item controller
 */
class ItemController extends BaseController
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
    public function actionView($slug)
    {
        $itemSlug = ItemSlug::findSlug($slug);

        if (!$itemSlug) {
            throw new NotFoundHttpException('Запрашиваемый товар не найден!');
        }

        if ($itemSlug->slug === $slug) {
            $item = Item::findOne($itemSlug->item_id);

            $breadcrumbs = [];
            $tree = $item->category->parents()->orderBy([Category::NS_LEFT_ATTRIBUTE => SORT_ASC])->all();
            foreach($tree as $node) {
                // hide root category
                if ($node->name === 'root') {
                    continue;
                }

                $breadcrumbs[] = [
                    'label' => $node->name,
                    'url' => $node->getUrl(),
                ];
            }
            $breadcrumbs[] = [
                'label' => $item->category->name,
                'url' => $item->category->getUrl(),
            ];
            $breadcrumbs[] = [
                'label' => $item->name,
            ];

            $attributesDataProvider = new ActiveDataProvider([
                'query' => $item->category->getCategoryTreeAttributes()
            ]);

            $relationsDataProvider = new ActiveDataProvider([
                'query' => $item->getRelatedItems()
            ]);

            $giftsDataProvider = new ActiveDataProvider([
                'query' => $item->getGifts()
            ]);

            $recommendedDataProvider = new ActiveDataProvider([
                'query' => $item->getRecommendedItems()
            ]);

            return $this->render('view', [
                'item' => $item,
                'breadcrumbs' => $breadcrumbs,
                'attributesDataProvider' => $attributesDataProvider,
                'relationsDataProvider' => $relationsDataProvider,
                'giftsDataProvider' => $giftsDataProvider,
                'recommendedDataProvider' => $recommendedDataProvider,
            ]);
        } else {
            // slug obsolete, redirect to new one
            Yii::$app->response->redirect(Url::to(['item/view', 'slug' => $itemSlug->slug]), 301)->send();
            Yii::$app->end();
            return;
        }
    }

}
