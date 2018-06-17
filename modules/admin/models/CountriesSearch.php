<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Countries;

/**
 * CountriesSearch represents the model behind the search form about `app\models\Countries`.
 */
class CountriesSearch extends Countries
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'code_iso', 'dc'], 'integer'],
            [['title_ru', 'title_ua', 'title_be', 'title_en', 'title_es', 'title_pt', 'title_de', 'title_fr', 'title_it', 'title_po', 'title_ja', 'title_lt', 'title_lv', 'title_cz', 'title_zh', 'title_he', 'code_alpha2', 'code_alpha3'], 'safe'],
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
        $query = Countries::find();

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
            'code_iso' => $this->code_iso,
            'dc' => $this->dc,
        ]);

        $query->andFilterWhere(['like', 'title_ru', $this->title_ru])
            ->andFilterWhere(['like', 'title_ua', $this->title_ua])
            ->andFilterWhere(['like', 'title_be', $this->title_be])
            ->andFilterWhere(['like', 'title_en', $this->title_en])
            ->andFilterWhere(['like', 'title_es', $this->title_es])
            ->andFilterWhere(['like', 'title_pt', $this->title_pt])
            ->andFilterWhere(['like', 'title_de', $this->title_de])
            ->andFilterWhere(['like', 'title_fr', $this->title_fr])
            ->andFilterWhere(['like', 'title_it', $this->title_it])
            ->andFilterWhere(['like', 'title_po', $this->title_po])
            ->andFilterWhere(['like', 'title_ja', $this->title_ja])
            ->andFilterWhere(['like', 'title_lt', $this->title_lt])
            ->andFilterWhere(['like', 'title_lv', $this->title_lv])
            ->andFilterWhere(['like', 'title_cz', $this->title_cz])
            ->andFilterWhere(['like', 'title_zh', $this->title_zh])
            ->andFilterWhere(['like', 'title_he', $this->title_he])
            ->andFilterWhere(['like', 'code_alpha2', $this->code_alpha2])
            ->andFilterWhere(['like', 'code_alpha3', $this->code_alpha3])
            ->andFilterWhere(['like', 'flag', $this->flag]);

        return $dataProvider;
    }
}
