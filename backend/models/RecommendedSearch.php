<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Recommended;

/**
 * RecommendedSearch represents the model behind the search form of `common\models\Recommended`.
 */
class RecommendedSearch extends Recommended
{
    public $itemName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'item_id', 'recommended_item_id'], 'integer'],
            [['itemName'], 'string', 'max' => 255],

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
        $query = RecommendedSearch::find()
            ->select('{{%recommended}}.*, {{%items}}.name as itemName')
            ->leftJoin('items', 'recommended.recommended_item_id = items.id')
            ->where(['item_id' => null])
            ->orderBy(['items.modified_at' => SORT_DESC]);

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
                'itemName' => [
                    'asc' => ['{{%items}}.name' => SORT_ASC],
                    'desc' => ['{{%items}}.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ],
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'item_id' => $this->item_id,
            'recommended_item_id' => $this->recommended_item_id,
        ])
        ->andFilterWhere(['ilike', '{{%items}}.name', $this->itemName]);

        return $dataProvider;
    }
}
