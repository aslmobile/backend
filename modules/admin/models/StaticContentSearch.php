<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StaticContent;

/**
 * StaticContentSearch represents the model behind the search form about `app\models\StaticContent`.
 */
class StaticContentSearch extends StaticContent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['fan_title', 'fan_title_color', 'fan_icon', 'fan_image', 'fan_tooltip', 'crush_tooltip', 'verified_tooltip', 'unique_code_tooltip'], 'safe'],
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
        $query = StaticContent::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [
				'pageSize' => 50,
			],
			'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'fan_title', $this->fan_title])
            ->andFilterWhere(['like', 'fan_title_color', $this->fan_title_color])
            ->andFilterWhere(['like', 'fan_icon', $this->fan_icon])
            ->andFilterWhere(['like', 'fan_image', $this->fan_image])
            ->andFilterWhere(['like', 'fan_tooltip', $this->fan_tooltip])
            ->andFilterWhere(['like', 'crush_tooltip', $this->crush_tooltip])
            ->andFilterWhere(['like', 'verified_tooltip', $this->verified_tooltip])
            ->andFilterWhere(['like', 'unique_code_tooltip', $this->unique_code_tooltip]);

        return $dataProvider;
    }
}
