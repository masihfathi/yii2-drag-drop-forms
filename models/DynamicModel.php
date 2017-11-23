<?php

namespace masihfathi\form\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use masihfathi\form\traits\ViewsHelpersTrait;

class DynamicModel extends ActiveRecord {

    use ViewsHelpersTrait;

    public $items;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        $params = Yii::$app->request->queryParams;
        if (isset($params['form_id'])) {
            $id = (int) $params['form_id'];
        } else {
            throw new \Exception('form id param not found');
        }
        return Yii::$app->controller->module->formDataTable . $id;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        $array = [];
        if (is_array($this->items)) {
            $array[] = [['id'], 'integer'];
            foreach ($this->items as $value) {
                $array[] = [[$value], 'string','max'=>255];
            }
        }
        return $array;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     * @param int $pageSize
     *
     * @return ActiveDataProvider
     */
    public function search(array $params, int $id, int $pageSize = 10) {
        $table = Yii::$app->controller->module->formDataTable . $id;
        $items = array_merge(['id'],$this->items);
        $query = (new Query)->from($table);
        $query->indexBy('id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'db' => Yii::$app->controller->module->db,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'attributes' => $items
        ]);
        $condition = ['and'];
        foreach ($params as $val) {
            if (is_array($val)) {
                foreach ($val as $key => $value) {
                    if (in_array($key, $items)) {
                        $condition[] = ['like', $key, $value];
                    }
                }
            }
        }
        if (isset($condition)) {
            $query->andFilterWhere($condition);
        }
        return $dataProvider;
    }

}
