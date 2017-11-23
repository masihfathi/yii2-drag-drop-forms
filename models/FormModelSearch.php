<?php
namespace masihfathi\form\models;
use yii\data\ActiveDataProvider;

class FormModelSearch extends FormModel {

    public function rules(){
        return [
            [['form_id', 'maximum', 'answer'], 'integer'],
            [['author', 'title', 'body', 'date_start', 'date_end', 'meta_title', 'url'], 'safe'],
        ];
    }

/**
 * Search and filter result of gridview
 *
 * @param array $param List of params
 * @return ActiveDataProvider 
*/
    public function search($params) {
        $query = FormModel::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([ 'query' => $query, ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'author' => $this->author,
            'form_id' => $this->form_id,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'maximum' => $this->maximum,
            'answer' => $this->answer,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'body', $this->body])
            ->andFilterWhere(['like', 'meta_title', $this->meta_title])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
