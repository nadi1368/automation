<?php

namespace hesabro\automation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AuClientGroupSearch represents the model behind the search form of `backend\modules\automation\models\AuClientGroup`.
 */
class AuClientGroupSearch extends AuClientGroup
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id'], 'integer'],
            [['title', 'additional_data'], 'safe'],
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
        $query = AuClientGroup::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'deleted_at' => $this->deleted_at,
            'slave_id' => $this->slave_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'additional_data', $this->additional_data]);

        return $dataProvider;
    }
}
