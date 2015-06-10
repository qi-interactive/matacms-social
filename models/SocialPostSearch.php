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
use matacms\social\models\SocialPost;

/**
 * ContentBlockSearch represents the model behind the search form about `matacms\social\models\SocialPost`.
 */
class SocialPostSearch extends SocialPost {

    public function rules() {
        return [
            [['Id', 'Author'], 'string', 'max' => 128],
            [['SocialNetwork'], 'string', 'max' => 32],
            [['Text', 'Author', 'SocialNetwork', 'PublicationDate', 'Media', 'URI'], 'safe']
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
        $query = SocialPost::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (\Yii::$app->getRequest()->get("id"))
            $query->andWhere(["SocialNetwork" => \Yii::$app->getRequest()->get("id")]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Id' => $this->Id,
        ]);

        $query->orFilterWhere(['like', 'Text', $this->Text])
            ->orFilterWhere(['like', 'PublicationDate', $this->PublicationDate]);

        return $dataProvider;
    }
}
