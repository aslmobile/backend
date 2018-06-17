<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Lang;

/**
 * LangSearch represents the model behind the search form about `app\modules\admin\models\Lang`.
 */
class LangSearch extends Lang
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'default', 'created_at', 'updated_at'], 'integer'],
            [['flag', 'url', 'local', 'name','code'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Lang::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [
				'pageSize' => 50,
			],			
			'sort'=> ['defaultOrder' => ['default' => SORT_DESC]]			
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'default' => $this->default,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'flag', $this->flag])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'local', $this->local])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
