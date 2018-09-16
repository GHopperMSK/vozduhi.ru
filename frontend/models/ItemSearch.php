<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Item;
use common\models\Category;
use common\models\Attribute;
use common\models\Recommended;

/**
 * ItemSearch represents the model behind the search form of `common\models\Item`.
 */
class ItemSearch extends Item
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param Filter $filter
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function categorySearch(Filter $filter, $params)
    {
        $viewQuery = (new \yii\db\Query())
            ->select(['id'])
            ->distinct()
            ->from('{{%items_value}}')
            ->where([
                'in',
                '{{%items}}.category_id',
                Category::find()
                    ->select('id')
                    ->where([
                        '>=',
                        Category::NS_LEFT_ATTRIBUTE,
                        Category::find()
                            ->where(['id' => $filter->categoryId])
                            ->select(Category::NS_LEFT_ATTRIBUTE)
                    ])
                    ->andWhere([
                        '<=',
                        Category::NS_RIGHT_ATTRIBUTE,
                        Category::find()
                            ->where(['id' => $filter->categoryId])
                            ->select(Category::NS_RIGHT_ATTRIBUTE)
                    ])
            ]);


        $totalCond = [];
        $processedValues = [];
        if (is_array($filter->attr)) {
            foreach ($filter->attr as $attrValue) {
                if (!is_array($attrValue)) {
                    continue;
                }
                foreach ($attrValue as $value) {
                    if (!empty($value)) {
                        list($attributeId, $valueId) = explode('_', $value);
                        $processedValues[$attributeId][$valueId] = true;
                    }
                }

            }
        }

        foreach ($processedValues as $attributeId => $values) {
            $attrCond = [];
            $attribute = Attribute::findOne(['id' => $attributeId]);
            foreach ($values as $valueId => $value) {
                $attrCond[] = [
                    'value' => $attribute->getValueById($valueId)->value
                ];
            }
            if (count($attrCond)) {
                $totalCond[] = [
                    'AND',
                    ['code' => $attribute->code],
                    array_merge(['OR'], $attrCond)
                ];
            }
        }

        if (count($totalCond) > 0) {
            $totalCond = array_merge(['OR'], $totalCond);
            $viewQuery->andFilterWhere($totalCond);
        }

        // add priceStart filter
        if (isset($filter->priceStart)) {
            $viewQuery->andFilterWhere(['>=', 'price', $filter->priceStart]);
        }

        // add priceEnd filter
        if (isset($filter->priceEnd)) {
            $viewQuery->andFilterWhere(['<=', 'price', $filter->priceEnd]);
        }

        // add brands filter
        if (isset($filter->brands) && (is_array($filter->brands))) {
            $viewQuery->andFilterWhere(['in', 'brand_id', $filter->brands]);
        }

        $query = Item::find()->where(['in', 'id', $viewQuery])->andWhere(['=', 'active', true]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'modified_at' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);

//        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param Filter $filter
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function brandSearch(Filter $filter, $params)
    {
        $viewQuery = (new \yii\db\Query())
            ->select(['id'])
            ->distinct()
            ->from('{{%items_value}}')
            ->where([
                '=',
                'brand_id',
                $filter->brandId
            ]);


        $totalCond = [];
        $processedValues = [];
        if (is_array($filter->attr)) {
            foreach ($filter->attr as $attrValue) {
                if (!is_array($attrValue)) {
                    continue;
                }
                foreach ($attrValue as $value) {
                    if (!empty($value)) {
                        list($attributeId, $valueId) = explode('_', $value);
                        $processedValues[$attributeId][$valueId] = true;
                    }
                }

            }
        }

        foreach ($processedValues as $attributeId => $values) {
            $attrCond = [];
            $attribute = Attribute::findOne(['id' => $attributeId]);
            foreach ($values as $valueId => $value) {
                $attrCond[] = [
                    'value' => $attribute->getValueById($valueId)->value
                ];
            }
            if (count($attrCond)) {
                $totalCond[] = [
                    'AND',
                    ['code' => $attribute->code],
                    array_merge(['OR'], $attrCond)
                ];
            }
        }

        if (count($totalCond) > 0) {
            $totalCond = array_merge(['OR'], $totalCond);
            $viewQuery->andFilterWhere($totalCond);
        }

        // add priceStart filter
        if (isset($filter->priceStart)) {
            $viewQuery->andFilterWhere(['>=', 'price', $filter->priceStart]);
        }

        // add priceEnd filter
        if (isset($filter->priceEnd)) {
            $viewQuery->andFilterWhere(['<=', 'price', $filter->priceEnd]);
        }

        $query = Item::find()->where(['in', 'id', $viewQuery])->andWhere(['=', 'active', true]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'modified_at' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);

//        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @return ActiveDataProvider
     */
    public function lastItemsSearch()
    {
        $query = Item::find()->andWhere(['=', 'active', true])
            ->limit(12);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'modified_at' => SORT_DESC,
                ]
            ],
            'pagination' => false,
        ]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @return ActiveDataProvider
     */
    public function recommendedItemsSearch()
    {
        $query = Item::find()
            ->where(
                ['IN', 'id', Recommended::find()
                ->select('recommended_item_id')
                ->where(['item_id' => null])]
            )->andWhere(['=', 'active', true])->limit(12);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'modified_at' => SORT_DESC,
                ]
            ],
            'pagination' => false,
        ]);

        return $dataProvider;
    }

}
