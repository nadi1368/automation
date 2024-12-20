<?php

namespace hesabro\automation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class AuWorkFlowSearch
 * @package hesabro\automation\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class AuWorkFlowSearch extends AuWorkFlow
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'letter_type', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id'], 'integer'],
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
        $query = AuWorkFlow::find();

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
            'letter_type' => $this->letter_type,
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
