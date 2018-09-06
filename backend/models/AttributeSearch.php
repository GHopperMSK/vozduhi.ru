<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Attribute;

/**
 * AttributeSearch represents the model behind the search form of `common\models\Attribute`.
 */
class AttributeSearch extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'data_type_id'], 'integer'],
            [['code'], 'string'],
            [['name', 'dataTypeName'], 'safe'],
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
        $query = Attribute::find()
            ->select('{{%attributes}}.*, {{%data_types}}.name AS dataTypeName')
            ->leftJoin('{{%data_types}}', "{{%attributes}}.{{data_type_id}} = {{%data_types}}.{{id}}");

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
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'code' => [
                    'asc' => ['code' => SORT_ASC],
                    'desc' => ['code' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'dataTypeName' => [
                    'asc' => ['dataTypeName' => SORT_ASC],
                    'desc' => ['dataTypeName' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ],
        ]);

        $query->andFilterWhere(['ilike', '{{%attributes}}.name', $this->name]);
        $query->andFilterWhere(['ilike', '{{%attributes}}.code', $this->code]);
        $query->andFilterWhere(['=', 'data_type_id', $this->dataTypeName]);

        return $dataProvider;
    }
}
