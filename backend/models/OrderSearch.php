<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;

/**
 * OrderSearch represents the model behind the search form of `common\models\Order`.
 */
class OrderSearch extends Order
{
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status_id'], 'integer'],
            [['name', 'tel', 'status'], 'safe'],
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
        $query = Order::find()
            ->joinWith('status')
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

        $dataProvider->sort->attributes['status'] = [
            'asc' => ['{{%order_status}}.name' => SORT_ASC],
            'desc' => ['{{%order_status}}.name' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            'status_id' => $this->status_id,
        ]);

        $query->andFilterWhere([
            'status_id' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'tel', $this->tel]);

        return $dataProvider;
    }
}
