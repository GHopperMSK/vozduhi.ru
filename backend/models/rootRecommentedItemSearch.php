<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Item;
use common\models\Recommended;

/**
 * ItemSearch represents the model behind the search form of `common\models\Item`.
 */
class rootRecommentedItemSearch extends Item
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
            [['name', 'description', 'brandName', 'categoryTitle'], 'safe'],
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
            ->joinWith(['brand', 'category'])
            ->where(['not in','{{%items}}.id', Recommended::find()->select('recommended_item_id')])
            ->orderBy(['modified_at' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->setSort([
            'attributes' => [
                'brandName' => [
                    'asc' => ['{{%brands}}.name' => SORT_ASC],
                    'desc' => ['{{%brands}}.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'categoryTitle' => [
                    'asc' => ['{{%categories}}.name' => SORT_ASC],
                    'desc' => ['{{%categories}}.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'name' => [
                    'asc' => ['{{%items}}.name' => SORT_ASC],
                    'desc' => ['{{%items}}.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ],
        ]);

        $query->andFilterWhere(['ilike', '{{%items}}.name', $this->name])
            ->andFilterWhere(['ilike', '{{%brands}}.name', $this->brandName])
            ->andFilterWhere(['ilike', '{{%categories}}.name', $this->categoryTitle])
            ->andFilterWhere(['ilike', '{{%items}}.description', $this->description]);

        return $dataProvider;
    }
}
