<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\social\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use matacms\social\models\Social;

/**
 * ContentBlockSearch represents the model behind the search form about `matacms\social\models\Social`.
 */
class SocialSearch extends Social {

    public function rules() {
        return [
            [['Id', 'SocialNetwork', 'Response'], 'required'],
            [['SocialNetwork'], 'string', 'max' => 32],
            [['Processed'], 'string', 'max'=>1]
        ];
    }

    public function scenarios() {
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
    public function search($params) {
        $query = Social::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Id' => $this->Id,
        ]);

        $query->andFilterWhere(['like', 'SocialNetwork', $this->SocialNetwork])
            ->andFilterWhere(['like', 'DateCreated', $this->DateCreated])
            ->andFilterWhere(['like', 'Response', $this->Response])
            ->andFilterWhere(['like', 'Processed', $this->Processed]);

        return $dataProvider;
    }
}
