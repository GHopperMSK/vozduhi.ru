<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Item;

/**
 * ItemSearch represents the model behind the search form of `common\models\Item`.
 */
class ItemSearch extends Item
{
    public $brandName;
    public $categoryTitle;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'category_id'], 'integer'],
            [['article', 'name', 'active', 'brandName', 'categoryTitle'], 'safe'],
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
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Item::find()
            ->select('{{%items}}.*')
            ->joinWith(['brand', 'category']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'modified_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->attributes['brandName'] = [
            'asc' => ['{{%brands}}.name' => SORT_ASC],
            'desc' => ['{{%brands}}.name' => SORT_DESC],
            'default' => SORT_ASC
        ];
        $dataProvider->sort->attributes['categoryTitle'] = [
            'asc' => ['{{%categories}}.name' => SORT_ASC],
            'desc' => ['{{%categories}}.name' => SORT_DESC],
            'default' => SORT_ASC
        ];

        $query->andFilterWhere(['ilike', '{{%items}}.name', $this->name])
            ->andFilterWhere(['=', '{{%items}}.article', $this->article])
            ->andFilterWhere(['=', '{{%items}}.active', $this->active])
            ->andFilterWhere(['ilike', '{{%brands}}.name', $this->brandName])
            ->andFilterWhere(['ilike', '{{%categories}}.name', $this->categoryTitle]);

        return $dataProvider;
    }
}
